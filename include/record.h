#ifndef RECORD_H
#define RECORD_H

#define TIMESTAMP_SIZE 20

typedef struct
{
  char sampled_at[TIMESTAMP_SIZE];
  float temp;
  float oxygen;
  char pond_id;
}PondRecord;

#endif
