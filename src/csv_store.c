#include <stdio.h>
#include "csv_store.h"

int csv_store_append_record(const char *filename, PondRecord record)
{
  FILE *fp = fopen(filename,"a");

  if (fp == NULL)
      return 1;

  fprintf(fp, "%s,%c,%.2f,%.2f\n",
          record.sampled_at,
          record.pond_id,
          record.temp,
          record.oxygen);
  fclose(fp);
  return 0;
}
