#ifndef INPUT_CLI_H
#define INPUT_CLI_H

#include "record.h"

typedef enum
{
  INPUT_PARSE_OK = 0,
  INPUT_PARSE_ERR_NULL_ARG = 1,
  INPUT_PARSE_ERR_FORMAT = 2
} InputParseResult;

InputParseResult input_parse_record_line(const char *line, PondRecord *out_record);

#endif

