// 06-projects/1.c
// 这份文件讲什么：
// 1. 这是池塘记录 CLI 的项目种子版本
// 2. 用结构体承载一条记录，并把输入和输出分别封装
// 3. main 只负责循环控制和收尾，后续可继续加阈值常量、CSV、日志

#include <stdio.h>
#include <time.h>

#define TEMP_LOW_LIMIT 24.5f
#define TEMP_HIGH_LIMIT 28.1f
#define OXYGEN_LOW_LIMIT 5.0f
#define TIMESTAMP_SIZE 20

//数据层
typedef struct
{
    char sampled_at[TIMESTAMP_SIZE];
    float temp;
    float oxygen;
    char pond_id;
  } PondRecord;

int fill_record_timestamp(PondRecord *record)
{
    time_t now = time(NULL);
    struct tm *local = localtime(&now);

    if (local == NULL)
    return 0;

    return strftime(record->sampled_at,TIMESTAMP_SIZE,"%Y-%m-%d %H:%M:%S",local) > 0;
}


//输入层
int read_pond_record(PondRecord *record)
{
    printf("Enter temp, oxygen and id (q to quit):\n");
    // return scanf("%f %f %c", &record->temp, &record->oxygen, &record->pond_id) == 3;
    if (scanf("%f %f %c",&record->temp,&record->oxygen,&record->pond_id) != 3)
    return 0;
    return fill_record_timestamp(record);
}


//判断层：返回状态文本
const char *temp_status(float temperature)
{
  if (temperature < TEMP_LOW_LIMIT)
    return "low";
  else if (temperature <= TEMP_HIGH_LIMIT)
      return "normal";
  else 
    return "high";
}

const char *oxygen_status(float oxygen)
{
  if (oxygen < OXYGEN_LOW_LIMIT)
    return "low";
  else 
    return "normal";
}


//打印状态
void print_temp_status(float temperature)
{
//     if (temperature < TEMP_LOW_LIMIT) //v_1:
//         printf("temp:%.1f(Low)", temperature);
//     else if (temperature <= TEMP_HIGH_LIMIT)
//         printf("temp:%.1f(Normal)", temperature);
//     else
//         printf("temp:%.1f(High)", temperature);

       // if (temp_status(temperature) == "low" )//v_2
       //    printf("temp:%.1f(Low)",temperature);
       // else if(temp_status(temperature) == "normal")
       //    printf("temp:%.1f(Normal)",temperature);
       // else 
       //    printf("temp:%.1f(High)",temperature);
       
         printf("temp:%.1f(%s)",temperature,temp_status(temperature));//v_3
 }

void print_oxygen_status(float oxygen)
{
    // if (oxygen < OXYGEN_LOW_LIMIT)//v_1
    //     printf("oxygen:%.1f(Low)", oxygen);
    // else
    //     printf("oxygen:%.1f(Normal)", oxygen);

    // if (oxygen_status(oxygen) == "low")//v_2
    // printf("oxygen:%.1f(Low)",oxygen);
    // else 
    // printf("oxygen:%.1f(Normal)",oxygen);
   
       printf("oxygen:%.1f(%s)",oxygen,oxygen_status(oxygen));//v_3
}

//输出层
void print_pond_record(PondRecord record)
{
    printf("[%s] Pond %c", record.sampled_at,record.pond_id);
    putchar(' ');
    print_temp_status(record.temp);
    putchar(' ');
    print_oxygen_status(record.oxygen);
    putchar('\n'); 
}

int save_pond_record_csv(PondRecord record)
{
    FILE *fp = fopen("pond_records_csv","a");
    
    if (fp == NULL)
    return 1;

    fprintf(fp, "%s,%c,%.1f,%s,%.1f,%s\n",
            record.sampled_at,
            record.pond_id,
            record.temp,
            temp_status(record.temp),
            record.oxygen,
            oxygen_status(record.oxygen));
    fclose(fp);
    return 0;
}


//流程层
int main(void)
{
    PondRecord record;

    while (read_pond_record(&record))
    {
        print_pond_record(record);
    }
    save_pond_record_csv(record);
    printf("Done\n");
    return 0;
}
