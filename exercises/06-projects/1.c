// 06-projects/1.c
// 这份文件讲什么：
// 1. 这是池塘记录 CLI 的项目种子版本
// 2. 用结构体承载一条记录，并把输入和输出分别封装
// 3. main 只负责循环控制和收尾，后续可继续加阈值常量、CSV、日志

#include <stdio.h>

typedef struct
{
    float temp;
    float oxygen;
    char pond_id;
} PondRecord;

void print_temp_status(float temperature)
{
    if (temperature < 24.5)
        printf("temp:%.1f(Low)", temperature);
    else if (temperature <= 28.1)
        printf("temp:%.1f(Normal)", temperature);
    else
        printf("temp:%.1f(High)", temperature);
}

void print_oxygen_status(float oxygen)
{
    if (oxygen < 5.0)
        printf("oxygen:%.1f(Low)", oxygen);
    else
        printf("oxygen:%.1f(Normal)", oxygen);
}

void print_pond_record(PondRecord record)
{
    printf("Pond %c", record.pond_id);
    putchar(' ');
    print_temp_status(record.temp);
    putchar(' ');
    print_oxygen_status(record.oxygen);
    putchar('\n');
}

int read_pond_record(PondRecord *record)
{
    printf("Enter temp, oxygen and id (q to quit):\n");
    return scanf("%f %f %c", &record->temp, &record->oxygen, &record->pond_id) == 3;
}

int main(void)
{
    PondRecord record;

    while (read_pond_record(&record))
    {
        print_pond_record(record);
    }

    printf("Done\n");
    return 0;
}
