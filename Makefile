CC = gcc
CFLAGS = -Wall -g
BIN = bin/program

# --- 快捷映射区：在这里添加你的练习题简写 ---
# 格式: 简写: s = 路径
11: s = exercises/01-basics/1.c 
12: s = exercises/01-basics/2.c
13: s = exercises/01-basics/3.c 
14: s = exercises/01-basics/4.c 
15: s = exercises/01-basics/5.c
21: s = exercises/02-pointers/1.c
# ---------------------------------------

# 通用编译运行逻辑
# 当你输入 make 11 时，MAKECMDGOALS 就是 11
$(MAKECMDGOALS):
	@mkdir -p bin
	@if [ -z "$(s)" ]; then \
		echo "错误: 未定义的简写或路径。请在 Makefile 中检查映射。"; \
	else \
		echo "正在编译并运行: $(s)"; \
		$(CC) $(CFLAGS) $(s) -o $(BIN) && ./$(BIN); \
	fi

# 传统的 clean 依然保留
clean:
	rm -rf bin build

.PHONY: clean

