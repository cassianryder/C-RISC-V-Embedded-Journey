// 05-embedded/1.c
// 这份文件讲什么：
// 1. 用自定义缓冲区实现最小字符输出系统
// 2. 接触 write()、文件描述符、字符流
// 3. 作为从 basics 走向 embedded / system I/O 的桥接文件

#include <stdio.h>
#include <unistd.h>

#define BUF_SIZE 8

char buf[BUF_SIZE];
int buf_len = 0;

void my_flush(void)
{
    if (buf_len > 0)
    {
        write(1, buf, buf_len);
        buf_len = 0;
    }
}

int my_putchar(int c)
{
    buf[buf_len] = (char)c;
    buf_len++;

    if (buf_len == BUF_SIZE || c == '\n')
    {
        write(1, buf, buf_len);
        buf_len = 0;
    }

    return c;
}

int main(void)
{
    char a = '\0';

    while (a != '\n')
    {
        if (scanf("%c", &a) != 1)
        {
            printf("Error: Invalid input. Please enter a char.\n");
            return 1;
        }
        my_putchar(a);
    }

    my_flush();
    return 0;
}
