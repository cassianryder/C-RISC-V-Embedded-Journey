#include <stdio.h>
#include <string.h>
#include "csv_store.h"

int main(void)
{
  int fail_test = 0;
  const char *filename = "build/test_pond_log.csv";
  remove(filename);

  PondRecord record_test_1 = {"", 23.6f, 4.5f, 'A'};
  PondRecord record_test_2 = {"", 23.8f, 5.7f, 'B'};
  PondRecord record_test_3 = {"", 23.7f, 5.9f, 'C'};


  if(csv_store_append_record(filename, record_test_1) != 0)
  {
    printf("csv_store_append_record函数测试未通过！\n");
    fail_test++;
  }

  if(csv_store_append_record(filename, record_test_2) != 0)
  {
    printf("csv_store_append_record函数测试未通过！\n");
    fail_test++;
  }

    if(csv_store_append_record(filename, record_test_3) != 0)
  {
    printf("csv_store_append_record函数测试未通过！\n");
    fail_test++;
  }

  int header_count = 0;
  int line_count = 0;
  char line[128];
  int ch;
  FILE *fp = fopen(filename, "r");

  if(fp == NULL)
  {
    printf("打开csv失败！\n");
    fail_test++;
  }
  else
  {
    while((ch = fgetc(fp)) != EOF)
    {
     if(ch == '\n')
        line_count++;
    }

    fclose(fp);

    if(line_count < 2)
    {
      printf("csv_store_append_record写入行数测试未通过！\n");
      fail_test++;
    }
  }
  
  if(fail_test == 0)
    printf("csv_store_append_record函数测试通过！\n");
  return fail_test;

  }

