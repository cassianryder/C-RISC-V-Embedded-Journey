#!/usr/bin/env python3
from __future__ import annotations

import argparse
import json
from dataclasses import dataclass
from datetime import date, timedelta
from pathlib import Path
from typing import Dict, List


REPO_ROOT = Path(__file__).resolve().parents[2]
PROBLEMS_DIR = REPO_ROOT / "notes" / "problems"
STATE_FILE = Path(__file__).resolve().with_name("review_state.json")
TAG_FILE = Path(__file__).resolve().with_name("review_tags.json")
TOPIC_PRESETS = {
    "03-structs": ["03-structs", "结构体", "结构体指针", "点和箭头", "值传递", "指针传递", "函数边界"],
    "06-projects": ["06-projects", "项目映射", "状态判断输出", "接口设计", "日志", "CSV", "状态机", "队列"],
    "补码": ["补码", "内存", "信号", "数据表示", "char", "有符号无符号", "模运算", "最高位", "位序", "组成原理"],
    "文件I/O": ["文件I/O", "缓冲输出", "字符输入输出", "输入缓冲", "scanf", "printf", "write", "getchar", "EOF"],
    "文件i/o": ["文件I/O", "缓冲输出", "字符输入输出", "输入缓冲", "scanf", "printf", "write", "getchar", "EOF"],
}


@dataclass
class Card:
    card_id: str
    file_name: str
    source_date: str
    question_heading: str
    question_text: str
    answer_text: str
    closure_text: str
    usage_text: str


def today_str() -> str:
    return date.today().isoformat()


def parse_cards() -> List[Card]:
    cards: List[Card] = []

    for md in sorted(PROBLEMS_DIR.glob("20*.md")):
        if md.name == "problem.md":
            continue

        lines = md.read_text(encoding="utf-8").splitlines()
        current = None
        section = None

        def flush_current() -> None:
            nonlocal current
            if not current:
                return

            question_text = " ".join(current["question"]).strip()
            answer_text = " ".join(current["answer"]).strip()
            closure_text = " ".join(current["closure"]).strip()
            usage_text = " ".join(current["usage"]).strip()

            if question_text:
                cards.append(
                    Card(
                        card_id=f"{md.stem}::{current['heading']}",
                        file_name=md.name,
                        source_date=md.stem[:10],
                        question_heading=current["heading"],
                        question_text=question_text,
                        answer_text=answer_text,
                        closure_text=closure_text,
                        usage_text=usage_text,
                    )
                )
            current = None

        for raw in lines:
            line = raw.strip()

            if line.startswith("### 问题"):
                flush_current()
                current = {
                    "heading": line.replace("### ", "", 1),
                    "question": [],
                    "answer": [],
                    "closure": [],
                    "usage": [],
                }
                section = "question"
                continue

            if not current:
                continue

            if line == "### 解答":
                section = "answer"
                continue
            if line == "### 闭环":
                section = "closure"
                continue
            if line.startswith("### 使用场景"):
                section = "usage"
                continue
            if line.startswith("## 2.") or line.startswith("## 3."):
                flush_current()
                section = None
                continue
            if line.startswith("### "):
                section = None
                continue

            if line.startswith("- "):
                text = line[2:].strip()
            else:
                text = line

            if text and section in {"question", "answer", "closure", "usage"}:
                current[section].append(text)

        flush_current()

    return cards


def load_state() -> Dict:
    if STATE_FILE.exists():
        return json.loads(STATE_FILE.read_text(encoding="utf-8"))
    return {"cards": {}}


def load_tag_registry() -> Dict:
    if TAG_FILE.exists():
        return json.loads(TAG_FILE.read_text(encoding="utf-8"))
    return {"file_defaults": {}, "card_overrides": {}}


def save_state(state: Dict) -> None:
    STATE_FILE.write_text(
        json.dumps(state, ensure_ascii=False, indent=2, sort_keys=True),
        encoding="utf-8",
    )


def resolve_tags(card: Card, registry: Dict) -> List[str]:
    file_defaults = registry.get("file_defaults", {})
    card_overrides = registry.get("card_overrides", {})
    tags = []
    tags.extend(file_defaults.get(card.file_name, []))
    tags.extend(card_overrides.get(card.card_id, []))
    seen = set()
    result = []
    for tag in tags:
        if tag not in seen:
            seen.add(tag)
            result.append(tag)
    return result


def sync_state(cards: List[Card], state: Dict, registry: Dict) -> Dict:
    today = today_str()
    cards_map = state.setdefault("cards", {})

    for card in cards:
        tags = resolve_tags(card, registry)
        if card.card_id not in cards_map:
            cards_map[card.card_id] = {
                "file_name": card.file_name,
                "source_date": card.source_date,
                "question_heading": card.question_heading,
                "question_text": card.question_text,
                "answer_text": card.answer_text,
                "closure_text": card.closure_text,
                "usage_text": card.usage_text,
                "due": today,
                "interval_days": 0,
                "repetitions": 0,
                "ease": 2.5,
                "lapses": 0,
                "last_reviewed": None,
                "last_score": None,
                "tags": tags,
            }
        else:
            cards_map[card.card_id].update(
                {
                    "file_name": card.file_name,
                    "source_date": card.source_date,
                    "question_heading": card.question_heading,
                    "question_text": card.question_text,
                    "answer_text": card.answer_text,
                    "closure_text": card.closure_text,
                    "usage_text": card.usage_text,
                    "tags": tags,
                }
            )

    return state


def tag_match(item: Dict, tag: str | None) -> bool:
    if not tag:
        return True
    return tag in item.get("tags", [])


def topic_match(item: Dict, topic: str | None) -> bool:
    if not topic:
        return True
    expected_tags = TOPIC_PRESETS.get(topic, [topic])
    current_tags = set(item.get("tags", []))
    return any(tag in current_tags for tag in expected_tags)


def is_this_week(item: Dict) -> bool:
    source = item.get("source_date")
    if not source:
        return False
    try:
        source_day = date.fromisoformat(source)
    except ValueError:
        return False
    today = date.today()
    return today - timedelta(days=6) <= source_day <= today


def due_cards(
    state: Dict,
    keyword: str | None = None,
    tag: str | None = None,
    topic: str | None = None,
    this_week: bool = False,
) -> List[Dict]:
    today = date.today()
    result = []
    for card_id, payload in state.get("cards", {}).items():
        due = date.fromisoformat(payload["due"])
        if due <= today:
            item = dict(payload)
            item["card_id"] = card_id
            haystack = " ".join(
                [
                    item.get("file_name", ""),
                    item.get("question_heading", ""),
                    item.get("question_text", ""),
                ]
            ).lower()
            if keyword and keyword.lower() not in haystack:
                continue
            if not tag_match(item, tag):
                continue
            if not topic_match(item, topic):
                continue
            if this_week and not is_this_week(item):
                continue
            result.append(item)
    result.sort(key=lambda x: (x["due"], x["file_name"], x["question_heading"]))
    return result


def update_schedule(payload: Dict, quality: int) -> None:
    today = date.today()
    ease = float(payload.get("ease", 2.5))
    repetitions = int(payload.get("repetitions", 0))
    interval = int(payload.get("interval_days", 0))
    lapses = int(payload.get("lapses", 0))

    if quality < 3:
        repetitions = 0
        interval = 1
        lapses += 1
    else:
        if repetitions == 0:
            interval = 1
        elif repetitions == 1:
            interval = 3
        else:
            interval = max(1, round(interval * ease))
        repetitions += 1

    ease = ease + (0.1 - (5 - quality) * (0.08 + (5 - quality) * 0.02))
    ease = max(1.3, round(ease, 2))

    payload["ease"] = ease
    payload["repetitions"] = repetitions
    payload["interval_days"] = interval
    payload["lapses"] = lapses
    payload["last_score"] = quality
    payload["last_reviewed"] = today.isoformat()
    payload["due"] = (today + timedelta(days=interval)).isoformat()


def cmd_list(
    state: Dict,
    keyword: str | None = None,
    tag: str | None = None,
    topic: str | None = None,
    this_week: bool = False,
    limit: int | None = None,
) -> None:
    due = due_cards(state, keyword=keyword, tag=tag, topic=topic, this_week=this_week)
    if limit is not None:
        due = due[:limit]
    if not due:
        print("No due cards today.")
        return
    print(f"Due cards: {len(due)}")
    for item in due:
        tags = ", ".join(item.get("tags", []))
        print(f"- {item['card_id']} | due {item['due']} | tags [{tags}] | {item['question_text']}")


def cmd_stats(
    state: Dict,
    keyword: str | None = None,
    tag: str | None = None,
    topic: str | None = None,
    this_week: bool = False,
) -> None:
    cards = state.get("cards", {})
    due = due_cards(state, keyword=keyword, tag=tag, topic=topic, this_week=this_week)
    print(f"Total cards: {len(cards)}")
    print(f"Due today: {len(due)}")
    if tag:
        print(f"Tag filter: {tag}")
    if topic:
        print(f"Topic filter: {topic}")
    if this_week:
        print("Week filter: only cards created in the last 7 days")

    if cards:
        lapses = sum(int(v.get("lapses", 0)) for v in cards.values())
        avg_ease = sum(float(v.get("ease", 2.5)) for v in cards.values()) / len(cards)
        print(f"Total lapses: {lapses}")
        print(f"Average ease: {avg_ease:.2f}")


def cmd_tags(state: Dict) -> None:
    counts: Dict[str, int] = {}
    for payload in state.get("cards", {}).values():
        for tag in payload.get("tags", []):
            counts[tag] = counts.get(tag, 0) + 1

    if not counts:
        print("No tags registered.")
        return

    print("Available tags:")
    for tag, count in sorted(counts.items(), key=lambda x: (x[0])):
        print(f"- {tag}: {count}")


def review_session(
    state: Dict,
    keyword: str | None = None,
    tag: str | None = None,
    topic: str | None = None,
    this_week: bool = False,
    limit: int | None = None,
) -> None:
    due = due_cards(state, keyword=keyword, tag=tag, topic=topic, this_week=this_week)
    if limit is not None:
        due = due[:limit]
    if not due:
        print("No due cards today.")
        return

    print(f"Starting review session. Due cards: {len(due)}")
    reviewed = 0

    for item in due:
        print("\n" + "=" * 72)
        print(f"{item['card_id']}")
        print(f"Source: {item['file_name']}")
        print(f"Question: {item['question_text']}")

        cmd = input("Press Enter to show answer, or type q to stop: ").strip().lower()
        if cmd == "q":
            break

        print("\nAnswer:")
        print(item["answer_text"] or "(No answer text parsed)")

        if item.get("closure_text"):
            print("\nClosure:")
            print(item["closure_text"])

        if item.get("usage_text"):
            print("\nUsage:")
            print(item["usage_text"])

        while True:
            score_raw = input("\nRate recall 0-5 (5=easy, 3=hard but remembered, 0=blank, q=stop): ").strip().lower()
            if score_raw == "q":
                save_state(state)
                print("Session stopped. Progress saved.")
                return
            if score_raw in {"0", "1", "2", "3", "4", "5"}:
                quality = int(score_raw)
                break
            print("Please input 0, 1, 2, 3, 4, 5, or q.")

        update_schedule(state["cards"][item["card_id"]], quality)
        reviewed += 1
        next_due = state["cards"][item["card_id"]]["due"]
        interval = state["cards"][item["card_id"]]["interval_days"]
        print(f"Saved. Next due: {next_due} (interval {interval} day(s))")
        save_state(state)

    print(f"\nSession finished. Reviewed: {reviewed}")


def main() -> None:
    parser = argparse.ArgumentParser(description="Local spaced repetition for notes/problems markdown cards.")
    parser.add_argument(
        "command",
        nargs="?",
        default="today",
        choices=["today", "list", "stats", "tags"],
        help="today: interactive review session, list: due cards, stats: summary, tags: available strict tags",
    )
    parser.add_argument("--limit", type=int, default=None, help="Only show/review the first N due cards.")
    parser.add_argument("--grep", type=str, default=None, help="Only include cards whose file name or question text contains this keyword.")
    parser.add_argument("--tag", type=str, default=None, help="Strict exact tag filter, e.g. 结构体指针, 补码, 输入缓冲")
    parser.add_argument(
        "--topic",
        type=str,
        default=None,
        help="Topic preset or keyword, e.g. 03-structs, 06-projects, 补码, 文件I/O",
    )
    parser.add_argument(
        "--this-week",
        action="store_true",
        help="Only include cards created in the last 7 days.",
    )
    args = parser.parse_args()

    cards = parse_cards()
    registry = load_tag_registry()
    state = load_state()
    state = sync_state(cards, state, registry)
    save_state(state)

    if args.command == "list":
        cmd_list(state, keyword=args.grep, tag=args.tag, topic=args.topic, this_week=args.this_week, limit=args.limit)
    elif args.command == "stats":
        cmd_stats(state, keyword=args.grep, tag=args.tag, topic=args.topic, this_week=args.this_week)
    elif args.command == "tags":
        cmd_tags(state)
    else:
        review_session(state, keyword=args.grep, tag=args.tag, topic=args.topic, this_week=args.this_week, limit=args.limit)


if __name__ == "__main__":
    main()
