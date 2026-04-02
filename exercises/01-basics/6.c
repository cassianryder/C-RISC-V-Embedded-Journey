//循环语句for while do while
#include <stdio.h>
int main(void){
  // int i,s=0;
  // for(i=0;i<101;i++){
  //   s += i;
  // }
  // printf("%d\n",s);

  // int num = 0;
  // for(int i=0;i <= 100;i++)//先判断后后执行
  // {
  //  num += i;
  // }
  // printf("%d\n",num);

  // int a = 1;
  // while(a < 10)
  // {
  //   printf("%d\n",++a);//先判断后执行
  // }
  
  int a = 0;
  do 
  {
    printf("%d\n",a++);//先执行后判断
  }
  while (a < 10);//do while 有分号
  return 0;
}
