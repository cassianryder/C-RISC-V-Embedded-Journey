#include "judge.h"

#define TEMP_LOW_LIMIT 24.5f
#define TEMP_HIGH_LIMIT 28.1f
#define OXYGEN_LOW_LIMIT 5.0f

const char *temp_status(float temperature)
{
  if (temperature < TEMP_LOW_LIMIT)
      return "low";
  else if (temperature <= TEMP_HIGH_LIMIT)
      return "normal";
  else
      return "high";
}

const char *oxygen_status(float oxygen)
{
  if (oxygen < OXYGEN_LOW_LIMIT)
      return "low";
  else
      return "normal";
}

int needs_aeration(PondRecord record)
{
  if (record.oxygen < OXYGEN_LOW_LIMIT)
      return 1;
  return 0;
}
