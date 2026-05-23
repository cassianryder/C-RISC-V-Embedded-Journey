#ifndef JUDGE_H
#define JUDGE_H

#include "record.h"

const char *temp_status(float temperature);
const char *oxygen_status(float oxygen);
int needs_aeration(PondRecord record);

#endif
