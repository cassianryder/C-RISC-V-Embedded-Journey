// 01-basics/16.c
// 这份文件讲什么：
// 1. 这是 06-projects/1.c 的学习痕迹副本
// 2. 用来保留旧注释、探索过程和阶段性理解，不作为当前项目主线推进

#include <stdio.h>
#include <time.h>

#define TEMP_LOW_LIMIT 24.5f
#define TEMP_HIGH_LIMIT 28.1f
#define OXYGEN_LOW_LIMIT 5.0f
#define TIMESTAMP_SIZE 20
#define CSV_FILE_NAME "pond_records.csv"
#define OXYGEN_ALERT_FILE_NAME "oxygen_alert.csv"
#define CONTROL_LOG_FILE_NAME "control_log.csv"


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

int needs_aeration (PondRecord record)
{
  if (record.oxygen < OXYGEN_LOW_LIMIT)
      return 1;

  return 0;
}

// Convert water-quality state into an aerator control decision.
int aerator_should_be_on (PondRecord record)
{
  return needs_aeration(record);
}

const char *aerator_action (PondRecord record)
{
 if (aerator_should_be_on(record) == 1)
     return "ON";
 else
     return "OFF";
}

const char *aerator_reason (PondRecord record)
{
  if (needs_aeration(record) == 1)
      return "LOW_OXYGEN";
  else
      return "NORMAL_OXYGEN";
}

void print_aeration_action (PondRecord record)
{
  if (aerator_should_be_on(record))
      printf("Action: turn Pond %c aeration ON (oxygen %.1f < %.1f)\n",
             record.pond_id, record.oxygen, OXYGEN_LOW_LIMIT);
  else 
      printf("Action: keep Pond %c aeration OFF (oxygen %.1f >= %.1f)\n",
             record.pond_id, record.oxygen, OXYGEN_LOW_LIMIT);
}

void print_oxygen_alert (PondRecord record)
{
  if (needs_aeration(record))
      printf ("Alert: Pond %c low oxygen detected (oxygen %.1f < %.1f)\n",
               record.pond_id, record.oxygen, OXYGEN_LOW_LIMIT);
}

int save_control_log_csv (PondRecord record)
{
  FILE *fp = fopen (CONTROL_LOG_FILE_NAME,"a");

  if (fp == NULL)
      return 0;

  fseek (fp, 0, SEEK_END);
  long file_size = ftell (fp);

  if (file_size == 0)
  {
    fprintf(fp, "sampled_at,pond_id,actuator,action,reason\n");
  }

  fprintf (fp, "%s,%c,%s,%s,%s\n",
           record.sampled_at,
           record.pond_id,
           "AERATOR",
           aerator_action(record),
           aerator_reason(record));
  fclose (fp);
  return 1;
}

int save_oxygen_alert_csv (PondRecord record)
{
  if (!needs_aeration(record))
      return 1;

  FILE *fp = fopen (OXYGEN_ALERT_FILE_NAME, "a");

  if (fp == NULL)
    return 0;

  fseek (fp, 0, SEEK_END);
  long file_size = ftell (fp);

  if (file_size == 0)
  {
      fprintf (fp, "sampled_at,pond_id,event,oxygen,oxygen_low_limit\n");
  }

  fprintf (fp, "%s,%c,%s,%.1f,%.1f\n",
          record.sampled_at,
          record.pond_id,
          "LOW_OXYGEN",
          record.oxygen,
          OXYGEN_LOW_LIMIT);

  fclose (fp);
  return 1;
}

//打印状态
void print_temp_status (float temperature)
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
    // FILE *fp = fopen("pond_records_csv","a");
    FILE *fp = fopen(CSV_FILE_NAME, "a");

    if (fp == NULL)
        return 0;

    // Check whether this is a new CSV file before writing the header.
    fseek(fp, 0, SEEK_END);
    long file_size = ftell(fp);

    if (file_size == 0)
    {
        fprintf(fp, "sampled_at,pond_id,temp,temp_status,oxygen,oxygen_status\n");
    }

    fprintf(fp, "%s,%c,%.1f,%s,%.1f,%s\n",
            record.sampled_at,
            record.pond_id,
            record.temp,
            temp_status(record.temp),
            record.oxygen,
            oxygen_status(record.oxygen));
    fclose(fp);
    return 1;
}


//流程层
int main(void)
{
    PondRecord record;

    while (read_pond_record(&record))
    {
        print_pond_record(record);

    // if (needs_aeration(record) == 1)
    // {
    //     printf("Alert: Pond %c needs aeration\n",record.pond_id);
    // }
       print_aeration_action(record);
       print_oxygen_alert(record);
    // if (save_pond_record_csv(record) == 0)
    if (!save_pond_record_csv(record))
    {
        printf("Error: failed to save.\n");
    }

    if (!save_oxygen_alert_csv(record))
    {
        printf("Error: failed to save oxygen alert.\n");
    }
    if (!save_control_log_csv(record))
    {
    printf("Error: failed to save control log.\n");
    }

    }
    // save_pond_record_csv(record);
    printf("Done\n");
    return 0;
}

