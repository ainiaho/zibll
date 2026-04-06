# 子比主题 (Zibll) 开发说明文档

## 目录

1. [项目概述](#项目概述)
2. [目录结构](#目录结构)
3. [核心功能模块](#核心功能模块)
4. [二次开发指南](#二次开发指南)
5. [API 参考](#api-参考)
6. [最佳实践](#最佳实践)

---

## 项目概述

**子比主题 (Zibll)** 是一款专为博客、自媒体、资讯类网站设计的 WordPress 主题。

- **版本**: 2.2
- **官方网站**: https://www.zibll.com
- **设计风格**: 简约优雅
- **主要特性**: 
  - 付费阅读/付费下载
  - 个人中心
  - 网址导航
  - SEO 优化
  - 响应式设计
  - AI 对话与知识库
  - 第三方支付集成

---

## 目录结构

```
/workspace/
├── functions.php              # 主题主入口文件
├── functions-theme.php        # 主题核心功能加载
├── style.css                  # 主题样式定义
├── header.php                 # 页面头部模板
├── footer.php                 # 页面底部模板
├── index.php                  # 首页模板
├── single.php                 # 文章详情页模板
├── page.php                   # 页面模板
├── archive.php                # 归档页模板
├── sidebar.php                # 侧边栏模板
├── comments.php               # 评论模板
│
├── functions/                 # 核心功能函数目录
│   ├── functions.php          # 基础功能函数
│   ├── zib-header.php         # 头部相关功能
│   ├── zib-footer.php         # 底部相关功能
│   ├── zib-index.php          # 首页功能
│   ├── zib-single.php         # 文章页功能
│   ├── zib-posts-list.php     # 文章列表功能
│   ├── zib-comments-list.php  # 评论列表功能
│   ├── zib-user.php           # 用户中心功能
│   ├── zib-author.php         # 作者页功能
│   ├── zib-category.php       # 分类页功能
│   ├── zib-share.php          # 分享功能
│   ├── zib-content.php        # 内容处理功能
│   └── zib-svg-icon.php       # SVG 图标功能
│
├── functions/ai-*.php         # AI 功能模块
│   ├── ai-core.php            # AI 核心配置与知识库管理
│   ├── ai-admin.php           # AI 后台管理功能
│   └── ai-frontend.php        # AI 前端交互功能
│
├── action/                    # AJAX 动作处理
│   ├── action.php             # 通用动作处理
│   ├── comment.php            # 评论相关动作
│   ├── user.php               # 用户相关动作
│   ├── sign_register.php      # 登录注册动作
│   └── ...
│
├── zibpay/                    # 支付系统模块
│   ├── functions.php          # 支付功能入口
│   ├── class/                 # 支付类库
│   ├── page/                  # 支付管理页面
│   ├── shop/                  # 商店功能
│   └── sdk/                   # 支付 SDK
│
├── oauth/                     # 第三方登录
│   ├── qq/                    # QQ 登录
│   ├── weixin/                # 微信登录
│   ├── weibo/                 # 微博登录
│   ├── github/                # GitHub 登录
│   └── oauth.php              # OAuth 统一入口
│
├── framework/                 # 主题框架
│   ├── options-framework.php  # 设置框架入口
│   ├── includes/              # 框架核心类
│   ├── css/                   # 框架样式
│   └── js/                    # 框架脚本
│
├── widgets/                   # 小工具模块
│   ├── widget-index.php       # 首页小工具
│   ├── widget-posts.php       # 文章小工具
│   ├── widget-slider.php      # 轮播小工具
│   └── ...
│
├── template/                  # 自定义页面模板
│   ├── category-topics.php    # 专题分类模板
│   ├── category-dosc.php      # 文档分类模板
│   └── ...
│
├── pages/                     # 特殊页面模板
│   ├── archives.php           # 归档页
│   ├── newposts.php           # 新文章页
│   ├── sidebar.php            # 侧边栏页
│   └── ...
│
├── css/                       # 样式文件
│   ├── main.css               # 主样式
│   ├── bootstrap.min.css      # Bootstrap 样式
│   ├── ai-chat.css            # AI 聊天样式
│   └── ...
│
├── js/                        # JavaScript 文件
│   ├── main.js                # 主脚本
│   ├── ai-chat.js             # AI 聊天脚本
│   ├── comment.js             # 评论脚本
│   └── ...
│
└── vendor/                    # Composer 依赖库
    ├── yurunsoft/pay-sdk/     # 支付 SDK
    └── psr/http-message/      # HTTP 消息接口
```

---

## 核心功能模块

### 1. AI 对话与知识库模块

**文件位置**: `functions/ai-*.php`

#### 主要类

- **Zib_AI_Config**: AI 配置管理
  - `get_api_key()`: 获取 API 密钥
  - `get_api_endpoint()`: 获取 API 端点
  - `get_model()`: 获取模型名称
  - `get_system_prompt()`: 获取系统提示词
  - `is_knowledge_base_enabled()`: 检查知识库是否启用
  - `get_max_tokens()`: 获取最大上下文长度

- **Zib_Knowledge_Base**: 知识库管理
  - `init_table()`: 初始化数据库表
  - `add_item()`: 添加知识条目
  - `search()`: 搜索知识库内容
  - `delete_item()`: 删除知识条目

#### 数据库表结构

```sql
CREATE TABLE wp_zib_ai_knowledge (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    content longtext NOT NULL,
    category varchar(100) DEFAULT '',
    tags varchar(500) DEFAULT '',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status tinyint(1) DEFAULT 1,
    PRIMARY KEY (id),
    KEY category (category),
    KEY status (status)
);
```

#### 配置选项

在后台设置中可配置以下选项：
- `ai_api_key`: OpenAI API 密钥
- `ai_api_endpoint`: API 端点 URL
- `ai_model`: 使用的模型 (默认 gpt-3.5-turbo)
- `ai_system_prompt`: 系统提示词
- `ai_knowledge_base_enabled`: 是否启用知识库
- `ai_max_tokens`: 最大生成 token 数

### 2. 支付系统模块 (ZibPay)

**文件位置**: `zibpay/`

#### 核心功能

- 订单管理
- 商品管理
- 支付接口集成 (支付宝、微信支付等)
- 付费阅读
- 付费下载

#### 主要类

- **Zibpay_Order**: 订单管理类
  - `add_order_showdb()`: 创建订单表
  - 订单查询与管理

#### 后台管理菜单

```php
add_menu_page('Zibll 商城', 'Zibll 商城', 'administrator', 'zibpay_page', 'zibpay_page', 'dashicons-cart');
add_submenu_page('zibpay_page', '商品明细', '商品明细', 'administrator', 'zibpay_product_page', 'zibpay_product_page');
add_submenu_page('zibpay_page', '订单明细', '订单明细', 'administrator', 'zibpay_order_page', 'zibpay_order_page');
```

### 3. 第三方登录模块 (OAuth)

**文件位置**: `oauth/`

#### 支持的登录方式

- QQ 登录 (`qq/`)
- 微信登录 (`weixin/`)
- 微博登录 (`weibo/`)
- GitHub 登录 (`github/`)

#### 路由规则

```php
// OAuth 登录处理页路由 (/oauth)
new_rules['oauth/([A-Za-z]+)$']          = 'index.php?oauth=$matches[1]';
new_rules['oauth/([A-Za-z]+)/callback$'] = 'index.php?oauth=$matches[1]&oauth_callback=1';
```

### 4. 主题设置框架

**文件位置**: `framework/`

基于 Options Framework 构建，提供可视化主题设置界面。

#### 核心文件

- `options.php`: 主题设置入口
- `framework/options-framework.php`: 框架入口
- `framework/includes/class-options-framework-admin.php`: 后台管理
- `framework/includes/class-options-interface.php`: 设置界面

---

## 二次开发指南

### 1. 自定义函数添加

在 `functions.php` 文件中添加自定义代码：

```php
<?php
require get_stylesheet_directory() . '/functions-theme.php';

/**
 * 在此处添加您的自定义函数
 * 主题更新时请备份此文件的修改
 */

// 示例：添加自定义功能
function my_custom_function() {
    // 你的代码
}
add_action('wp_head', 'my_custom_function');
```

### 2. 扩展 AI 功能

#### 添加自定义 AI 配置

```php
// 在 functions/ai-core.php 的 Zib_AI_Config 类中添加
public static function get_temperature() {
    return _pz('ai_temperature', 0.7);
}
```

#### 扩展知识库功能

```php
// 继承 Zib_Knowledge_Base 类
class My_Knowledge_Base extends Zib_Knowledge_Base {
    
    public static function search_by_category($category, $limit = 10) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zib_ai_knowledge';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE category = %s AND status = 1 LIMIT %d",
            $category,
            $limit
        ));
        
        return $results;
    }
}
```

### 3. 扩展支付功能

#### 添加新的支付方式

```php
// 在 zibpay/class/ 目录下创建新的支付类
class Zibpay_NewPayment {
    
    public function pay($order_id, $amount, $subject) {
        // 实现支付逻辑
    }
    
    public function callback() {
        // 处理支付回调
    }
}
```

### 4. 自定义页面模板

在 `template/` 或 `pages/` 目录下创建新的页面模板：

```php
<?php
/*
Template Name: 自定义页面模板
*/

get_header();
?>

<div class="custom-page">
    <!-- 你的页面内容 -->
</div>

<?php get_footer(); ?>
```

### 5. 添加新的小工具

在 `widgets/` 目录下创建新的小工具：

```php
<?php
class My_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'my_widget',
            __('我的小工具', 'text_domain'),
            array('description' => __('小工具描述', 'text_domain'))
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        // 输出小工具内容
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        // 后台表单
    }
    
    public function update($new_instance, $old_instance) {
        // 保存设置
    }
}

add_action('widgets_init', function() {
    register_widget('My_Widget');
});
```

### 6. AJAX 功能扩展

在 `action/` 目录下添加新的 AJAX 处理函数：

```php
// action/my-action.php
add_action('wp_ajax_my_custom_action', 'my_custom_action_handler');
add_action('wp_ajax_nopriv_my_custom_action', 'my_custom_action_handler');

function my_custom_action_handler() {
    // 处理 AJAX 请求
    $data = $_POST['data'];
    
    // 返回 JSON 响应
    wp_send_json_success(array(
        'message' => '操作成功',
        'data' => $result
    ));
}
```

---

## API 参考

### 主题内置函数

#### `_pz()` - 获取主题设置

```php
/**
 * 获取主题设置选项
 * 
 * @param string $key     设置键名
 * @param mixed  $default 默认值
 * @return mixed          设置值
 */
function _pz($key, $default = '') {
    // 实现代码
}

// 使用示例
$api_key = _pz('ai_api_key', '');
```

#### `zib_get_user_info()` - 获取用户信息

```php
/**
 * 获取当前用户信息
 * 
 * @return array 用户信息数组
 */
function zib_get_user_info() {
    // 实现代码
}
```

### REST API 扩展

主题支持通过 WordPress REST API 进行扩展：

```php
add_action('rest_api_init', function() {
    register_rest_route('zib/v1', '/ai/chat', array(
        'methods'  => 'POST',
        'callback' => 'zib_ai_chat_handler',
        'permission_callback' => '__return_true'
    ));
});

function zib_ai_chat_handler($request) {
    $message = $request->get_param('message');
    // 处理 AI 对话
    return rest_ensure_response($response);
}
```

---

## 最佳实践

### 1. 代码组织

- 将相关功能组织到独立的文件中
- 使用类来封装复杂的功能
- 遵循 WordPress 编码规范

### 2. 性能优化

- 使用缓存减少数据库查询
- 延迟加载非关键资源
- 优化图片和静态资源

```php
// 示例：使用缓存
function get_cached_data($key, $callback, $expiration = 3600) {
    $cached = get_transient($key);
    if ($cached !== false) {
        return $cached;
    }
    
    $data = call_user_func($callback);
    set_transient($key, $data, $expiration);
    
    return $data;
}
```

### 3. 安全性

- 验证和清理所有用户输入
- 使用 nonce 验证表单提交
- 转义输出内容

```php
// 验证 nonce
if (!wp_verify_nonce($_POST['nonce'], 'my_action')) {
    wp_send_json_error('非法请求');
}

// 清理输入
$data = sanitize_text_field($_POST['data']);

// 转义输出
echo esc_html($data);
```

### 4. 国际化

使用 WordPress 的国际化函数：

```php
__('Text to translate', 'text_domain');
_e('Text to translate and echo', 'text_domain');
sprintf(__('Hello %s', 'text_domain'), $name);
```

### 5. 兼容性

- 检查 WordPress 版本兼容性
- 使用条件判断确保函数存在
- 提供降级方案

```php
if (version_compare(get_bloginfo('version'), '5.0', '>=')) {
    // 使用新版本功能
} else {
    // 降级方案
}
```

### 6. 调试技巧

```php
// 开启调试模式 (wp-config.php)
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// 记录日志
error_log('Debug message: ' . print_r($data, true));

// 使用 WordPress 的调试函数
var_dump($variable);
print_r($array);
```

---

## 常见问题

### Q1: 如何修改主题版本号？

编辑 `style.css` 文件中的 `Version` 字段。

### Q2: 如何添加新的主题设置选项？

在 `options.php` 文件中添加新的设置项，使用 Options Framework 的语法。

### Q3: AI 功能不工作怎么办？

1. 检查 API 密钥是否正确配置
2. 确认 API 端点 URL 可访问
3. 查看错误日志
4. 检查网络连接

### Q4: 支付功能如何测试？

使用支付平台提供的沙箱环境进行测试，不要使用真实账户进行测试。

---

## 更新日志

参见 `update_log.md` 文件。

---

## 技术支持

- **官方网站**: https://www.zibll.com
- **作者 QQ**: 770349780
- **二次开发**: 主题源码有详细注释，支持二次开发

---

## 许可证

请遵守 WordPress 主题相关许可证规定。

---

*最后更新：2024 年*
