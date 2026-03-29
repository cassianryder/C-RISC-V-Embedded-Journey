CC = gcc
CFLAGS = -Wall -g
TARGET = bin/program

all: $(TARGET)

$(TARGET): exercises/01-basics/1.c
	mkdir -p bin
	$(CC) $(CFLAGS) $< -o $@

run: $(TARGET)
	./$(TARGET)

clean:
	rm -rf build bin
