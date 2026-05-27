#ifndef CSV_STORE_H
#define CSV_STORE_H

#include "record.h"

int csv_store_append_record(const char *filename, PondRecord record);

#endif
