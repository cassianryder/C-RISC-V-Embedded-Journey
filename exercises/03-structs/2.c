// 03-structs/2.c
// 这份文件讲什么：
// 1. 用地址观察结构体成员是如何组织在一条记录里的
// 2. 作为从“指针步长”走向“结构体成员地址”的桥接文件
// 3. 为后续理解 record.temp、record->temp、&record->temp 做铺垫

#include <stdio.h>

typedef struct
{
    float temp;
    float oxygen;
    char pond_id;
} PondRecord;

int main(void)
{
    PondRecord record = {27.5f, 4.2f, 'A'};

    printf("&record          = %p\n", &record);
    printf("&record.temp     = %p\n", &record.temp);
    printf("&record.oxygen   = %p\n", &record.oxygen);
    printf("&record.pond_id  = %p\n", &record.pond_id);
    printf("sizeof(PondRecord) = %zu\n", sizeof(PondRecord));
    return 0;
}
