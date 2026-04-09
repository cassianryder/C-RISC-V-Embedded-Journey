//函数形参与实参
#include <stdio.h>

// void swapInt(int a,int b)//函数定义时参数列表中的参数位形式参数，a和b为形式参数，代指一个类型的变量
// //用处：在函数体中，需要去描述一个数据变量的流程，但这个定义又没有具体的数据，所以需要一个形式参数去表现在函数体中变量的变量过程
// {
//   int c = a;
//       a = b;
//       b = c;
// }

void swapInt(int *a,int *b)
{
  int c = *a;
      *a = *b;
      *b = c;
}

void testFun(void)//无参，在c语言中要用void表示无参，无参就没有形参和实参之分
{
  printf("testFun\n");
}

void printfArr(int a[10], int n)//数组作为函数的操作时,传入的数组名其实是数组的首地址，因此可以省略数组的最高维
{
 // printf("a:size = %lu\n",sizeof(a));//数组作为函数参数时会退化成指针
 for(int i = 0;i < n;++i)
    a[i] += 100;
}

int main(void){
  int num1 = 10;
  int num2 = 5;
  // int tempVal = num1;
  // num1 = num2;
  // num2 = tempVal;
  // swapInt(num1,num2);//实际参数，表示实际操作的是哪些参数
  swapInt(&num1,&num2);
  testFun();//在调用是也无需实参列表
  
  int arr[10] = {1,2,3,4,5,6,7,8,9,10};
  printf("%lu\n",sizeof(arr));
  
  printfArr(arr,10);
  for(int i = 0;i < 10;++i)
    printf("%d\n",arr[i]);

  printf("num1 = %d,\t num2 = %d\n",num1,num2);//实参传递给形参时会出现拷贝现象，num1传给a会把num1的值拷贝给a
  //值传递，实参传递给形参，传递值（形参做的操作并不改变实参）
  //址传递，传递的是一个地址，在函数的内部通过地址的方式去修改实参
  return 0;
}
