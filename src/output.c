#include "output.h"
#include <stdio.h>

#define FORMAT_OK 0
#define FORMAT_ERR 1
#define BUFFER_TOO_SMALL 2

int output_format_record_line(char *buffer, size_t buffer_size,
                              PondRecord record, int should_aerate)
{
  int written;

  written = snprintf(buffer, buffer_size,
                     "pond=%c temp=%.2f oxygen=%.2f aerate=%s", record.pond_id,
                     record.temp, record.oxygen, should_aerate ? "on" : "off");

  if (written < 0)
    return FORMAT_ERR;
  if ((size_t)written >= buffer_size)
    return BUFFER_TOO_SMALL;
  return FORMAT_OK;
}
