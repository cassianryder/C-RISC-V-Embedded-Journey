#include <stdio.h>
#include "control.h"

int main(void)
{
  int fail_test = 0;
  PondRecord low_record = {"", 24.0f, 4.0f, 'A'};

  if(control_should_aerate(low_record) != 1)
  {
    printf("control_should_aerate函数测试未通过！\n");
    fail_test++;
  }

  PondRecord boundary_record = {"", 24.0f, 5.0f, 'A'};

   if(control_should_aerate(boundary_record) != 0)
  {
    printf("control_should_aerate函数测试未通过！\n");
    fail_test++;
  }

   PondRecord normal_record = {"", 24.0f, 6.0f, 'A'};

   if(control_should_aerate(normal_record) != 0)
  {
    printf("control_should_aerate函数测试未通过！\n");
    fail_test++;
  }

  if (fail_test == 0)
    printf("control_should_aerate函数测试通过！\n");

  return fail_test;




  
}
