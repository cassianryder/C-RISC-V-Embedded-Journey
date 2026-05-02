// 03-structs/1.c
// 这份文件讲什么：这是一个结构体的样版代码
// 1. 用结构体把 temp、oxygen、pond_id 封装成一条记录
// 2. 结构体作为函数参数，减少 main 的混乱度
// 3. 为下一步 read_pond_record(PondRecord *record) 做准备,read_pond_record(PondRecord *record）已完成

#include <stdio.h>

//数据层
typedef struct
{
    float temp;
    float oxygen;
    char pond_id;
} PondRecord;//type

//判断
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

//输出
void print_pond_record(PondRecord record)
{
    printf("Pond %c", record.pond_id);
    putchar(' ');
    print_temp_status(record.temp);
    putchar(' ');
    print_oxygen_status(record.oxygen);
    putchar('\n');
}

//输入
int read_pond_record(PondRecord *record)
{
  printf("Enter temp, oxygen and id (q to quit):\n");
  return scanf("%f %f %c",&record->temp,&record->oxygen,&record->pond_id) == 3;

}

//主逻辑
int main(void)
{
    PondRecord record;//变量类型
    // printf("%lu\n", sizeof(PondRecord));
    // printf("Enter temp, oxygen and id (q to quit):\n");
    // while (scanf("%f %f %c", &record.temp, &record.oxygen, &record.pond_id) == 3)
    // {
    //     print_pond_record(record);
    //     printf("Enter temp, oxygen and id (q to quit):\n");
    // }
    
    while(read_pond_record(&record))
  {
    print_pond_record(record);
  }
    printf("Done\n");
    return 0;
}
