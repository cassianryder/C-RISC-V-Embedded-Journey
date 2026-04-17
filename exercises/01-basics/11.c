//函数的嵌套调用：主调函数与被调函数的关系
//被调函数在主调函数之前要有定义或声明
//函数是否可以嵌套定义？无法嵌套定义
//函数的递归调用：主调函数在函数体内部直接或间接调用了该主调函数
//1.递归函数带参数，通过参数决定是否递归结束
// #include <stdio.h>
//
//  int myFun(int n)
//   {
//     if (n <= 1) return 1;//递归
//   else return n * myFun(n - 1);
//   }
//
// int main(void){
//   int m;
//   scanf("%d",&m);//入口函数，代码从入口函数开始
//   printf("%d\n",myFun(m));
// return 0;
// }

//池塘数据监控器
//输入温度和溶氧，输出温度正常/异常，溶氧正常/不足
// #include <stdio.h>
//
// void check_oxygen(double oxygen)
// {
//  if(oxygen < 3.65) printf("溶氧异常\n");
//   else printf("溶氧正常\n");
// }
//
// void check_temperature(double oxygen,double temperature)
// {
//  if(temperature < 23.45 || temperature > 28.76) printf("水温异常\n");
//   else printf("水温正常\n");
//   check_oxygen(oxygen);//嵌套调用 
// }
// int main(void){
//   double oxygen,temperature;//局部变量
//   if (scanf("%lf%lf",&oxygen,&temperature) != 2)//scanf读取返回值判断，\0 空格
//   {
//     printf("无效值！\n");
//     return 1;
//   }
//   printf("溶氧：%.2f mg/L\n",oxygen);
//   printf("水温：%.2f C\n",temperature);
//   check_temperature(oxygen,temperature);//实际参数
//   return 0;
// }

//C Primer plus 
// #include <stdio.h>
// #define SIZE 50
//
//
// int main(void){
//  float list[SIZE];
//  readlist(list,SIZE);
//  sort(list,SIZE);
//   average(list,SIZE);
//   bargraph(list,SIZE);
//   return 0;
// }

//9.1 lethead1.c 
#include <stdio.h>
#define NAME "GIGATHINK, INC."
#define ADDRESS "101 Megabuck Plaza"
#define PLACE "Megapolis, CA 94904"
#define WIDTH 40

void starbar(void)
{
    for(int count = 1;count < WIDTH;++count)
  {
    putchar('*');
    putchar('\n');
  }
}

int main(void){
  starbar();
printf("%s\n", NAME);
printf("%s\n", ADDRESS);
printf("%s\n", PLACE);
  starbar();
}

