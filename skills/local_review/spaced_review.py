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
    "06-projects": ["06-projects", "项目映射", "状态判断输出", "接口设计", "日志", "CSV", "状态机", "队列", "存储层", "时间戳", "返回值"],
    "补码": ["补码", "内存", "信号", "数据表示", "char", "有符号无符号", "模运算", "最高位", "位序", "组成原理"],
    "文件I/O": ["文件I/O", "缓冲输出", "字符输入输出", "输入缓冲", "scanf", "printf", "write", "getchar", "EOF"],
    "文件i/o": ["文件I/O", "缓冲输出", "字符输入输出", "输入缓冲", "scanf", "printf", "write", "getchar", "EOF"],
}


@dataclass
class TimeBlock:
    label: str
    minutes: int


def parse_time_block(raw: str) -> TimeBlock:
    separators = [":", "="]
    for separator in separators:
        if separator in raw:
            label, minutes_text = raw.split(separator, 1)
            label = label.strip()
            minutes_text = minutes_text.strip()
            if not label:
                raise argparse.ArgumentTypeError("time block label cannot be empty")
            try:
                minutes = int(minutes_text)
            except ValueError as exc:
                raise argparse.ArgumentTypeError("time block minutes must be an integer") from exc
            if minutes <= 0:
                raise argparse.ArgumentTypeError("time block minutes must be positive")
            return TimeBlock(label=label, minutes=minutes)

    raise argparse.ArgumentTypeError("time block must look like morning:60 or evening=120")


def total_block_minutes(blocks: List[TimeBlock]) -> int:
    return sum(block.minutes for block in blocks)


def session_budget(total_minutes: int) -> Dict[str, int]:
    """Return a small review budget that protects mainline project time."""
    if total_minutes <= 0:
        return {"review_minutes": 0, "card_limit": 0, "planning_minutes": 0, "mainline_minutes": 0, "close_minutes": 0}

    review_minutes = round(total_minutes * 0.16)
    if total_minutes <= 45:
        review_minutes = min(8, max(5, review_minutes))
    elif total_minutes <= 120:
        review_minutes = min(18, max(10, review_minutes))
    elif total_minutes <= 240:
        review_minutes = min(30, max(18, review_minutes))
    else:
        review_minutes = min(40, max(25, review_minutes))

    planning_minutes = 5 if total_minutes <= 45 else 8
    close_minutes = 5 if total_minutes >= 60 else 3
    card_limit = max(1, min(12, (review_minutes + 3) // 4))
    mainline_minutes = max(0, total_minutes - review_minutes - planning_minutes - close_minutes)

    return {
        "review_minutes": review_minutes,
        "card_limit": card_limit,
        "planning_minutes": planning_minutes,
        "mainline_minutes": mainline_minutes,
        "close_minutes": close_minutes,
    }


def allocate_block_plan(blocks: List[TimeBlock], budget: Dict[str, int]) -> List[Dict[str, int | str]]:
    """Allocate review/planning early, closeout late, and mainline in the gaps."""
    rows: List[Dict[str, int | str]] = []
    for block in blocks:
        rows.append(
            {
                "label": block.label,
                "minutes": block.minutes,
                "review": 0,
                "planning": 0,
                "mainline": block.minutes,
                "closeout": 0,
            }
        )

    def take_forward(key: str, amount: int) -> None:
        remaining = amount
        for row in rows:
            if remaining <= 0:
                return
            available = int(row["mainline"])
            used = min(available, remaining)
            row[key] = int(row[key]) + used
            row["mainline"] = available - used
            remaining -= used

    def take_backward(key: str, amount: int) -> None:
        remaining = amount
        for row in reversed(rows):
            if remaining <= 0:
                return
            available = int(row["mainline"])
            used = min(available, remaining)
            row[key] = int(row[key]) + used
            row["mainline"] = available - used
            remaining -= used

    take_forward("review", budget["review_minutes"])
    take_forward("planning", budget["planning_minutes"])
    take_backward("closeout", budget["close_minutes"])
    return rows


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


def related_tags(seed: str | None, registry: Dict) -> set[str]:
    if not seed:
        return set()

    relations = registry.get("tag_relations", {})
    result = {seed}

    # Keep this intentionally one-hop. Daily review should stay focused:
    # related-tag "FILE *" should pull direct file-I/O neighbors, not the
    # whole 06-projects graph through recursive parent links.
    result.update(relations.get(seed, []))

    for parent, children in relations.items():
        if seed in children:
            result.add(parent)

    return result


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


def related_tag_match(item: Dict, related_filter: set[str] | None) -> bool:
    if not related_filter:
        return True
    current_tags = set(item.get("tags", []))
    return bool(current_tags & related_filter)


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
    related_filter: set[str] | None = None,
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
            if not related_tag_match(item, related_filter):
                continue
            if this_week and not is_this_week(item):
                continue
            result.append(item)
    result.sort(key=lambda x: (x["due"], x["file_name"], x["question_heading"]))
    return result


def days_overdue(item: Dict) -> int:
    try:
        due = date.fromisoformat(item["due"])
    except (KeyError, ValueError):
        return 0
    return max(0, (date.today() - due).days)


def card_priority(item: Dict, main_topic: str | None = None, registry: Dict | None = None) -> int:
    """Score due cards for short daily sessions.

    Higher means more useful today: current mainline, weak recall, recent cards,
    and overdue cards rise to the top. This avoids old due cards consuming the
    whole session when project momentum matters.
    """
    score = 0

    registry = registry or {}
    current_tags = set(item.get("tags", []))

    if main_topic:
        current_tags = set(item.get("tags", []))
        if main_topic in current_tags:
            score += 45
        elif topic_match(item, main_topic):
            score += 20

        related = related_tags(main_topic, registry)
        overlap = current_tags & related
        score += min(25, len(overlap) * 4)

        weights = registry.get("workflow_weights", {}).get(main_topic, {})
        for tag in current_tags:
            score += int(weights.get(tag, 0))

    last_score = item.get("last_score")
    if last_score is None:
        score += 8
    else:
        try:
            last_score_int = int(last_score)
        except (TypeError, ValueError):
            last_score_int = 3
        if last_score_int <= 2:
            score += 30
        elif last_score_int == 3:
            score += 18
        elif last_score_int == 4:
            score += 6
        else:
            score -= 4

    score += min(12, days_overdue(item) * 2)
    score += min(20, int(item.get("lapses", 0)) * 8)

    try:
        source_day = date.fromisoformat(item.get("source_date", ""))
        age_days = (date.today() - source_day).days
        if age_days <= 3:
            score += 16
        elif age_days <= 7:
            score += 12
        elif age_days <= 14:
            score += 6
    except ValueError:
        pass

    return score


def source_ordinal(item: Dict) -> int:
    try:
        return date.fromisoformat(item.get("source_date", "")).toordinal()
    except ValueError:
        return 0


def take_balanced(ordered: List[Dict], limit: int, max_per_file: int = 2) -> List[Dict]:
    selected: List[Dict] = []
    selected_ids = set()
    counts: Dict[str, int] = {}

    for item in ordered:
        file_name = item.get("file_name", "")
        if counts.get(file_name, 0) >= max_per_file:
            continue
        selected.append(item)
        selected_ids.add(item["card_id"])
        counts[file_name] = counts.get(file_name, 0) + 1
        if len(selected) == limit:
            return selected

    for item in ordered:
        if item["card_id"] in selected_ids:
            continue
        selected.append(item)
        if len(selected) == limit:
            break

    return selected


def smart_order(due: List[Dict], main_topic: str | None = None, registry: Dict | None = None) -> List[Dict]:
    ordered = []
    for item in due:
        item_with_score = dict(item)
        item_with_score["_priority"] = card_priority(item_with_score, main_topic=main_topic, registry=registry)
        ordered.append(item_with_score)

    ordered.sort(
        key=lambda x: (
            -int(x.get("_priority", 0)),
            -source_ordinal(x),
            x.get("last_reviewed") or "",
            x["due"],
            x["file_name"],
            x["question_heading"],
        )
    )
    return ordered


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
    registry: Dict,
    keyword: str | None = None,
    tag: str | None = None,
    topic: str | None = None,
    related_tag: str | None = None,
    main_topic: str | None = None,
    this_week: bool = False,
    limit: int | None = None,
    smart: bool = False,
) -> None:
    related_filter = related_tags(related_tag, registry)
    due = due_cards(state, keyword=keyword, tag=tag, topic=topic, related_filter=related_filter, this_week=this_week)
    if smart or main_topic:
        due = smart_order(due, main_topic=main_topic, registry=registry)
    if limit is not None:
        if smart or main_topic:
            due = take_balanced(due, limit)
        else:
            due = due[:limit]
    if not due:
        print("No due cards today.")
        return
    print(f"Due cards: {len(due)}")
    if related_tag:
        print(f"Related tag filter: {related_tag} -> {', '.join(sorted(related_filter))}")
    for item in due:
        tags = ", ".join(item.get("tags", []))
        priority = f" | priority {item['_priority']}" if "_priority" in item else ""
        print(f"- {item['card_id']} | due {item['due']}{priority} | tags [{tags}] | {item['question_text']}")


def cmd_plan(
    state: Dict,
    registry: Dict,
    total_minutes: int,
    blocks: List[TimeBlock] | None = None,
    keyword: str | None = None,
    tag: str | None = None,
    topic: str | None = None,
    related_tag: str | None = None,
    main_topic: str | None = None,
    this_week: bool = False,
) -> None:
    budget = session_budget(total_minutes)
    related_filter = related_tags(related_tag, registry)
    due = due_cards(state, keyword=keyword, tag=tag, topic=topic, related_filter=related_filter, this_week=this_week)
    ordered = smart_order(due, main_topic=main_topic, registry=registry)
    selected = take_balanced(ordered, budget["card_limit"])

    print(f"Available minutes: {total_minutes}")
    print(f"Review budget: {budget['review_minutes']} min")
    print(f"Suggested card limit: {budget['card_limit']}")
    print(f"Planning budget: {budget['planning_minutes']} min")
    print(f"Mainline budget: {budget['mainline_minutes']} min")
    print(f"Closeout budget: {budget['close_minutes']} min")
    if main_topic:
        print(f"Main topic priority: {main_topic}")
        related = related_tags(main_topic, registry)
        if related:
            print(f"Main topic related tags: {', '.join(sorted(related))}")
    if related_tag:
        print(f"Related tag filter: {related_tag} -> {', '.join(sorted(related_filter))}")

    if blocks:
        print("\nTime blocks:")
        for block in blocks:
            print(f"- {block.label}: {block.minutes} min")

        print("\nSuggested block split:")
        for row in allocate_block_plan(blocks, budget):
            print(
                f"- {row['label']}: "
                f"SRS {row['review']} min, "
                f"planning {row['planning']} min, "
                f"mainline {row['mainline']} min, "
                f"closeout {row['closeout']} min"
            )

    if not selected:
        print("No due cards for this plan.")
        return

    print("\nSuggested cards:")
    for item in selected:
        tags = ", ".join(item.get("tags", []))
        print(f"- priority {item['_priority']} | {item['card_id']} | tags [{tags}] | {item['question_text']}")

    print("\nAfter-review calibration gate:")
    print("- I can explain the weak point in my own words.")
    print("- I can name the exact misconception I had.")
    print("- I can map it to today's mainline file/function/line.")
    print("- If it involves a function, I can state input, output, return value, and side effects.")
    print("- If it involves a pointer, I can separate object, address, pointer variable, and dereference.")
    print("- If it involves storage/return codes, I can separate success path and failure path.")

    print("\nDaily flow after SRS:")
    print("1. Calibrate understanding until the gate above is met.")
    print("2. Start daily mainline planning.")
    print("3. Start heuristic guided practice with the main logic blanked out.")
    print("4. Fill key conditions, loop body, return values, and call sites yourself before seeing hints.")

    command = f"python3 {Path(__file__).name} today --smart --limit {budget['card_limit']}"
    if blocks:
        for block in blocks:
            command += f" --block {block.label}:{block.minutes}"
    if main_topic:
        command += f" --main-topic {main_topic}"
    if topic:
        command += f" --topic {topic}"
    if tag:
        command += f" --tag {tag}"
    if related_tag:
        command += f" --related-tag {related_tag}"
    if keyword:
        command += f" --grep {keyword}"
    if this_week:
        command += " --this-week"
    print(f"\nRecommended command from skills/local_review/: {command}")


def cmd_stats(
    state: Dict,
    keyword: str | None = None,
    tag: str | None = None,
    topic: str | None = None,
    related_filter: set[str] | None = None,
    this_week: bool = False,
) -> None:
    cards = state.get("cards", {})
    due = due_cards(state, keyword=keyword, tag=tag, topic=topic, related_filter=related_filter, this_week=this_week)
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


def cmd_tags(state: Dict, registry: Dict) -> None:
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

    groups = registry.get("tag_groups", {})
    if groups:
        print("\nTag groups:")
        for group, tags in groups.items():
            present = [tag for tag in tags if tag in counts]
            if present:
                print(f"- {group}: {', '.join(present)}")


def cmd_map(registry: Dict) -> None:
    print("Strict tag map:")
    for group, tags in registry.get("tag_groups", {}).items():
        print(f"\n[{group}]")
        for tag in tags:
            print(f"- {tag}")

    relations = registry.get("tag_relations", {})
    if relations:
        print("\nRelated tag graph:")
        for tag, related in relations.items():
            print(f"- {tag} -> {', '.join(related)}")

    weights = registry.get("workflow_weights", {})
    if weights:
        print("\nWorkflow weights:")
        for workflow, tag_weights in weights.items():
            ordered = sorted(tag_weights.items(), key=lambda x: (-int(x[1]), x[0]))
            compact = ", ".join(f"{tag}:{weight}" for tag, weight in ordered)
            print(f"- {workflow}: {compact}")


def review_session(
    state: Dict,
    registry: Dict,
    keyword: str | None = None,
    tag: str | None = None,
    topic: str | None = None,
    related_tag: str | None = None,
    main_topic: str | None = None,
    this_week: bool = False,
    limit: int | None = None,
    smart: bool = False,
) -> None:
    related_filter = related_tags(related_tag, registry)
    due = due_cards(state, keyword=keyword, tag=tag, topic=topic, related_filter=related_filter, this_week=this_week)
    if smart or main_topic:
        due = smart_order(due, main_topic=main_topic, registry=registry)
    if limit is not None:
        if smart or main_topic:
            due = take_balanced(due, limit)
        else:
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
        if "_priority" in item:
            print(f"Priority: {item['_priority']}")
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
        choices=["today", "list", "stats", "tags", "map", "plan"],
        help="today: interactive review, list: due cards, plan: time-weighted smart plan, stats: summary, tags/map: strict tag tools",
    )
    parser.add_argument("--limit", type=int, default=None, help="Only show/review the first N due cards.")
    parser.add_argument("--minutes", type=int, default=None, help="Available study minutes; auto-sets review budget and card limit when used with plan/today.")
    parser.add_argument(
        "--block",
        action="append",
        type=parse_time_block,
        default=[],
        help="Available time block, repeatable. Examples: --block morning:60 --block evening=120",
    )
    parser.add_argument("--grep", type=str, default=None, help="Only include cards whose file name or question text contains this keyword.")
    parser.add_argument("--tag", type=str, default=None, help="Strict exact tag filter, e.g. 结构体指针, 补码, 输入缓冲")
    parser.add_argument("--related-tag", type=str, default=None, help="Include cards tagged with this tag or its related tags.")
    parser.add_argument(
        "--topic",
        type=str,
        default=None,
        help="Topic preset or keyword, e.g. 03-structs, 06-projects, 补码, 文件I/O",
    )
    parser.add_argument("--main-topic", type=str, default=None, help="Prioritize this topic without filtering out other due cards.")
    parser.add_argument("--smart", action="store_true", help="Rank due cards by mainline relevance, weak recall, recency, and overdue days.")
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
        cmd_list(
            state,
            registry,
            keyword=args.grep,
            tag=args.tag,
            topic=args.topic,
            related_tag=args.related_tag,
            main_topic=args.main_topic,
            this_week=args.this_week,
            limit=args.limit,
            smart=args.smart,
        )
    elif args.command == "stats":
        cmd_stats(
            state,
            keyword=args.grep,
            tag=args.tag,
            topic=args.topic,
            related_filter=related_tags(args.related_tag, registry),
            this_week=args.this_week,
        )
    elif args.command == "tags":
        cmd_tags(state, registry)
    elif args.command == "map":
        cmd_map(registry)
    elif args.command == "plan":
        total_minutes = total_block_minutes(args.block) if args.block else (args.minutes if args.minutes is not None else 90)
        cmd_plan(
            state,
            registry,
            total_minutes=total_minutes,
            blocks=args.block,
            keyword=args.grep,
            tag=args.tag,
            topic=args.topic,
            related_tag=args.related_tag,
            main_topic=args.main_topic,
            this_week=args.this_week,
        )
    else:
        limit = args.limit
        smart = args.smart
        total_minutes = total_block_minutes(args.block) if args.block else args.minutes
        if total_minutes is not None and limit is None:
            limit = session_budget(total_minutes)["card_limit"]
            smart = True
        review_session(
            state,
            registry,
            keyword=args.grep,
            tag=args.tag,
            topic=args.topic,
            related_tag=args.related_tag,
            main_topic=args.main_topic,
            this_week=args.this_week,
            limit=limit,
            smart=smart,
        )


if __name__ == "__main__":
    main()
