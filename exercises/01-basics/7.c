//跳转语句,break continue goto
#include <stdio.h>
int main(void){
  // for(int i = 0;i < 10;i++)
  // {
  //   if(i == 5)
  //   {
  //     printf("找到了\n");
  //     break;//break 跳出循环
  //   }
  //   printf("%d\n",i);
  // }
  
  // int i = 0;
  // while (i < 10)
  // {
  //   {
  //   printf("找到了\n");
  //   break;
  //   }
  // printf("%d\n",i++);//printf("%d\n",i); i++/++i;
  // }

  // for (int i = 0;i < 10;i++)
  // {
  //   if(i % 2 != 0)
  //   {
  //     printf("%d\n",i);
  //   }
  // 
  

  // for(int i = 0;i < 10;i++)
  // {
  //   if(i % 2 == 0)
  //   {
  //     continue;//使用continue结束这次循环进入下一次循环,跳转语句，在for中先到表达式3，在回到循环条件处
  //   }
  //   printf("%d\n",i);
  // }
  

//   int i = 0;
// MYLAB:
//   printf("%d\n",++i);
//   goto MYLAB;//goto谨慎使用

// int isFind = 0;

// for(int i = 0;i < 4;i++)
//   {
//   for(int j = 0;j <= 10;j++)
//     {
//       if(i == 2 && j == 5)//跳出内循环
//       {
//       // isFind = 1;
//       // break;
//       goto MYOVER;
//       }
//       printf("%d\n",i * 10 + j);
//     }
//     // if(isFind)
//     //   break;
//   }
// MYOVER:
 

  return 0;
}
