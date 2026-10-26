# 去水印 · 自建解析服务（short_videos）

这是一个 **PHP + Apache** 的 Docker 服务，基于 MIT 开源项目：
https://github.com/jiuhunwl/short_videos

- 不需要公众号认证
- 不需要 API key
- 支持抖音、快手、小红书
- 云函数直接调用

## 接口

健康检查：

```text
GET /health.php
```

抖音：

```text
GET /api/douyin/douyin.php?url=分享链接
```

小红书：

```text
GET /api/xiaohongshu/xhsjx.php?url=分享链接
```

快手：

```text
GET /api/kuaishou/ksjx.php?url=分享链接
```

## 本地测试（有 Docker 才需要）

```bash
docker compose up --build
```

打开：

```text
http://localhost:8080/health.php
```

## 部署到微信云托管

1. 打开微信云托管控制台
2. 新建服务
3. 部署方式选 **Git 平台部署**
4. 先把本目录上传到一个 GitHub 仓库（例如 `shuiyin-parser`）
5. 在云托管里授权 GitHub，选择这个仓库和 `main` 分支
6. Dockerfile 在根目录，端口填：

```text
80
```

7. 内存建议 512MB 或 1GB
8. 开启公网访问
9. 部署

部署完成后会得到一个 HTTPS 域名，例如：

```text
https://xxxx-xxxx.ap-shanghai.run.tcloudbase.com
```

先测：

```text
https://你的域名/health.php
```

返回：

```json
{"status":"ok","service":"short_videos"}
```

再测：

```text
https://你的域名/api/douyin/douyin.php?url=分享链接
```

## 云函数配置

云开发控制台 → 云函数 → parseLink → 配置 → 环境变量：

```text
PARSER_BASE_URL=https://你的云托管域名
PARSER_MODE=shortvideos
```

原来的 Cloudflare 地址删掉；`APIHZ_ID` / `APIHZ_KEY` 可以留着备用。

然后重新上传 `parseLink`：

```text
右键 cloudfunctions/parseLink
→ 上传并部署：云端安装依赖
```

## 注意

- 云托管按资源计费，低流量一般几十元/月
- 平台风控、Cookie 变化会导致个别链接解析失败
- 素材版权归原作者，商用需授权
- 这个服务只做解析，不保存视频文件
