# 子比主题 (zibll) - 项目说明文档

## 📋 项目概述

**子比主题 (zibll)** 是一款专为博客、自媒体、资讯类网站设计的 WordPress 主题。采用简约优雅的设计风格，提供创新的前端模块化功能配置和全面的用户功能，支持快捷支付功能。

- **主题名称**: 子比主题
- **版本**: 2.2
- **开发者**: 倾微科技-Qinver
- **官方网站**: https://www.zibll.com
- **设计风格**: 简约优雅、扁平化、响应式

---

## 🎯 核心特性

### 1. 基础功能
- ✅ 响应式设计，支持多设备访问
- ✅ 中文界面优化
- ✅ SEO 深度优化（含专题 SEO）
- ✅ 熊掌号支持
- ✅ 百度资源主动推送
- ✅ 夜间/日间主题切换

### 2. 用户系统
- ✅ 完整的前端用户中心
- ✅ 社会化登录（QQ、微信、微博、GitHub）
- ✅ 邮箱验证码注册
- ✅ 前端修改邮箱/昵称
- ✅ 社交账号绑定与解绑
- ✅ 禁止重复昵称检测

### 3. 内容管理
- ✅ 前端发布文章
- ✅ 增强编辑器（支持 Markdown）
- ✅ 古腾堡块扩展
- ✅ 文章格式选择（画廊、图片等）
- ✅ 文章列表 AJAX 加载
- ✅ 投稿审核功能

### 4. 支付系统 (v2.1+)
- ✅ 付费阅读功能
- ✅ 付费下载功能
- ✅ 支付宝企业支付接口
- ✅ 企业 H5 支付
- ✅ 商品购买通知邮件
- ✅ 评论/投稿审核通知邮件

### 5. AI 智能助手 (新增)
- ✅ AI 对话功能（支持 OpenAI、DeepSeek 等）
- ✅ 知识库管理系统
- ✅ RAG 检索增强
- ✅ 前端聊天窗口
- ✅ 后台管理界面

### 6. 互动功能
- ✅ 评论系统（支持 AJAX）
- ✅ 海报分享功能（Canvas 绘图）
- ✅ 外链重定向加密
- ✅ 图片灯箱（支持键盘切换、滑动关闭）
- ✅ 站内搜索优化

### 7. 小工具模块
- ✅ 文章幻灯片小工具
- ✅ 多栏目文章展示
- ✅ 最近评论小工具
- ✅ 用户列表小工具
- ✅ 链接列表小工具
- ✅ 公告栏小工具
- ✅ 搜索小工具
- ✅ 侧栏随动功能

---

## 📁 目录结构

```
/workspace/
├── functions.php              # 主功能文件
├── style.css                  # 主题样式定义
├── header.php                 # 头部模板
├── footer.php                 # 底部模板
├── index.php                  # 首页模板
├── single.php                 # 文章页模板
├── page.php                   # 页面模板
├── archive.php                # 归档页模板
├── category.php               # 分类页模板
├── tag.php                    # 标签页模板
├── search.php                 # 搜索页模板
├── author.php                 # 作者页模板
├── comments.php               # 评论模板
├── sidebar.php                # 侧边栏模板
├── 404.php                    # 404 错误页
│
├── action/                    # AJAX 动作处理
│   ├── action.php
│   ├── comment.php
│   ├── user.php
│   └── ...
│
├── framework/                 # 主题框架核心
│   ├── code/
│   ├── css/
│   ├── includes/
│   ├── js/
│   └── options-framework.php
│
├── functions/                 # 功能模块
│   ├── ai-core.php           # AI 核心功能
│   ├── ai-admin.php          # AI 后台管理
│   ├── ai-frontend.php       # AI 前端展示
│   ├── zib-ai.php            # 原有 AI 模块
│   ├── zib-comments-list.php # 评论列表
│   ├── zib-content.php       # 内容处理
│   ├── zib-header.php        # 头部处理
│   ├── zib-single.php        # 文章页处理
│   └── ...
│
├── zibpay/                    # 支付系统模块
│   ├── class/
│   ├── sdk/
│   ├── shop/
│   └── page/
│
├── oauth/                     # 社会化登录
│   ├── qq/
│   ├── weibo/
│   ├── weixin/
│   └── github/
│
├── widgets/                   # 小工具模块
│   ├── widget-index.php
│   ├── widget-posts.php
│   ├── widget-slider.php
│   └── ...
│
├── template/                  # 自定义模板
│   ├── category-dosc.php
│   ├── category-topics.php
│   └── ...
│
├── pages/                     # 特殊页面模板
│   ├── archives.php
│   ├── newposts.php
│   └── resetpassword.php
│
├── css/                       # 样式文件
│   ├── main.css
│   ├── bootstrap.min.css
│   ├── ai-chat.css
│   └── ...
│
├── js/                        # JavaScript 文件
│   ├── main.js
│   ├── ai-chat.js
│   ├── comment.js
│   └── ...
│
├── img/                       # 图片资源
│   ├── logo.png
│   ├── favicon.png
│   └── ...
│
├── fonts/                     # 字体文件
├── vendor/                    # 第三方依赖库
└── yiyan/                     # 一言功能模块
```

---

## 🚀 安装步骤

### 1. 环境要求
- WordPress 5.0+
- PHP 7.4+
- MySQL 5.6+

### 2. 安装方法
1. 将主题文件夹上传至 `/wp-content/themes/` 目录
2. 在 WordPress 后台 → 外观 → 主题 中激活"子比主题"
3. 进入"子比主题"选项面板进行配置

### 3. AI 功能初始化
激活主题后，系统会自动创建 AI 知识库数据表：
- 表名：`wp_zib_ai_knowledge`
- 包含字段：ID、标题、内容、分类、标签、状态、时间戳

---

## ⚙️ 配置指南

### AI 功能配置
1. 登录 WordPress 后台
2. 进入 **子比主题 > AI 与知识库**
3. 填写配置：
   - API 密钥（OpenAI/DeepSeek 等）
   - API 端点地址
   - 模型名称（如 `gpt-3.5-turbo`）
   - 最大 Token 数
   - 系统提示词
   - 启用知识库选项

### 支付功能配置
1. 进入 **子比主题 > 支付设置**
2. 配置支付宝/微信支付参数
3. 设置商品价格和内容可见性

### 社会化登录配置
1. 进入 **子比主题 > 登录设置**
2. 填写各平台 AppID 和 AppSecret
3. 配置回调地址

---

## 📝 更新日志摘要

### v2.2 (当前版本)
- 修复多项安全漏洞（OSS、SQL 注入）
- 新增支付宝企业支付接口
- 新增邮箱验证码注册
- 优化海报分享功能（减少 80% 代码量）
- 增加 5 种幻灯片切换动画

### v2.1
- 全新支付系统（付费阅读/下载）
- 搜索功能增强
- 图片灯箱优化
- 古腾堡块扩展

### v2.0
- 专题 SEO 深度优化
- 百度资源自动推送
- GitHub 登录功能
- 侧栏随动功能
- 日间/夜间 Logo 切换

---

## 🔒 安全注意事项

1. **API 密钥保护**: 不要在前端暴露敏感信息
2. **权限控制**: 仅管理员可配置核心功能
3. **内容审核**: 建议监控 UGC 内容
4. **定期更新**: 及时应用安全补丁
5. **速率限制**: 注意第三方 API 调用频率

---

## 🛠️ 故障排除

### AI 不回复
- 检查 API 密钥和端点是否正确
- 确认模型名称有效
- 查看网络连接状态

### 支付功能异常
- 验证商户配置信息
- 检查回调地址设置
- 确认 SSL 证书有效

### 前端显示问题
- 清除浏览器缓存
- 检查 JavaScript 冲突
- 验证主题文件完整性

---

## 📞 技术支持

- **官方网站**: https://www.zibll.com
- **开发者**: 倾微科技-Qinver
- **文档**: 参考主题内置帮助文档

---

## 📄 许可证

本主题基于 WordPress GPL 协议发布。

---

**最后更新**: 2024 年  
**兼容版本**: WordPress 5.0+, PHP 7.4+
