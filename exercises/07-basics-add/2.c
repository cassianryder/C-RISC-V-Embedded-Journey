// 07-basics-add/2.c
// 这份文件讲什么：
// 1. 观察部分初始化数组、未显式初始化元素的默认值
// 2. 补强 printf 宽度控制、字符串常量和数组输出观察
// 3. 通过 sizeof 求数组总字节数和元素个数

#include <stdio.h>

#define SIZE 4

int main(void)
{
    int some_data[SIZE] = {1492, 1066};
    int i;

    printf("%2s%14s\n", "i", "some_data[i]");
    for (i = 0; i < SIZE; i++)
        printf("%2d%14d\n", i, some_data[i]);

    printf("array bytes        = %zu\n", sizeof(some_data));
    printf("single item bytes  = %zu\n", sizeof(some_data[0]));
    printf("element count      = %zu\n", sizeof(some_data) / sizeof(some_data[0]));
    return 0;
}
