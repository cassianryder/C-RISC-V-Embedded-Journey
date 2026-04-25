# Current Snapshot

> Daily overwrite only. For GPT Project to read current mainline progress, unresolved problems, and natural next step.

- Current main file: `exercises/03-structs/1.c`

- Current focus: `struct packs one record`, `struct as function parameter`, `main only handles control flow`

- Recently completed:
  - Split the pointer-focused code out of `10.c` into `exercises/02-pointers/1.c` and kept the key learning comments.
  - Split the struct-focused mainline into `exercises/03-structs/1.c`, added a short file-purpose header, and made it the new main file.
  - Split the pond-record CLI seed into `exercises/06-projects/1.c` and kept it as the clean project seed.
  - Updated the `Makefile` entries so `make 21`, `make 31`, and `make 61` now run the pointer / struct / project tracks directly.

- Current unresolved issues:
  - Input is still handled directly in `main`; it has not been wrapped into `read_pond_record(PondRecord *record)` yet.
  - The next conceptual jump is struct pointer access with `->`.
  - Temperature and oxygen thresholds are not yet extracted as constants.

- Natural next step:
  - Continue directly in `exercises/03-structs/1.c` with `read_pond_record(PondRecord *record)`.
  - Build a clean distinction between `record.temp` and `record->temp`.
  - If progress is good, extract thresholds into constants.
