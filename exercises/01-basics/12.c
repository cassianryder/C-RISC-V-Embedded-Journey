// #include <stdio.h>
// #include <unistd.h>
//
// #define BUF_SIZE 8
//
// char buf[BUF_SIZE];
// int buf_len = 0;
//
// void my_flush(void)
// {
//     if (buf_len > 0)
//     {
//         write(1, buf, buf_len);
//         buf_len = 0;
// }
//
// int my_putchar(int c)
// {
//     buf[buf_len] = (char)c;
//     buf_len++;
//
//     if (buf_len == BUF_SIZE || c == '\n')
//     {
//         write(1, buf, buf_len);
//         buf_len = 0;
//     }
//
//     return c;
// }

// int my_puts(const char *s)
// {
//     int count = 0;
//
//     while (s[count] != '\0')
//     {
//         my_putchar(s[count]);
//         count++;
//     }
//
//     my_putchar('\n');
//     return count;
// }
//

// int main(void)
// {
//     my_puts("Aqua shrimp monitor");
//     my_puts("temperature normal");
//     my_puts("oxygen low");
//     my_flush();
//     return 0;
// }


//my_putchar逻辑实现
#include <unistd.h>
#include <stdio.h>
#define BUF_SIZE 8

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

int my_puts(const char *s)
{
  int count = 0;
  while(s[count] != '\0')
  {
    my_putchar(s[count]);
    count++;
  }
  my_putchar('\n');
  return count;
}

int my_putstr(const char *s)//单行输出版本
{
  int count = 0;
  while(s[count] != '\0')
  {
    my_putchar(s[count]);
    count++;
  }
   return count;
}

// void my_ponds_status(const char *pond_name,const char *temp,const char *oxygen)
// {
//   // my_puts(pond_name);
//   // my_puts(temp);
//   // my_puts(oxygen);
//   my_putstr(pond_name);
//   my_putchar(' ');
//   my_putstr(temp);
//   my_putchar(' ');
//   my_putstr(oxygen);
//   my_putchar('\n');
// }

int my_ponds_status(const char *pond_name,const char *temp,const char *oxygen)
{
  int total = 0;
  total += my_putstr(pond_name);
  my_putchar(' ');
  total += 1;

  total += my_putstr(temp);
  my_putchar(' ');
  total += 1;

  total += my_putstr(oxygen);
  // my_putchar('\n');
  total += 1;

  return total;
  
}

void print_temp_status(float temperature)
{
  if (temperature < 24.5) my_putstr("temp:Low");
  else if (24.5 <= temperature  && temperature <= 28.1) my_putstr("temp:Normal");//c的语法else if 
  else  my_putstr("temp:High");
}

void print_oxygen_status(float oxygen)
{
  if (oxygen < 5.0) my_putstr("oxygen:Low");
  else my_putstr("oxygen:Normal");
}

int main(void){
  int len = 0;
  // int a = '\0';
  // while((a = getchar()) != '\n' && a != EOF)//EOF为什么不加'',宏定义（Macro）是什么？
  // {
  //   my_putchar(a); 
  // }
  // my_putchar('\n');
  float temp,oxygen;
  if((scanf("%f %f",&temp,&oxygen)) != 2)
  {
    printf("please enter a vaild value!!!");
    return 1;
  }
  // my_ponds_status("pondA","",""); 
  my_putstr("pond A");
  my_putchar(' ');
  print_temp_status(temp);
  my_putchar(' ');
  print_oxygen_status(oxygen);
  my_putchar('\n');

  // my_puts("pondA Temp:27");
  // my_puts("oxygen Low");
  //
  // len = my_ponds_status("pond A","Temp:21 C","oxygen:5.0mg/L");
  // printf("line chars = %d\n",len);
  my_putchar('\n');

  my_flush();
  return 0;
}

