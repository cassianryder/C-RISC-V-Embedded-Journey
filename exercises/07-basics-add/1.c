// 07-basics-add/1.c
// 这份文件讲什么：
// 1. 递归调用中的栈帧展开
// 2. 同名局部变量在不同调用层中的地址区别
// 3. 作为 basics 之后的补强型练习

#include <stdio.h>

void up_and_down(int n)
{
    printf("Level %d: n location %p\n", n, &n);
    if (n < 4)
        up_and_down(n + 1);
    printf("LEVEL %d: n location %p\n", n, &n);
}

int main(void)
{
    up_and_down(1);
    return 0;
}
