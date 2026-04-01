//表达式,语句
#include <stdio.h>
int main(void){
  // int hp = 10;
  // hp += 2;
  // printf("%d\n",hp);

  // char a = 100;
  // int b = 10;
  // b = b + a;//类型自动转换,小类型转大类型
  // printf("%d\n",b);
  //
  // int hp = 10;
  // hp += (int)2.0f;//大类型转小类型,对于基本数据类型，short,int,long,char,double,float会自动实现强制转换
  // printf("%d\n",hp);
  
  int a = 2,b = 4;
  // scanf("%d_%d",&a,&b);
  scanf("%d",&a);
  scanf("%d",&b);
  printf("a=%d,b=%d\n",a,b);
  
  return 0;
}
