//数组
#include <stdio.h>
int main(){
  int arr[3] = {1,2,3};//第一种初始化
  // int arr1[3] = {1,2};//第二种初始化，没有给出的为0值
  // int arr2[] = {1,2,3};//第三种初始化
  // printf("arr = %d\n", arr[2] );
  // printf("arr1 = %d\n",arr1[0]);
  // printf("arr2 = %d\n",arr2[1]);
  for(int i = 0;i < 3;++i)
  {
    printf("%d\t",arr[i]);
    printf("\n");
  }
  return 0;
}
