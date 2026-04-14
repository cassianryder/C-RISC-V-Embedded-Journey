# 养虾通部署方案

## 推荐架构

- 对外正式访问：`dadatun.online` -> Cloudflare -> 美国 VPS
- 中国广州 VPS：仅做 FRP 中转或开发调试，不作为面向客户的主站
- 家中 Mac mini：仅做开发环境、演示环境、临时联调

这样做的原因：

- 面向客户的正式站点更适合直接放在公网可达、配置稳定的美国 VPS
- Cloudflare 适合放在正式站点前做 HTTPS、代理、防护和缓存
- 中国大陆服务器如果直接对公众提供网站服务，通常需要备案；你现在的广州 VPS 更适合做内网穿透和开发用途，不适合当主站

## 域名规划

- `dadatun.online`：主站
- `www.dadatun.online`：跳转到主站
- `api.dadatun.online`：可选，后续单独放 API
- `preview.dadatun.online`：可选，临时演示或灰度环境

## 服务器建议

### 美国 VPS

部署内容：

- Nginx
- PHP 8.3+
- MySQL 8.x 或 MariaDB 10.11+
- 养虾通 `web-portal`

建议目录：

```text
/var/www/dadatun/aqualog-rv/web-portal
```

站点根目录：

```text
/var/www/dadatun/aqualog-rv/web-portal/public
```

### 广州 VPS

用途：

- FRP 服务端
- 临时预览环境转发
- 调试家中 Mac mini

不建议：

- 直接作为面向客户的正式主站

## Cloudflare 配置

### DNS

在 Cloudflare 中添加：

- `A` 记录：`dadatun.online` -> 美国 VPS 公网 IP
- `CNAME` 记录：`www` -> `dadatun.online`

正式上线时建议打开橙云代理。

### SSL/TLS

建议：

- SSL/TLS 模式使用 `Full (strict)`
- 在美国 VPS 上安装 Cloudflare Origin CA 证书

### 源站保护

建议额外开启：

- Authenticated Origin Pulls
- 防火墙只允许 Cloudflare 回源 IP 或配合源站校验

## Nginx 反向代理建议

站点思路：

- `public/` 作为根目录
- PHP 通过 `php-fpm` 处理
- `/api/` 路由保持可访问

核心要求：

- `index.php` 作为回退入口
- 静态资源长期缓存
- API 不缓存

## MySQL

建议在美国 VPS 本机部署数据库，避免初期引入跨机网络复杂度。

数据库建议：

- 数据库名：`aqualog_rv`
- 应用账号：单独创建，不使用 `root`
- 每天自动备份

## 上线步骤建议

1. 在美国 VPS 安装 `nginx + php-fpm + mysql`
2. 上传项目代码到服务器
3. 导入 `database/schema.sql`
4. 依次执行控制命令迁移脚本
5. 修改 `app/config.php` 的数据库地址、账号、密码
6. 配置 Nginx 站点根目录为 `web-portal/public`
7. 在 Cloudflare 生成 Origin CA 证书并安装到 Nginx
8. 在 Cloudflare 中把域名切到橙云代理
9. SSL/TLS 模式切到 `Full (strict)`
10. 验证 `dashboard.php`、`history.php`、`control.php`、`api/upload_telemetry.php`

## 临时上线给客户看

如果你只是临时演示，然后准备下线：

- 最稳的方案仍然是美国 VPS 正式部署
- 演示结束后：
  - 停止 `php-fpm`/Nginx 站点
  - 或在 Cloudflare 上把主域切到维护页
  - 或暂停 DNS 解析

不建议直接把家中 Mac mini 暴露为客户正式站点。

## 设备侧对接

当前项目已经有：

- `api/upload_telemetry.php`：设备上报水质采样
- `api/device_queue.php`：设备拉取待执行命令
- `api/device_ack.php`：设备回执命令状态

模拟设备消费者：

```bash
php scripts/milkv_device_consumer.php --base-url=http://127.0.0.1:8000 --pond-code=pond_01 --device-type=增氧机 --device-no=1
```

只跑一轮：

```bash
php scripts/milkv_device_consumer.php --base-url=http://127.0.0.1:8000 --pond-code=pond_01 --device-type=增氧机 --device-no=1 --once
```

## 你当前最合适的部署策略

当前阶段建议：

- 正式演示站部署在美国 VPS
- `dadatun.online` 和 `www.dadatun.online` 走 Cloudflare
- 广州 VPS + FRP + Mac mini 只保留给你自己调试

等你后面要做国内客户长期可访问版本，再单独规划备案、国内节点和合规架构。
