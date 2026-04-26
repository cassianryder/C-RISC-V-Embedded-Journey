// 02-pointers/1.c
// 这份文件讲什么：
// 1. 指针作为函数参数时，如何通过地址修改实参
// 2. 数组作为函数参数时会退化成指针
// 3. 值传递与址传递的区别

#include <stdio.h>

void swapInt(int *a, int *b)
{
    int c = *a;
    *a = *b;
    *b = c;
}

void testFun(void)
{
    printf("testFun\n");
}

void printfArr(int a[10], int n)
{
    for (int i = 0; i < n; ++i)
        a[i] += 100;
}

int main(void)
{
    int num1 = 10;
    int num2 = 5;
    int arr[10] = {1, 2, 3, 4, 5, 6, 7, 8, 9, 10};

    swapInt(&num1, &num2);
    testFun();

    printf("%lu\n", sizeof(arr));

    printfArr(arr, 10);
    for (int i = 0; i < 10; ++i)
        printf("%d\n", arr[i]);

    printf("num1 = %d,\t num2 = %d\n", num1, num2);
    return 0;
}
