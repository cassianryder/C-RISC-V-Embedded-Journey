// 04-data-structures/1.c
// 这份文件讲什么：
// 1. 用数组保存一组固定规模的数据
// 2. 观察部分初始化、编译器补零、指定初始化等数组组织方式
// 3. 作为从 basics 走向“数据如何组织”的最小入口

#include <stdio.h>

#define MONTHS 12

int main(void)
{
    int days[MONTHS] = {31, 28, [4] = 31, 30, 31, [1] = 29};
    int i;

    for (i = 0; i < MONTHS; i++)
        printf("Month %2d: %2d days\n", i + 1, days[i]);

    return 0;
}
