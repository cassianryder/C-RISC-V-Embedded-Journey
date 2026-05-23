#include <stdio.h>
#include <string.h>
#include "judge.h"

int main(void)
{
  int fail_test = 0;
  PondRecord record = {"", 24.0f, 4.0f, 'A'};
  PondRecord boundary_record = {"", 25.0f, 5.0f, 'B'};
  PondRecord normal_record = {"", 29.0f, 6.0f, 'C'};

  if (strcmp(oxygen_status(record.oxygen),"low") != 0)
  {
    printf("oxygen_status函数测试未通过！\n");
    fail_test++;
  }

  if (strcmp(temp_status(record.temp),"low") != 0)
  {
    printf("temp_status函数测试未通过！\n");
    fail_test++;
  }

  if (needs_aeration(record)!= 1)
  {
    printf("needs_aeration函数测试未通过！\n");
    fail_test++;
  }

  if (strcmp(temp_status(boundary_record.temp),"normal") != 0)
  {
    printf("temp_status函数测试未通过！\n");
    fail_test++;
  }

    if (strcmp(temp_status(normal_record.temp),"high") != 0)
  {
    printf("temp_status函数测试未通过！\n");
    fail_test++;
  }

  for (record.oxygen = 5.0f; record.oxygen <= 6.0f; record.oxygen++)
  {
  if (strcmp(oxygen_status(record.oxygen),"normal") != 0)
  {
    printf("oxygen_status函数测试未通过！\n");
    fail_test++;
  }

  if (needs_aeration(record)!= 0)
  {
    printf("needs_aeration函数测试未通过！\n");
    fail_test++;
  }

  }

  if (fail_test == 0)
    printf("测试通过！\n");
 return fail_test;
}
