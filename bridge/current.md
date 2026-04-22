# Current Snapshot

> Daily overwrite only. For GPT Project to read current mainline progress, unresolved problems, and natural next step.

- Current main file: `exercises/01-basics/12.c`

- Current focus: `numeric + status record output`, `judging layer -> recording layer`, `input buffer / string length / return-value fundamentals`

- Recently completed:
  - Finished the minimal judging-output loop in `12.c` based on temperature and oxygen values: `pond A temp:Normal oxygen:Low`.
  - Narrowed `12.c` back to the actual mainline and mostly removed older test output from the main path.
  - Used `13.c` to strengthen fundamentals around recursion stack frames, input-buffer cleanup, character vs string, and `strlen` vs `sizeof`.

- Current unresolved issues:
  - `12.c` still outputs status only, not a fuller `value + status` record line.
  - `my_ponds_status()` and the newer judging functions are still two separate approaches and need a cleaner unified interface.
  - `printf` and `write` are still mixed.
  - Temperature and oxygen thresholds are not yet extracted as constants.

- Natural next step:
  - Push `12.c` to output a fuller line such as `pond A temp:27.5(normal) oxygen:4.2(low)`.
  - If progress is good, extract thresholds into constants and tighten the record-output function.
  - Then prepare for organizing one full pond record with a struct later.
