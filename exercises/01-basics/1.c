// 验证数据类型内存所占的字节大小
#include <stdio.h>
int main(void){
  // printf("short=%lu\n",sizeof(short));
  // printf("int=%lu\n",sizeof(int));
  // printf("long=%lu\n",sizeof(long));
  // printf("float=%lu\n",sizeof(float));
  // printf("double=%lu\n",sizeof(double));
  // printf("char=%lu\n",sizeof(char));
  //
  // for(int i=0;i<128;i++)
  //   printf("%d=%c\n",i,i);
  //
  // unsigned char num = -1;
  // printf("%d",num);

  //int 类型与进制输出
  
  unsigned int x = 10;
  printf("%#X\n",x);//将变量x转为无符号十六进制数
  printf("%#X\n",&x);//将变量x的首地址输出
  printf("%lu\n",sizeof(&x));
  printf("%lu\n",sizeof(x));
  printf("%lu\n",sizeof(int));
  printf("%d\n",x);
  printf("八进制:%o\n",x);
  printf("十六进制:%X\n",x);
  printf("十六进制小写:%x\n",x);
  return 0;
}
