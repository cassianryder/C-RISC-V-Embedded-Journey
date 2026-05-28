#include <stdio.h>
#include "csv_store.h"

#define CSV_STORE_OK 0
#define CSV_STORE_ERR_OPEN 1
#define CSV_STORE_ERR_SEEK 2
#define CSV_STORE_ERR_TELL 3
#define CSV_STORE_ERR_WRITE_HEADER 4
#define CSV_STORE_ERR_WRITE_RECORD 5
#define CSV_STORE_ERR_CLOSE 6

int csv_store_append_record(const char *filename, PondRecord record)
{
  FILE *fp = fopen(filename, "a+");

  if (fp == NULL)
      return CSV_STORE_ERR_OPEN;

  if (fseek(fp, 0, SEEK_END) != 0)
  {
    fclose(fp);
    return CSV_STORE_ERR_SEEK;
  }

  long size = ftell(fp);

  if (size < 0)
  {
    fclose(fp);
    return CSV_STORE_ERR_TELL;
  }

  if (size == 0)
  {
    if (fprintf(fp, "sampled_at,pond_id,temp,oxygen\n") < 0)
    {
      fclose(fp);
      return CSV_STORE_ERR_WRITE_HEADER;
    }
  }

  if (fprintf(fp, "%s,%c,%.2f,%.2f\n",
          record.sampled_at,
          record.pond_id,
          record.temp,
          record.oxygen) < 0)
  {
    fclose(fp);
    return CSV_STORE_ERR_WRITE_RECORD;
  }

  if (fclose(fp) != 0)
    return CSV_STORE_ERR_CLOSE;
  return CSV_STORE_OK;
}
