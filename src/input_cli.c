#include "input_cli.h"
#include <stdio.h>

InputParseResult input_parse_record_line(const char *line,
                                         PondRecord *out_record)
{
  if (line == NULL)
    return INPUT_PARSE_ERR_NULL_ARG;
  if (out_record == NULL)
    return INPUT_PARSE_ERR_NULL_ARG;

  char pond_id;
  float temp;
  float oxygen;
  char extra;
  int matched;

  matched = sscanf(line, " %c,%f,%f %c", &pond_id, &temp, &oxygen, &extra);

  if (matched != 3)
    return INPUT_PARSE_ERR_FORMAT;

  out_record->pond_id = pond_id;
  out_record->temp = temp;
  out_record->oxygen = oxygen;
  out_record->sampled_at[0] = '\0';
  return INPUT_PARSE_OK;
}
