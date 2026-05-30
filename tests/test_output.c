#include "output.h"
#include <stdio.h>
#include <string.h>

int main(void)
{
  int fail_test = 0;
  char buffer[128];
  char small_buffer[8];
  PondRecord record_test_1 = {"2026-05-30 10:00:00", 23.6f, 4.5f, 'A'};

  if (output_format_record_line(buffer, sizeof(buffer), record_test_1, 1) != 0)
  {
    printf("output_format_record_line函数测试未通过！\n");
    fail_test++;
  }

  if (strcmp(buffer, "pond=A temp=23.60 oxygen=4.50 aerate=on") != 0)
  {
    printf("output_format_record_line函数测试未通过！\n");
    fail_test++;
  }

  if (output_format_record_line(small_buffer, sizeof(small_buffer),
                                record_test_1, 1) != 2)
  {
    printf("output_format_record_line函数测试未通过！\n");
    fail_test++;
  }

  if (fail_test == 0)
    printf("output_format_record_line函数测试通过！\n");
  return fail_test;
}
