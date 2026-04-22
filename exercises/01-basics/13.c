///* lethead1.c */ 
//#include <stdio.h>
//#define NAME "GIGATHINK, INC."
//#define ADDRESS "101 Megabuck Plaza"
//#define PLACE "Megapolis, CA 94904"
//#define WIDTH 40
//#define WIDTH_1 30
//
//void starbar(void)
//{
//	for (int count = 1;count <= WIDTH;++count)
//	putchar('*');
//	putchar('\n'); 
//}

//void starbar_1(void)
//{
//	for (int count_1 = 1;count_1 <= WIDTH_1;++count_1)
//	putchar('*');
//}

//int main(void)
//{
//	starbar();
// printf("%s",NAME);
//  printf("%s",ADDRESS);
//   printf("%s\n",PLACE);
//   starbar();
//   return 0;
//}

//实现阶乘，递归
//#include <stdio.h>
//
//int rec(int a)
//{
////	int c = 0;
//	if(a <= 0)
//    return 1;
//	else 
////	 c = rec(a) * rec(a-1);
//	 return a * rec(a-1);
//}
//
//int main(void)
//{
//	int a = 0;
//	printf("enter a integer:"); 
//	if(scanf("%d",&a) != 1)
//	{
//	printf("please enter a integer!!!");
//	return 1;
//	}
//	if(a < 0)
//	return 1;
//	else
//	printf("rec = %d\n",rec(a));
//	return 0;
//}

//实现my_putchar,my_flush
//#include <stdio.h>
//#include <unistd.h>
//#define BUF_SIZE 8
//
//char buf[BUF_SIZE];
//int buf_len = 0;
//
//void my_flush(void)
//{
//	if(buf_len > 0)
//	{
//		write(1,buf,buf_len);
//		buf_len = 0;
//	}
//}
//
//int my_putchar(int c)
//{
//	buf[buf_len] = c;
//	buf_len++;
//	if(buf_len = BUF_SIZE || c =='\n')
//	{
//		write(1,buf,buf_len);
//		buf_len = 0;
//	}
//	return c;
//}
//int main(void)
//{
//	int a = 0;
//	printf("enter char:");
//	if((scanf("%c",&a)) != 1)
//	{
//	printf("please enter a char!");
//	return 1;
//	}
//	my_putchar(a);
////	my_putchar('H');
////	my_putchar(72);
////	my_putchar('i');
////	my_putchar('\n');
//	my_flush();
//	return 0;
//}

#include <stdio.h>//这个程序的逻辑
//void up_and_down(int);
void up_and_down(int n)
{
	printf("Level %d: n location %p\n",n,&n);//%p的作用？
	if(n < 4)
	up_and_down(n + 1);
	printf("LEVEL %d: n location %p\n",n,&n);
}
int main(void)
{
	up_and_down(1);
	return 0;
}

//The usage of the for
//praise1.c 
//#include <stdio.h>
//int main(void)
//{
//	const int FIRST_OZ = 46;
//	const int NEXT_OZ = 20;
//	int ounces,cost; 
//	printf("ounces cost\n");
//	for(ounces = 1,cost = FIRST_OZ;ounces <= 16;ounces++,cost += NEXT_OZ)//可以这样用？
//	printf("%5d $%4.2f\n",ounces,cost / 100.0);
//	return 0;
//}

//字符数组，my_putchar,my_putstr,my_flush,getchar() flush the buffer 
//#include <stdio.h>
//#include <unistd.h>
//#define BUF_SIZE 8
//#define PRAISE "You are an extraordinary being." 
//
//char buf[BUF_SIZE];
//int buf_len = 0;
//
//void my_flush(void)
//{
//	if(buf_len > 0)
//	{
//		write(1,buf,buf_len);
//		buf_len = 0;
//	}
//}
//
//int my_putchar(int c)
//{
// buf[buf_len] = c;
// buf_len++;
// if(buf_len == BUF_SIZE || c == '\n')
// {
// 	write(1,buf,buf_len);
// 	buf_len = 0;
// }
// return c;
//}
//
//int my_putstr(const char *s)
//{
// int count = 0;
// while(s[count] != '\0')
// {
// my_putchar(s[count]);
// count ++;
// }
// return count;
//} 
//
//int main(void)
//{
//	int size = 0;
//	int s;
//	s = my_putstr("please enter the array size:");
//	putchar(' ');
//	printf("%d\n",s);
////	printf("please enter the array size:");
//	if(scanf("%d",&size) != 1)//return value 
//	{
//		printf("please enter a lnvid integer!!!\n");
//		return 1;
//	}
//	
//	while(getchar() != '\n');//flush the buffer//why？
//	
//	char name[size];//have a '/0' at the end of the array.you can compare "x" whit 'x',use the first principle to explaining it.
//	printf("please enter your name:");
//	if(scanf("%s",&name) != 1) //这里!=1是返回值不等于1吗？
//	{
//		printf("please enter a Invid char!!!");
//		return 1;
//	}
//	printf("What's your name?");
//	printf("Hello,%s.%s",name,PRAISE);
//	return 0;
//}

//strlen function 
//praise2.c
//#include <stdio.h>
//#include <string.h>
//#define PRAISE "wherever we look nowdays"
//int main(void)
//{
//	char name[40];
//    printf("what's your name?");
//    scanf("%s",&name);
//    printf("Hello,%s.%s\n",name,PRAISE);
//	printf("your name of %zd letters occupies %zd momery cells.\n",strlen(name),sizeof name);
//	printf("%d\n",strlen(PRAISE));//strlen函数是12.c中 my_ponds_status（）函数吗？函数返回值？ 
//	printf("and occupies %zd memery cells.\n",sizeof PRAISE);//%zd的用途？
//	return 0;
//    
//}

//Macro
//#include <stdio.h>
//#define PI 3.14159
//int main(void)
//{
//	float area,circum,radius;
//	printf("what's is the radius of your pizza?");
//	scanf("%f",&radius);
//	printf("the circum of the pizza is %.2f\n",PI * radius);
//	return 0;
// } 

//const limit a varible read-only(assigenment of read-only varible'MONTHES')
//#include <stdio.h> 
//int main(){
//	const int MONTHES = 12;//使用场景？
//	MONTHES = 13;
//	printf("%d\n",MONTHES);
//	return 0;
//}

//4.5defiens.c 明示常量 
//#include <stdio.h>
//#include <limits.h>//整型限制
//#include <float.h>//浮点型限制
//int main(void)
//{
//	printf("some number limits for this sysetem:\n");
//	printf("biggest int:%d\n",INT_MAX);
//	printf("smallest long long:%lld\n",LLONG_MIN);
//	printf("One byte = %d bits on this system.\n",CHAR_BIT);
//	printf("Largest double:%e\n",DBL_MAX);
//	printf("smallest normal float:%e\n",FLT_MIN);
//	printf("float precision = %d digits\n",FLT_DIG);
//	printf("float epsilon = %e\n",FLT_EPSILON);
//	return 0;
// } 

//单字符IO
//8.1 echo.c
//#include <stdio.h>
//#include <unistd.h>
//int main(void)
//{
//	char ch;
//	//while((ch = getchar() ) != '#'); //'\n' analysis the diffent of the both 
//	while((ch = getchar()) != EOF);//EOF is a singel  
//	putchar(ch);
//	return 0;
//}

//file_eof.c open a file and screen it
//#include <stdio.h>
//#include <stdlib.h>
//int main(void)
//{
//	int ch;
//	FILE *fp;
//	char fname[50];//storage file names 
//	printf("enter the name of the file:");
//	scanf("%s",fname);
//	fp = fopen(fname,"r");//open the file which will read
//	if(fp == NULL) //if false
//	{
//		printf("Failed to open file. Bye\n");
//		exit(1);//exit program ,exit(1) and return 1
//	}
//	while((ch = getc(fp) != EOF))
//	putchar(ch);
//	fclose(fp);//close the file
//	return 0;
// } 

//showchar1.c have a large problem of the io program
#include <stdio.h>
void diplay(char cr,int lines,int width);


int main(void)
{
	int ch;//the characters which will print later
	int rows,cols;//rows and columns
	printf("enter a character and two integers\n");
	which((ch = getchar()) != '\n')
	{
		scanf("%d %d",&rows,&cols);
		display(ch,rows,cols)
	}
	
}

