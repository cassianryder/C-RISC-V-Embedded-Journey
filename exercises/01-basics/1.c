// 验证数据类型内存所占的字节大小
#include <stdio.h>
int main(void){
  printf("short=%lu\n",sizeof(short));
  printf("int=%lu\n",sizeof(int));
  printf("long=%lu\n",sizeof(long));
  printf("float=%lu\n",sizeof(float));
  printf("double=%lu\n",sizeof(double));
  printf("char=%lu\n",sizeof(char));
 
  for(int i=0;i<128;i++)
    printf("%d=%c\n",i,i);

  unsigned char num = -1;
  printf("%d",num);
}
