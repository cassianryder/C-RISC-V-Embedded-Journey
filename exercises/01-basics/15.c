//4.29
//pointer
//&bah &pooh 常量 
//ptr = &bah val = *ptr,ptr变量中存储的是bah的地址，*ptr为去该地址下读取bah的值
//ptr = &bah;val = *ptr equals to val = bah,那在实际场景中赋值是同样的操作逻辑吗
//* 间接运算符 & 地址运算符
//声明指针：int *pi;char *pc;float *pf,*pg//pf,pg都是指向float类型变量的指针
//9.15 swap3.c 使用指针解决交换函数问题。按字节寻址系统？ 
//#include <stdio.h>
//void interchange(int *u,int *v);
//int main(void)
//{
//	int x = 5,y = 10;
//	printf("Originally x = %d and y = %d.\n",x,y);
//	interchange(&x,&y);//send address to the function
//	printf("Now x = %d and y = %d.\n",x,y);
//	return 0;
// } 
//void interchange(int *u,int *v)
//{
//	int temp;
//	temp = *u;
//	*u = *v;
//	*v = temp;
//}

/* day_mon1.c -- 打印每个月的天数 */ //10.1day_mon1.c 
//#include <stdio.h>
//#define MONTHS 12
//int main(void)
//{
//int days[MONTHS] = { 31, 28, 31, 30, 31, 30, 31, 31,
//30, 31, 30, 31 };
//int index;
//for (index = 0; index < MONTHS; index++)
//printf("Month %2d has %2d days.\n", index + 1, days[index]);
//return 0;
//}

//10.2 no_data.c
//初始化数组
//#include <stdio.h>
//#define SIZE 4
//int main(void)
//{
//	int no_data[SIZE];
//	int i;
//	printf("%2s%14s\n","i","no_data[i]");//这是printf的何种用法？ 
//	for(i = 0;i < SIZE;i++)
//	printf("%2d%14d\n",i,no_data[i]);
//	return 0;
// } 

//somedata.c
//#include <stdio.h>
//#define SIZE 4
//int main(void)
//{
//	int some_data[SIZE] = {1492,1066};
//	int i;
//	int j;
//	j = putchar('i'); 
////	printf("%2s%14s\n","i","some_data[i]");
//  printf("%c%14s\n",j,"some_data[i]");//有双引号为字符串 
//	for(i = 0;i < SIZE;i++)
//	{
//		printf("%2d%14d\n",i,some_data[i]);
////		return 1;
//	}
//	return 0;
//}

/* day_mon2.c -- 让编译器计算元素个数 */
//#include <stdio.h>
//int main(void)
//{
//   const int days[] = { 31, 28, 31, 30, 31, 30, 31, 31,30, 31 };
//   int index;
//   printf("%d\n",sizeof days);
//   printf("%d\n",sizeof days[0]);
//   for(index = 0;index < sizeof days/sizeof days[0];index++)
//   printf("Month %2d had %d days.\n",index + 1,days[index]);
//   return 0;
//}

//container.c
//#include <stdio.h>
//#define MONTHS 12
//int main(void)
//{
//	int days[MONTHS] = {31,28,[4] = 31,30,31,[1] = 29};
//	int i;
//	for(i = 0;i < MONTHS;i++)
//	printf("%2d %d\n",i + 1,days[i]);
//	return 0;
//}

//pnt_add.c 指针与地址 page 
#include <stdio.h>
#define SIZE 4
int main(void)
{
	short dates[SIZE];
	short *pti;
	short index;
	double bills[SIZE];
	double *ptf;
	pti = dates;
	ptf = bills;
	printf("%23s %15s\n","short","double");
	for(index = 0;index < SIZE;index ++)
	printf("pointers + %d: %10p %10p\n",index,pti + index,ptf + index);
	return 0;
 } 

