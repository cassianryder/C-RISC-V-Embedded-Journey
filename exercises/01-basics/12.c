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
//     }
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
//
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
  while((a = getchar()) != '\n' && a != EOF)//EOF为什么不加'',宏定义（Marco）是什么？
  {
    my_putchar(a);
  }
  my_flush();
  return 0;
}

