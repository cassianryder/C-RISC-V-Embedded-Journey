# ========= 基础配置 =========
CC = gcc
CFLAGS = -Wall -g
BIN_DIR = bin
BIN = $(BIN_DIR)/program

# ========= 【唯一维护区】=========
MAP = \
11:exercises/01-basics/1.c \
12:exercises/01-basics/2.c \
13:exercises/01-basics/3.c \
14:exercises/01-basics/4.c \
15:exercises/01-basics/5.c \
16:exercises/01-basics/6.c \
17:exercises/01-basics/7.c \
18:exercises/01-basics/8.c \
19:exercises/01-basics/9.c \
110:exercises/01-basics/10.c \
111:exercises/01-basics/11.c \
112:exercises/01-basics/12.c \
113:exercises/01-basics/13.c \
114:exercises/01-basics/14.c \
115:exercises/01-basics/15.c \
21:exercises/02-pointers/1.c \
31:exercises/03-structs/1.c \
51:exercises/05-embedded/1.c \
61:exercises/06-projects/1.c \
71:exercises/07-basics-add/1.c

# ========= 内部解析 =========
get_src = $(word 2,$(subst :, ,$(filter $1:%,$(MAP))))
IDS = $(foreach pair,$(MAP),$(word 1,$(subst :, ,$(pair))))

# ========= 核心执行函数 =========
# 👉 直接使用 $(call get_src,$1) 替代 Shell 变量，避开展开陷阱
define exec_template
	@mkdir -p $(BIN_DIR)
	@echo "🚀 [$2] $(call get_src,$1)"
	$(CC) $(CFLAGS) $(call get_src,$1) -o $(BIN)
	$3
endef

# ========= 自动生成逻辑 =========

# 👉 run
$(foreach id,$(IDS),\
  $(eval $(id): ; $(call exec_template,$(id),RUN,./$(BIN))))

# 👉 gdb (替换为 lldb)
$(foreach id,$(IDS),\
  $(eval $(id)g: ; $(call exec_template,$(id),GDB,lldb $(BIN))))

# 👉 asm
$(foreach id,$(IDS),\
  $(eval $(id)asm: ; \
    @echo "🔍 [ASM] $(call get_src,$(id))"; \
    $(CC) -S -O0 -fverbose-asm $(call get_src,$(id)) -o -))

# 👉 mem 
# ⚠️ 注意这里：必须使用 $$$$sp。
# eval 解析后变成 $$sp，Make 运行时变成 $sp 传给 Shell，最终 GDB 才能正确接收到 $sp
# 👉 mem (针对 macOS aarch64 的 LLDB 优化指令)
# 👉 mem (终极转义修复版)
$(foreach id,$(IDS),\
  $(eval $(id)mem: ; $(call exec_template,$(id),MEM,\
    lldb $(BIN) -o "b main" \
                -o "run" \
                -o "register read" \
								-o 'memory read -s 8 -f x -c 12 $sp')))
# ========= 工具命令 =========

.PHONY: clean list help

help: list

list:
	@echo "🛠️ 可用命令格式:"
	@echo "  make <ID>      (例: make 18)    -> 编译并运行"
	@echo "  make <ID>g     (例: make 18g)   -> 启动 GDB 调试"
	@echo "  make <ID>asm   (例: make 18asm) -> 输出汇编代码"
	@echo "  make <ID>mem   (例: make 18mem) -> GDB 查看寄存器与栈"
	@echo ""
	@echo "📌 当前已配置的 ID 列表:"
	@echo "  $(IDS)"

clean:
	@echo "🧹 正在清理..."
	rm -rf $(BIN_DIR) build
