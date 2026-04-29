// 05-embedded/2.c
// 这份文件讲什么：
// 1. 观察不同类型指针做 +1 时地址步长为什么不同
// 2. 作为“按字节寻址”与“按类型步长移动”之间的桥接练习
// 3. 为以后理解结构体布局、缓冲区和嵌入式内存观察打底

#include <stdio.h>

#define SIZE 4

int main(void)
{
    short dates[SIZE];
    short *pti = dates;
    short index;
    double bills[SIZE];
    double *ptf = bills;

    printf("%23s %15s\n", "short", "double");
    for (index = 0; index < SIZE; index++)
        printf("pointers + %d: %10p %10p\n", index, pti + index, ptf + index);

    printf("sizeof(short)  = %zu\n", sizeof(short));
    printf("sizeof(double) = %zu\n", sizeof(double));
    return 0;
}
