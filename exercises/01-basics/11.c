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

// 9.1 lethead1.c 
//  #include <stdio.h>
//  #define NAME "GIGATHINK, INC."
//  #define ADDRESS "101 Megabuck Plaza"
//  #define PLACE "Megapolis, CA 94904"
//  #define WIDTH 40
//
//  void starbar(void)
//  {
//      for(int count = 1;count < WIDTH;++count)
//    {
//      putchar('*');
//    }//或者在这里不写大括号 单人座
//      putchar('\n');
//    
//  }
//
//  int main(void){
//    starbar();
//  printf("%s\n", NAME);
//  printf("%s\n", ADDRESS);
//  printf("%s\n", PLACE);
//    starbar();
//  }

// #include <stdio.h>
// int main(void){
//   int a = 0;//初始化变量
//   int b = 0;
//   printf("Enter a integer:\n");
//   if (scanf("%d%d",&a,&b) != 2)//检查输入是否合法
//   {
//    printf("Error: Invalid input. Please enter a number.\n");
//    return 1;
//   };
//   printf("You entered: %d\n",a+b);
//   return 0;
//
// }

//putchar与getchar逻辑实现（缓冲区）
//my_putchar
#include <stdio.h>
#include <unistd.h>
#define BUF_SIZE 8

char buf[BUF_SIZE];//int buf[BUF_SIZE];
int buf_len = 0;

void my_flush(void) {
    if (buf_len > 0) {
        write(1, buf, buf_len);
        buf_len = 0;
    }
}

int my_putchar(int c)//1.char 在传参时会提升成 int，2.可以表示EOF等特殊字符
{
  buf[buf_len] = c;
  buf_len++;

  if (buf_len == BUF_SIZE || c == '\n')
  {
    write(1,buf,buf_len);//1(文件描述符)，在linux unix中0 stdin q stdout 2 stderr
    //buf（缓冲区指针）；指向内存中存放数据的起始地址，即要发送出去的数据在哪里
    //buf_len（写入字节数）；告  诉系统从buf中连续取多少字节写入
    buf_len = 0;
  }
  return c;
}
//局部变量会遮蔽同名全局变量

int main(void){
  char a = '\0';//char a;
  while(a != '\n'){//while (scanf("%c",&a) == 1)
    if (scanf("%c",&a) != 1)//if (a == '\n') break
    {
    printf("Error: Invalid input. Please enter a char.\n");
    return 1;
    }
  my_putchar(a);
  }
  // my_putchar('H');//my_putchar(72); mu_putchar('7');
  // my_putchar('i');
  // my_putchar('\n');
  my_flush();
  return 0;
}


//my_putchar逻辑实现
#include <unistd.h>
#define BUF_SIZE 8;

char buf[BUF_SIZE];
int buf_len = 0;

void my_flush(void)
{
  if(buf_len > 0)
  {
    write(1,buf,buf_len);
    buf_len = 0;
  }
}

int my_putchar(int c)
{
   buf[buf_len] = c;
   buf_len++;

 if(buf_len == BUF_SIZE || c == '\n')
  {
    write(1,buf,buf_len);
    buf_len = 0;
  }
  return c;
}

int main(void){
  char a = '\0';
  while(a = getchar() != '\n' && a != EOF)
  {
    my_putchar(a);
  }
  my_flush();
  return 0;
}
//重写
// #include <stdio.h>
// #include <unistd.h>
// #define BUF_SIZE 8
//
// char buf[BUF_SIZE];
// int buf_len = 0;
//
// void my_flush(void)
// {
//   if(buf_len > 0){
//     write(1,buf,buf_len);
//     buf_len = 0;
//   }
// }
//
// int my_putchar(int c)
// {
//   buf[buf_len] = c;
//   buf_len++;
//   if (buf_len == BUF_SIZE || c == '\n')问题1，这里需用比较（关系）运算符而非赋值运算符
//   {
//     write(1,buf,buf_len);
//     buf_len = 0;
//   }
//   return c;
// }
//
// int main(void){
//  my_putchar('H');
//  my_flush();
//   return 0;
// }


