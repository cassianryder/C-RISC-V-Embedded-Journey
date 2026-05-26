#include "judge.h"
#include "control.h"


int control_should_aerate(PondRecord record)
{
 return needs_aeration(record);
}
