// 02-pointers/2.c
// 这份文件讲什么：
// 1. 指针变量保存的是地址，* 用来根据地址访问原变量
// 2. 通过指针参数修改主调函数中的变量
// 3. 用 swap 示例吃透值传递和址传递的区别

#include <stdio.h>

void interchange(int *u, int *v)
{
    int temp;

    temp = *u;
    *u = *v;
    *v = temp;
}

int main(void)
{
    int x = 5;
    int y = 10;
    int *px = &x;

    printf("x = %d, &x = %p\n", x, &x);
    printf("px = %p, *px = %d\n", px, *px);

    printf("Originally x = %d and y = %d.\n", x, y);
    interchange(&x, &y);
    printf("Now x = %d and y = %d.\n", x, y);

    return 0;
}
