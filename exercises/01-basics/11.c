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
#include <stdio.h>

void check_oxygen(double oxygen)
{
 if(oxygen < 3.00) printf("溶氧异常\n");
  else printf("溶氧正常\n");
}

void check_temperature(double oxygen,double temperature)
{
 if(temperature > 28.00) printf("温度异常\n");
  else printf("温度正常\n");
}
int main(void){
  double m,n;
  scanf("%lf%lf",&m,&n);
  check_oxygen(m);
  check_temperature(m,n);
  return 0;
}
