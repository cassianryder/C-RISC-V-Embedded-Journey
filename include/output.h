#ifndef OUTPUT_H
#define OUTPUT_H

#include <stddef.h>
#include "record.h"

int output_format_record_line(char *buffer, size_t buffer_size, PondRecord record, int should_aerate);

#endif
