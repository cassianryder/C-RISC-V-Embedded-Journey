//赋值运算符,其他运算符
#include <stdio.h>
int main(void){
  unsigned char a = 10;
  a = 12;
  a += 3;
  a ^= 3;
  printf("%d\n",a);
  printf("%lu\n",sizeof(int));
  printf("%lu\n",sizeof(a));
  printf("%lu\n",sizeof(3.13));
  unsigned char b = 20;
  int max = a > b ? a : b;
  printf("max=%d\n",max);
  int x,y,z;
  z=((x = 3),(y = 4),(x + y));
  printf("%d\n",z);//逗号运算符
  // if (a>b
  //   printf("max=%d\n",a);
  // else 
  //   printf("max=%d\n",b);
  //

  return 0;
}
