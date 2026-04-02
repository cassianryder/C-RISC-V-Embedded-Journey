//流程控制语句
#include <stdio.h>
int main(void){
 // int a,b;
 // scanf("%d%d",&a,&b);
 // printf("a+b=%d\n",a+b);

 // int Npchp;
 //  scanf("%d",&Npchp);
 //  if (Npchp < 0)
 //  {
 //    printf("npc Die\n");
 //    // printf("掉落道具\n");
 //  }
 //  else 
 //  printf("npc att\n");

// int val;//if else if 语句
// scanf("%d",&val);
  // if(val > 90){
  //   printf ("A\n");
  // }
  // else if(val > 70){
  //   printf("B\n");
  // }
  // else{
  //   printf("C\n");
  // }

  // if(val <= 90)//if else嵌套
  // {
  //   if(val <= 70)
  //   {
  //     printf("C\n");
  //   }
  //   else
  //   {
  //     printf("B\n");
  //   }
  // }
  // else
  // {
  //   printf("A\n");
  // }
  
  //switch 语句
  int val;
  scanf("%d",&val);
  // switch(val)
  // {
  //   case 9:
  //   printf("A\n");
  //   case 8:
  //   printf("B\n");
  //   default:
  //   break;
  //  }
 switch(val / 10)
  {
    default:
    printf("C\n");
    break;
    case 9:
    printf("A\n");
    printf("999\n");
    break;
    case 8:
    case 7:
    printf("B\n");
    break;
  }
  return 0;
}
