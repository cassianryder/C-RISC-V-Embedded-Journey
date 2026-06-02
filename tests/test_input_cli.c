#include "input_cli.h"
#include <stdio.h>
#include <string.h>

static int float_in_range(float value, float low, float high)
{
  return value > low && value < high;
}

int main(void)
{
  int fail_test = 0;
  PondRecord record;
  InputParseResult result;

  result = input_parse_record_line("A,23.60,4.50", &record);

  if (result != INPUT_PARSE_OK)
  {
    printf("input_parse_record_line有效输入返回值测试未通过！\n");
    fail_test++;
  }

  if (record.pond_id != 'A')
  {
    printf("input_parse_record_line塘口解析测试未通过！\n");
    fail_test++;
  }

  if (!float_in_range(record.temp, 23.59f, 23.61f))
  {
    printf("input_parse_record_line温度解析测试未通过！\n");
    fail_test++;
  }

  if (!float_in_range(record.oxygen, 4.49f, 4.51f))
  {
    printf("input_parse_record_line溶氧解析测试未通过！\n");
    fail_test++;
  }

  if (strcmp(record.sampled_at, "") != 0)
  {
    printf("input_parse_record_line时间戳默认值测试未通过！\n");
    fail_test++;
  }

  result = input_parse_record_line("A,23.60", &record);

  if (result != INPUT_PARSE_ERR_FORMAT)
  {
    printf("input_parse_record_line字段缺失测试未通过！\n");
    fail_test++;
  }

  result = input_parse_record_line("A,23.60,4.50,extra", &record);

  if (result != INPUT_PARSE_ERR_FORMAT)
  {
    printf("input_parse_record_line多余字段测试未通过！\n");
    fail_test++;
  }

  result = input_parse_record_line("A,23.60,4.50abc", &record);

  if (result != INPUT_PARSE_ERR_FORMAT)
  {
    printf("input_parse_record_line尾部脏数据测试未通过！\n");
    fail_test++;
  }

  result = input_parse_record_line(NULL, &record);

  if (result != INPUT_PARSE_ERR_NULL_ARG)
  {
    printf("input_parse_record_line空输入参数测试未通过！\n");
    fail_test++;
  }

  result = input_parse_record_line("A,23.60,4.50", NULL);

  if (result != INPUT_PARSE_ERR_NULL_ARG)
  {
    printf("input_parse_record_line空输出参数测试未通过！\n");
    fail_test++;
  }

  if (fail_test == 0)
    printf("input_parse_record_line函数测试通过！\n");

  return fail_test;
}
