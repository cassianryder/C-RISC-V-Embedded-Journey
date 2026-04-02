//跳转语句,break continue goto
#include <stdio.h>
int main(void){
  int a = 0;
  for(int i = 0;i < 10;i++)
  {
    if(i == 5)
    {
      printf("找到了\n");
    }
    printf("%d\n",i);
  }
  return 0;
}
