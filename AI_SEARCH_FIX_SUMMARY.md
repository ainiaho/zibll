# AI 搜索界面显示问题修复说明

## 问题描述
AI 智能总结功能在搜索页面不显示。

## 修复内容

### 1. 修改 `search.php` (第 30 行)
**问题**: 使用了错误的配置项 `ai_chatbox_enabled`
**修复**: 改为使用正确的配置项 `ai_enabled`

```php
// 修改前
if (function_exists('zib_ai_frontend_chatbox') && _pz('ai_chatbox_enabled', false) && $s) {

// 修改后
if (function_exists('zib_ai_frontend_chatbox') && _pz('ai_enabled', false) && $s) {
```

### 2. 增强 `functions/ai-frontend.php`
添加了以下功能：

#### a) 搜索页面 AI 总结样式
```css
#ai-search-summary {
    line-height: 1.8;
}
#ai-search-summary .ai-summary-content {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}
#ai-search-summary .ai-summary-error {
    color: #dc3545;
    padding: 15px;
    background: #fff5f5;
    border-radius: 8px;
}
```

#### b) JavaScript 搜索总结加载函数
```javascript
function zibLoadSearchSummary() {
    let summaryContainer = document.getElementById('ai-search-summary');
    if (!summaryContainer) return;
    
    let keyword = summaryContainer.getAttribute('data-keyword');
    if (!keyword) return;
    
    jQuery.post(ajax_url, {
        action: 'zib_ai_search_summary',
        nonce: nonce,
        keyword: keyword
    }, function(response) {
        if (response.success) {
            summaryContainer.innerHTML = '<div class="ai-summary-content">' + response.data.content + '</div>';
        } else {
            summaryContainer.innerHTML = '<div class="ai-summary-error">错误信息</div>';
        }
    });
}
```

#### c) 页面加载时自动调用
```javascript
jQuery(document).ready(function($) {
    // ... 恢复历史对话代码 ...
    
    // 如果是搜索页面，加载 AI 总结
    zibLoadSearchSummary();
});
```

### 3. 新增 `functions/ai-core.php` AJAX 处理函数
添加了 `zib_ai_search_summary_ajax()` 函数处理搜索总结请求：

```php
function zib_ai_search_summary_ajax() {
    check_ajax_referer('zib_ai_nonce', 'nonce');
    
    $keyword = sanitize_text_field($_POST['keyword'] ?? '');
    
    if (empty($keyword)) {
        wp_send_json_error(array('message' => '关键词不能为空'));
        return;
    }
    
    // 构建搜索总结的提示词
    $system_prompt = "你是一个智能搜索助手。用户搜索了关键词：{$keyword}...";
    
    $messages = array(
        array('role' => 'system', 'content' => $system_prompt),
        array('role' => 'user', 'content' => "请帮我总结一下关于\"{$keyword}\"的核心信息")
    );
    
    $result = Zib_AI_Handler::chat($messages);
    
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
        return;
    }
    
    wp_send_json_success(array('content' => $result['content']));
}
add_action('wp_ajax_zib_ai_search_summary', 'zib_ai_search_summary_ajax');
add_action('wp_ajax_nopriv_zib_ai_search_summary', 'zib_ai_search_summary_ajax');
```

## 使用方法

1. **启用 AI 功能**: 在主题设置中确保启用了 AI 智能助手 (`ai_enabled`)
2. **配置 API**: 设置正确的 AI API 密钥和端点
3. **访问搜索页面**: 在有搜索关键词的搜索结果页面，会自动显示 AI 智能总结模块
4. **查看效果**: 系统会自动调用 AI 生成针对搜索关键词的总结内容

## 注意事项

- 需要正确配置 AI API（OpenAI 或兼容的国内大模型）
- 如果 API 不可用或配置错误，会显示友好的错误提示
- 支持未登录用户访问（通过 `wp_ajax_nopriv_` 钩子）
- 生成的总结内容为 HTML 格式，支持段落、列表等格式化显示

## 相关文件

- `/workspace/search.php` - 搜索页面模板
- `/workspace/functions/ai-frontend.php` - AI 前端展示功能
- `/workspace/functions/ai-core.php` - AI 核心功能
