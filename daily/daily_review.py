#!/usr/bin/env python3
from __future__ import annotations

import argparse
import subprocess
import sys
from pathlib import Path


REPO_ROOT = Path(__file__).resolve().parents[1]
SRS_SCRIPT = REPO_ROOT / "skills" / "local_review" / "spaced_review.py"
DEFAULT_MAIN_TOPIC = "06-projects"


def build_srs_command(args: argparse.Namespace) -> list[str]:
    command = [sys.executable, str(SRS_SCRIPT), args.action]

    if args.minutes is not None:
        command.extend(["--minutes", str(args.minutes)])

    for block in args.block:
        command.extend(["--block", block])

    if args.limit is not None:
        command.extend(["--limit", str(args.limit)])

    if args.grep:
        command.extend(["--grep", args.grep])

    if args.tag:
        command.extend(["--tag", args.tag])

    if args.related_tag:
        command.extend(["--related-tag", args.related_tag])

    if args.topic:
        command.extend(["--topic", args.topic])

    if args.main_topic:
        command.extend(["--main-topic", args.main_topic])

    if args.smart:
        command.append("--smart")

    if args.this_week:
        command.append("--this-week")

    return command


def print_daily_header(args: argparse.Namespace) -> None:
    print("Daily SRS -> Mainline Loop")
    print(f"- action: {args.action}")
    print(f"- main topic: {args.main_topic}")
    if args.minutes is not None:
        print(f"- available minutes: {args.minutes}")
    if args.block:
        print(f"- blocks: {', '.join(args.block)}")
    print("")
    print("After SRS, do not jump directly into code.")
    print("Calibration gate:")
    print("- Explain the weak point in your own words.")
    print("- Name the misconception.")
    print("- Map it to today's mainline function or line.")
    print("- State input, output, return value, and side effects if a function is involved.")
    print("")


def print_after_plan() -> None:
    print("")
    print("Mainline planning reminder:")
    print("- Pick one small engineering target.")
    print("- Blank out the main logic before seeing the full answer.")
    print("- Compile and run before recording notes.")
    print("- Close the loop before opening a new concept.")


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Daily wrapper around skills/local_review/spaced_review.py."
    )
    parser.add_argument(
        "action",
        nargs="?",
        default="plan",
        choices=["plan", "today", "list", "stats", "tags", "map"],
        help="Daily action. Defaults to plan.",
    )
    parser.add_argument("--minutes", type=int, help="Available minutes today.")
    parser.add_argument(
        "--block",
        action="append",
        default=[],
        help="Available time block, e.g. morning:60 or evening=120.",
    )
    parser.add_argument("--limit", type=int, help="Card limit.")
    parser.add_argument("--grep", help="Keyword filter.")
    parser.add_argument("--tag", help="Strict tag filter.")
    parser.add_argument("--related-tag", help="Include this tag and related tags.")
    parser.add_argument("--topic", help="Topic filter or preset.")
    parser.add_argument(
        "--main-topic",
        default=DEFAULT_MAIN_TOPIC,
        help="Mainline topic priority. Defaults to 06-projects.",
    )
    parser.add_argument(
        "--no-smart",
        dest="smart",
        action="store_false",
        help="Disable smart ranking.",
    )
    parser.add_argument(
        "--this-week",
        action="store_true",
        help="Only include cards created this week.",
    )
    parser.set_defaults(smart=True)

    args = parser.parse_args()

    if not SRS_SCRIPT.exists():
        print(f"Error: missing SRS script: {SRS_SCRIPT}", file=sys.stderr)
        return 1

    print_daily_header(args)
    sys.stdout.flush()
    command = build_srs_command(args)
    result = subprocess.run(command, cwd=REPO_ROOT)
    if result.returncode != 0:
        return result.returncode

    if args.action in {"plan", "today", "list"}:
        print_after_plan()

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
