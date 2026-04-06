# AI 与知识库功能说明

## 概述

已为子比主题添加了完整的 AI 智能助手和知识库功能，包括：

- ✅ AI 对话功能（支持 OpenAI、DeepSeek 等兼容 API）
- ✅ 知识库管理系统
- ✅ 后台管理界面
- ✅ 前端聊天窗口
- ✅ 知识库检索增强（RAG）

## 文件结构

```
functions/
├── ai-core.php        # AI 核心功能（配置、API 调用、知识库管理）
├── ai-admin.php       # 后台管理界面
└── ai-frontend.php    # 前端聊天窗口展示

functions/zib-ai.php   # 原有 AI 功能模块（已存在）
```

## 安装步骤

### 1. 自动加载（已完成）

已在 `functions-theme.php` 中添加了自动加载：

```php
require_once(get_theme_file_path('/functions/zib-ai.php'));
```

并在 `functions.php` 中添加了新模块：

```php
require get_stylesheet_directory() . '/functions/ai-core.php';
require get_stylesheet_directory() . '/functions/ai-admin.php';
require get_stylesheet_directory() . '/functions/ai-frontend.php';
```

### 2. 初始化数据库

激活主题或访问 WordPress 后台后，系统会自动创建知识库数据表：
- 表名：`wp_zib_ai_knowledge`
- 包含字段：ID、标题、内容、分类、标签、状态、创建时间、更新时间

## 使用方法

### 一、配置 AI 功能

1. 登录 WordPress 后台
2. 进入 **子比主题 > AI 与知识库**
3. 填写以下配置：

   - **API 密钥**：您的 AI 服务提供商的 API Key
     - OpenAI: `sk-...`
     - DeepSeek: `sk-...`
     - 其他兼容平台
   
   - **API 端点**：API 请求地址
     - OpenAI: `https://api.openai.com/v1/chat/completions`
     - DeepSeek: `https://api.deepseek.com/v1/chat/completions`
     - 其他平台请填写对应地址
   
   - **模型名称**：例如 `gpt-3.5-turbo`、`gpt-4`、`deepseek-chat`
   
   - **最大 Token 数**：单次回复的最大长度（建议 1000-2000）
   
   - **系统提示词**：设定 AI 的角色和行为
   
   - **启用知识库**：勾选后 AI 会先检索知识库再回答

### 二、管理知识库

#### 添加知识条目

1. 在 **AI 与知识库** 页面右侧点击 **添加知识条目**
2. 填写：
   - **标题**：知识的标题
   - **分类**：例如"常见问题"、"产品说明"、"使用教程"
   - **标签**：关键词，用逗号分隔
   - **内容**：详细的知识内容
   - **状态**：发布或草稿
3. 点击保存

#### 编辑/删除知识

- 在知识库列表中点击对应条目的 **编辑** 或 **删除** 按钮

### 三、测试 AI 对话

在管理页面右侧的 **测试对话** 区域：
1. 输入测试问题
2. 按回车或点击发送
3. 查看 AI 回复

### 四、启用前端聊天框

1. 需要手动在前端启用（代码已提供）
2. 在主题选项中找到 **AI 聊天框** 设置
3. 启用 **启用 AI 聊天框** 选项
4. 访客可在网站右下角看到聊天按钮

## API 兼容性

本功能支持所有兼容 OpenAI API 格式的服务商：

| 服务商 | API 端点 | 模型示例 |
|--------|---------|---------|
| OpenAI | https://api.openai.com/v1/chat/completions | gpt-3.5-turbo, gpt-4 |
| DeepSeek | https://api.deepseek.com/v1/chat/completions | deepseek-chat |
| Azure OpenAI | https://YOUR_RESOURCE.openai.azure.com/openai/deployments/YOUR_DEPLOYMENT/chat/completions | gpt-35-turbo |
| 本地部署 | http://localhost:11434/v1/chat/completions | llama2, qwen |

## 高级用法

### 1. 自定义系统提示词

示例提示词：
```
你是一个专业的 WordPress 网站客服助手。请基于知识库内容回答用户问题。
如果知识库中没有相关信息，请如实告知，并建议用户联系人工客服。
回答要简洁、友好、专业。
```

### 2. 知识库分类建议

- **常见问题**：FAQ、常见问题解答
- **产品说明**：产品介绍、功能说明
- **使用教程**：操作指南、步骤说明
- **技术文档**：API 文档、开发指南
- **政策条款**：服务协议、隐私政策

### 3. 优化搜索效果

- 为知识条目添加准确的标签
- 在内容中包含用户可能搜索的关键词
- 保持标题简洁明确

## 安全注意事项

1. **保护 API 密钥**：不要在前端代码中暴露 API 密钥
2. **权限控制**：只有管理员可以配置 AI 功能
3. **内容审核**：建议对 AI 生成内容进行适当监控
4. **速率限制**：注意 API 提供商的调用频率限制

## 故障排除

### AI 不回复

1. 检查 API 密钥是否正确
2. 确认 API 端点地址无误
3. 查看模型名称是否有效
4. 检查网络连接

### 知识库搜索不准确

1. 增加相关标签
2. 优化知识内容的关键词
3. 调整系统提示词

### 前端聊天框不显示

1. 确认已启用聊天框功能
2. 清除浏览器缓存
3. 检查是否有 JavaScript 冲突

## 技术细节

### 数据库表结构

```sql
CREATE TABLE wp_zib_ai_knowledge (
    id bigint(20) AUTO_INCREMENT PRIMARY KEY,
    title varchar(255) NOT NULL,
    content longtext NOT NULL,
    category varchar(100),
    tags varchar(500),
    status varchar(20) DEFAULT 'publish',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FULLTEXT KEY search_index (title, content, tags)
);
```

### AJAX 接口

- **聊天接口**：`wp_ajax_zib_ai_chat`
- **知识库列表**：`wp_ajax_zib_ai_get_knowledge_list`
- **添加知识**：`wp_ajax_zib_ai_add_knowledge`
- **更新知识**：`wp_ajax_zib_ai_update_knowledge`
- **删除知识**：`wp_ajax_zib_ai_delete_knowledge`

## 扩展开发

如需添加更多功能，可以参考现有代码结构：

1. 在 `ai-core.php` 中添加新的处理函数
2. 在 `ai-admin.php` 中添加管理界面
3. 在 `ai-frontend.php` 中添加前端展示

## 支持

如有问题或需要定制功能，请联系开发者。

---

**版本**: 1.0  
**更新日期**: 2024  
**兼容**: WordPress 5.0+, PHP 7.4+
