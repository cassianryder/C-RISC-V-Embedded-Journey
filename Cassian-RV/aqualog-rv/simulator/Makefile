CC = gcc
CFLAGS = -Wall -Wextra -pedantic -std=c11

TARGET = aqualog
SRC_DIR = src

SOURCES = \
	$(SRC_DIR)/main.c \
	$(SRC_DIR)/sensor_temp.c \
	$(SRC_DIR)/sensor_ph.c \
	$(SRC_DIR)/sensor_do.c \
	$(SRC_DIR)/sensor_turbidity.c \
	$(SRC_DIR)/sensor_water_level.c \
	$(SRC_DIR)/sensor_hub.c \
	$(SRC_DIR)/alert.c \
	$(SRC_DIR)/logger.c

all: $(TARGET)

$(TARGET): $(SOURCES)
	$(CC) $(CFLAGS) -o $(TARGET) $(SOURCES)

clean:
	rm -f $(TARGET) aqualog.csv
