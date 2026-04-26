//#include <stdio.h>//写不同版本，简化代码 
//int imin(int n,int m)
//{
//	if(n < m)
//	return n;
//	else 
//	return m;
//}
//int main(void)
//{
//	int i,j=0;
//	int k;
//	printf("please enter two numbers:");
//	if(scanf("%d%d",&i,&j) != 2)//the numbers of return value
//	{
//		printf("please enter a invalid integer!");
//		return 1;
//	}
//	k = imin(i,j);
//    printf("%d\n",k);
//	return 0;
//} 

//#include <stdio.h>
//int imax(int,int);//为何需要额外写这个声明 
//int main(void)
//{
//	printf("the maximum of %d and %d is %d\n",3,5,imax(3,5));
//	printf("the maximum of %d and %d is %d\n",3,5,imax(3.0,5.0));//浮点数如何传入函数 double转成int会丢失数据 
//	return 0;
//} 
//int imax(int n,int m)
//{
//	return (n > m ? n : m);
//}
//
//#include <stdio.h>
//int r(int a)
//{
//	if(a == 0 || a == 1)
//  return 1;
//  else 
//  return a * r(a-1);
//	int k = 0;
//	k = a * r(a-1);
//	return k; 
//}
//
//int main(void)
//{
//	int i = 0;
//	printf("please enter a integer:");
//	if(scanf("%d",&i) != 1)
//	{
//		printf("please enter a integer!");
//		return 1; 
//	}
//	r(i);
//	return 0;
//}

// #include <stdio.h>
// void to_binary(unsigned long n)
// {
// 	int r;
// 	r = n % 2;
// 	if(n >= 2)
// 	to_binary(n / 2);
// 	putchar(r == 0 ? '0' : '1');//这是什么运算符
// 	return; 
// }
// int main(void)
// {
// 	unsigned long number;
// 	printf("Enter an integer (q to quit):\n");
// 	while (scanf("%lu",&number) == 1)
// 	{
// 		printf("Binary equivalent:");
// 		to_binary(number);
// 		putchar('\n');//无法使用printf，此函数用来打印字符串，相当与my_puts
// 		printf("Enter a integer (q to quit):\n"); 
// 	}
// 	printf("Done\n");
// 	return 0;
// }
//

//C K&R 
//
#include <stdio.h>


