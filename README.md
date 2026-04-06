# 子比主题 (Zibll)

> 一款功能强大的现代化 WordPress 主题，专为知识付费、资源下载、社区论坛等场景设计。

## 📖 项目简介

子比主题（Zibll）是一款基于 WordPress 开发的多功能主题，集成了文章付费阅读、资源下载、会员系统、在线支付、AI 对话、OAuth 登录等丰富功能。主题采用现代化的设计风格，支持响应式布局，适用于各种设备。

### 主要特性

- 💰 **付费阅读系统** - 支持文章部分内容付费查看、全文付费下载
- 📦 **资源管理** - 完善的资源下载管理系统，支持多种存储方式
- 👥 **会员体系** - 多级会员制度，不同等级享受不同权益
- 💳 **支付集成** - 支持支付宝、微信支付、PayPal 等多种支付方式
- 🤖 **AI 功能** - 集成 AI 对话功能，支持自定义 AI 服务
- 🔐 **第三方登录** - 支持 QQ、微信、GitHub 等 OAuth 登录
- 📱 **响应式设计** - 完美适配 PC、平板、手机等各种设备
- 🎨 **高度可定制** - 丰富的主题设置选项，无需代码即可自定义

## 📁 目录结构

```
zibll/
├── assets/              # 静态资源目录
│   ├── css/            # 样式文件
│   ├── js/             # JavaScript 脚本
│   ├── images/         # 图片资源
│   └── fonts/          # 字体文件
├── inc/                # 核心功能模块
│   ├── functions/      # 核心函数库
│   ├── modules/        # 功能模块（支付、AI、OAuth 等）
│   ├── options/        # 主题设置框架
│   └── widgets/        # 小工具组件
├── template-parts/     # 模板片段
├── templates/          # 自定义页面模板
├── languages/          # 语言包（国际化）
├── style.css           # 主题主样式
├── functions.php       # 主题入口函数
├── index.php           # 主模板文件
├── header.php          # 页头模板
├── footer.php          # 页脚模板
└── README.md           # 说明文档
```

## 🚀 快速开始

### 环境要求

- WordPress 5.0 或更高版本
- PHP 7.4 或更高版本
- MySQL 5.6 或更高版本 / MariaDB 10.1 或更高版本

### 安装步骤

1. 下载主题压缩包并解压
2. 将 `zibll` 文件夹上传到 `wp-content/themes/` 目录
3. 在 WordPress 后台「外观」→「主题」中启用主题
4. 进入「子比主题设置」进行基础配置

### 必要配置

启用主题后，请完成以下配置：

1. **基础设置** - 站点名称、Logo、favicon 等
2. **支付配置** - 配置支付接口参数
3. **用户系统** - 设置会员等级和权益
4. **安全设置** - 配置登录保护、防刷机制

## 🔧 二次开发指南

### 添加自定义函数

在子主题的 `functions.php` 中添加自定义代码：

```php
// 示例：添加自定义功能
add_action('wp_enqueue_scripts', 'my_custom_scripts');
function my_custom_scripts() {
    wp_enqueue_script('my-script', get_stylesheet_directory_uri() . '/js/my-script.js', array(), '1.0.0', true);
}
```

### 扩展 AI 功能

```php
// 自定义 AI 服务提供者
add_filter('zib_ai_providers', 'my_ai_provider');
function my_ai_provider($providers) {
    $providers['custom'] = array(
        'name' => '自定义 AI',
        'api_url' => 'https://your-api.com/chat',
        'api_key' => 'your-api-key'
    );
    return $providers;
}
```

### 添加支付方式

```php
// 注册新的支付网关
add_filter('zib_payment_gateways', 'my_payment_gateway');
function my_payment_gateway($gateways) {
    $gateways['my_pay'] = array(
        'name' => '我的支付',
        'class' => 'ZIB_My_Payment_Gateway'
    );
    return $gateways;
}
```

### 创建自定义页面模板

在主题根目录创建 `template-custom.php`：

```php
<?php
/*
Template Name: 自定义页面
*/
get_header(); ?>

<div class="container">
    <h1><?php the_title(); ?></h1>
    <div class="content">
        <?php the_content(); ?>
    </div>
</div>

<?php get_footer(); ?>
```

### 使用主题内置函数

```php
// 获取用户信息
$user_info = zib_get_user_info($user_id);

// 检查用户是否购买过文章
$has_bought = zib_user_has_bought($post_id, $user_id);

// 获取文章付费信息
$pay_info = zib_get_post_pay_info($post_id);

// 输出付费提示框
zib_pay_prompt_box($post_id);
```

## 📚 API 参考

### 常用内置函数

| 函数名 | 说明 |
|--------|------|
| `zib_get_user_info()` | 获取用户详细信息 |
| `zib_user_has_bought()` | 检查用户购买状态 |
| `zib_get_post_pay_info()` | 获取文章付费配置 |
| `zib_pay_prompt_box()` | 输出付费提示框 |
| `zib_get_vip_level()` | 获取用户会员等级 |
| `zib_check_oauth_bind()` | 检查第三方账号绑定 |

### REST API 扩展

主题提供了丰富的 REST API 接口：

```
GET  /wp-json/zib/v1/user/info      - 获取用户信息
POST /wp-json/zib/v1/order/create   - 创建订单
GET  /wp-json/zib/v1/post/pay-info  - 获取文章付费信息
POST /wp-json/zib/v1/ai/chat        - AI 对话请求
```

## ⚙️ 主题设置

通过后台「子比主题设置」可以配置：

- **全局设置** - 站点配置、SEO 设置、统计代码
- **首页设置** - 布局、轮播图、推荐内容
- **文章设置** - 阅读模式、付费配置、分享功能
- **用户中心** - 登录注册、会员中心、积分系统
- **支付设置** - 支付接口、价格策略、优惠券
- **消息通知** - 邮件模板、短信通知、站内信
- **高级功能** - AI 配置、水印设置、防盗链

## 🛠️ 开发最佳实践

### 代码组织

- 使用子主题进行自定义，避免直接修改父主题
- 将自定义函数按功能分类存放
- 遵循 WordPress 编码规范

### 性能优化

- 合理使用缓存机制
- 按需加载脚本和样式
- 优化数据库查询

### 安全性

- 对所有用户输入进行验证和转义
- 使用 WordPress 内置的安全函数
- 定期检查依赖库的安全性

### 国际化

```php
// 使用 __() 和 _e() 函数
__('文本内容', 'zibll');
_e('文本内容', 'zibll');
```

## ❓ 常见问题

**Q: 如何修改主题颜色？**  
A: 在「子比主题设置」→「全局设置」→「配色方案」中修改，或通过自定义 CSS 覆盖。

**Q: 支付接口如何配置？**  
A: 进入「子比主题设置」→「支付设置」，根据选择的支付方式填写对应参数。

**Q: 如何添加自定义用户字段？**  
A: 使用 `zib_add_user_meta_field` 钩子添加，详见开发文档。

**Q: AI 功能无法使用怎么办？**  
A: 检查 API Key 是否正确配置，确认服务器可以访问 AI 服务提供商的 API。

## 📝 更新日志

### v7.x (当前版本)
- 新增 AI 对话功能
- 优化支付流程
- 改进移动端体验
- 修复已知问题

### v6.x
- 新增会员等级系统
- 支持多种支付方式
- 优化资源下载管理

## 📄 许可证

本主题基于 GPL v2 或更高版本许可证发布。

## 🔗 相关链接

- [官方文档](https://www.zibll.com/)
- [WordPress 官网](https://wordpress.org/)
- [开发者社区](https://www.zibll.com/community/)

---

> 💡 **提示**: 建议在进行任何自定义开发前，先备份网站数据和文件。如需技术支持，请访问官方网站或联系开发者。
