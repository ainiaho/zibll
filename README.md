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
├── action/             # AJAX 动作处理
│   ├── action.php      # 通用动作处理
│   ├── author-content.php  # 作者内容管理
│   ├── comment.php     # 评论相关
│   ├── new_posts.php   # 新文章推送
│   ├── php_upload.php  # 文件上传
│   ├── post_navs.php   # 文章导航
│   ├── sign_register.php  # 登录注册
│   └── user.php        # 用户操作
├── css/                # 样式文件
│   ├── ai-chat.css     # AI 聊天样式
│   ├── bootstrap.min.css
│   ├── editor-style.css
│   ├── font-awesome.min.css
│   ├── main.css        # 主样式
│   ├── navs.css        # 导航样式
│   ├── new-posts.css   # 新文章样式
│   ├── swiper.css      # 轮播图样式
│   ├── tinymce.css     # 编辑器样式
│   └── widget-set.css  # 小工具设置样式
├── functions/          # 核心功能函数
│   ├── ai-admin.php    # AI 管理功能
│   ├── ai-core.php     # AI 核心功能
│   ├── ai-frontend.php # AI 前端功能
│   ├── function-ajax.php  # AJAX 函数
│   ├── functions.php   # 核心函数库
│   ├── zib-ai.php      # AI 相关函数
│   ├── zib-author.php  # 作者相关函数
│   ├── zib-category.php # 分类相关函数
│   ├── zib-comments-list.php # 评论列表
│   ├── zib-content.php # 内容相关
│   ├── zib-footer.php  # 页脚相关
│   ├── zib-head.php    # 头部相关
│   ├── zib-header.php  # 页头相关
│   ├── zib-index.php   # 首页相关
│   ├── zib-posts-list.php # 文章列表
│   ├── zib-share.php   # 分享功能
│   ├── zib-single.php  # 文章页相关
│   ├── zib-svg-icon.php # SVG 图标
│   └── zib-user.php    # 用户相关
├── framework/          # 主题框架
│   ├── code/           # 代码模块
│   ├── css/            # 框架样式
│   ├── img/            # 框架图片
│   ├── includes/       # 框架包含文件
│   ├── js/             # 框架脚本
│   └── options-framework.php  # 选项框架
├── img/                # 图片资源
├── js/                 # JavaScript 脚本
│   ├── ai-chat.js      # AI 聊天脚本
│   ├── author.js       # 作者页脚本
│   ├── clipboard.min.js
│   ├── comment.js      # 评论脚本
│   ├── edit/           # 编辑器扩展
│   ├── enlighter/      # 代码高亮
│   ├── imgbox.js       # 图片浏览
│   ├── libs/           # 第三方库
│   ├── loader.js       # 加载器
│   ├── main.js         # 主脚本
│   ├── mini-touch.js   # 触摸支持
│   ├── navs.js         # 导航脚本
│   ├── newposts.js     # 新文章脚本
│   ├── poster-share.js # 海报分享
│   ├── precode.js      # 代码预处理
│   ├── section_navs.js # 分段导航
│   ├── sign-register.js # 登录注册
│   ├── svg-icon.js     # SVG 图标
│   └── widget-set.js   # 小工具设置
├── oauth/              # 第三方登录
│   ├── github/         # GitHub 登录
│   ├── qq/             # QQ 登录
│   ├── weibo/          # 微博登录
│   ├── weixin/         # 微信登录
│   └── oauth.php       # OAuth 核心
├── pages/              # 自定义页面模板
│   ├── archives.php    # 归档页
│   ├── newposts.php    # 新文章页
│   ├── postsnavs.php   # 文章导航页
│   ├── resetpassword.php  # 重置密码
│   └── sidebar.php     # 侧边栏
├── template/           # 模板片段
│   ├── category-dosc.php  # 分类文档
│   ├── category-topics.php # 分类主题
│   ├── content-404.php # 404 内容
│   ├── excerpt.php     # 摘要模板
│   └── single-dosc.php # 文章文档
├── vendor/             # Composer 依赖
├── widgets/            # 小工具组件
│   ├── widget-index.php    # 首页小工具
│   ├── widget-more.php     # 更多小工具
│   ├── widget-posts.php    # 文章小工具
│   ├── widget-slider.php   # 轮播小工具
│   └── widget-user.php     # 用户小工具
├── yiyan/              # 一言功能
│   ├── qv-yiyan.php
│   └── qv-yiyan.txt
├── zibpay/             # 支付系统
│   ├── assets/         # 支付资源
│   ├── class/          # 支付类
│   ├── page/           # 支付页面
│   ├── sdk/            # 支付 SDK
│   ├── shop/           # 商店功能
│   ├── download.php    # 下载处理
│   ├── function-ajax.php  # 支付 AJAX
│   ├── function-download.php # 下载功能
│   ├── function-user.php   # 支付用户功能
│   └── functions.php   # 支付核心函数
├── 404.php             # 404 页面
├── archive.php         # 归档页面
├── author.php          # 作者页面
├── category.php        # 分类页面
├── comments.php        # 评论模板
├── footer.php          # 页脚模板
├── functions-admin.php # 后台功能
├── functions-theme.php # 主题功能
├── functions-xzh.php   # 熊掌号功能
├── functions.php       # 主题入口函数
├── go.php              # 跳转页面
├── header.php          # 页头模板
├── image.php           # 图片页面
├── index.php           # 主模板文件
├── options.php         # 主题设置
├── page.php            # 页面模板
├── search.php          # 搜索页面
├── sidebar.php         # 侧边栏模板
├── single.php          # 文章页面
├── style.css           # 主题样式
└── tag.php             # 标签页面
```

## 🚀 快速开始

### 环境要求

- WordPress 5.0 或更高版本
- PHP 7.4 或更高版本（推荐 PHP 8.0+）
- MySQL 5.6 或更高版本 / MariaDB 10.1 或更高版本
- 需要开启以下 PHP 扩展：cURL、OpenSSL、JSON、mbstring

### 安装步骤

1. 下载主题压缩包并解压
2. 将 `zibll` 文件夹上传到 `wp-content/themes/` 目录
3. 在 WordPress 后台「外观」→「主题」中启用主题
4. 进入「子比主题设置」进行基础配置

### 必要配置

启用主题后，请完成以下配置：

1. **基础设置** - 站点名称、Logo、favicon、SEO 设置等
2. **支付配置** - 配置支付宝、微信支付等支付接口参数
3. **用户系统** - 设置会员等级、权益和积分规则
4. **AI 功能** - 配置 AI API Key 和服务提供商（可选）
5. **OAuth 登录** - 配置第三方登录平台的应用信息（可选）
6. **安全设置** - 配置登录保护、防刷机制、防盗链等

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
| `zib_get_ai_response()` | 获取 AI 对话响应 |
| `zib_get_author_posts()` | 获取作者文章列表 |
| `zib_get_resource_downloads()` | 获取资源下载信息 |
| `zib_get_user_balance()` | 获取用户余额和积分 |

### REST API 扩展

主题提供了丰富的 REST API 接口：

```
GET  /wp-json/zib/v1/user/info       - 获取用户信息
POST /wp-json/zib/v1/order/create    - 创建订单
GET  /wp-json/zib/v1/post/pay-info   - 获取文章付费信息
POST /wp-json/zib/v1/ai/chat         - AI 对话请求
GET  /wp-json/zib/v1/user/balance    - 获取用户余额和积分
POST /wp-json/zib/v1/resource/download - 资源下载验证
GET  /wp-json/zib/v1/author/posts    - 获取作者文章列表
POST /wp-json/zib/v1/oauth/bind      - 绑定第三方账号
```

## ⚙️ 主题设置

通过后台「子比主题设置」可以配置：

- **全局设置** - 站点配置、SEO 设置、统计代码、性能优化
- **首页设置** - 布局风格、轮播图、推荐内容、模块排序
- **文章设置** - 阅读模式、付费配置、分享功能、相关推荐
- **用户中心** - 登录注册、会员中心、积分系统、个人中心
- **支付设置** - 支付接口（支付宝/微信/PayPal）、价格策略、优惠券
- **AI 功能** - AI 服务提供商、API Key、对话模型、提示词配置
- **OAuth 登录** - QQ、微信、微博、GitHub 等第三方登录配置
- **消息通知** - 邮件模板、短信通知、站内信、推送服务
- **安全设置** - 登录保护、防刷机制、防盗链、水印设置
- **高级功能** - 自定义代码、数据库优化、缓存设置

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
A: 进入「子比主题设置」→「支付设置」，根据选择的支付方式填写对应参数（如支付宝 PID/Key、微信商户号等）。

**Q: 如何添加自定义用户字段？**  
A: 使用 `zib_add_user_meta_field` 钩子添加，详见开发文档。

**Q: AI 功能无法使用怎么办？**  
A: 检查 API Key 是否正确配置，确认服务器可以访问 AI 服务提供商的 API，并检查 PHP cURL 扩展是否开启。

**Q: 第三方登录无法使用？**  
A: 确保已在对应平台（QQ、微信、GitHub 等）创建应用并获取 AppID 和 AppKey，正确填写到主题设置中。

**Q: 如何设置会员等级和权益？**  
A: 在「子比主题设置」→「用户中心」→「会员设置」中配置会员等级、价格、图标和专属权益。

**Q: 资源下载失败？**  
A: 检查文件路径是否正确，确认服务器权限设置，查看是否开启了防盗链功能。

## 📝 更新日志

### v2.2 (当前版本)
- 集成 AI 对话功能，支持自定义 AI 服务
- 完善的付费阅读和资源下载系统
- 支持多种 OAuth 第三方登录（QQ、微信、GitHub、微博）
- 多级会员体系和积分系统
- 响应式设计，完美适配各种设备
- 优化的支付流程和用户体验
- 丰富的主题设置选项
- 代码高亮和海报分享功能

## 📄 许可证

本主题基于 GPL v2 或更高版本许可证发布。

## 🔗 相关链接

- [官方文档](https://www.zibll.com/)
- [WordPress 官网](https://wordpress.org/)
- [开发者社区](https://www.zibll.com/community/)

---

> 💡 **提示**: 建议在进行任何自定义开发前，先备份网站数据和文件。如需技术支持，请访问官方网站或联系开发者。
