<?php
/**
 * 子比主题 AI 连接诊断工具 (嵌入后台面板)
 * 使用方法：将此文件放入 /functions/ 目录，并在 functions.php 中引入（或直接在此文件末尾添加引入代码）
 * 显示位置：主题设置页面的顶部
 */

// 防止直接访问
if (!defined('ABSPATH')) exit;

// 注册设置页面显示的钩子
add_action('zib_options_page_top', 'zib_ai_debug_tool_render');

function zib_ai_debug_tool_render() {
    // 仅管理员可见
    if (!current_user_can('manage_options')) return;
    
    // 处理测试请求
    if (isset($_POST['zib_ai_debug_test']) && wp_verify_nonce($_POST['zib_ai_debug_nonce'], 'zib_ai_debug_action')) {
        zib_ai_debug_run_test();
    }
    
    // 渲染界面
    ?>
    <div style="background: #fff; border: 2px solid #d63638; padding: 20px; margin: 20px 0; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="margin-top:0; color: #d63638;">🔍 AI 连接诊断工具 (硅基流动专用)</h2>
        <p style="color: #666; font-size: 13px;">此工具用于绕过前端限制，直接在服务器端测试 API 连通性。</p>
        
        <?php if (isset($_POST['zib_ai_debug_test'])) : ?>
            <div style="background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin-top: 15px; font-family: monospace; font-size: 12px; white-space: pre-wrap; word-break: break-all; max-height: 500px; overflow-y: auto;">
                <?php echo zib_ai_debug_get_result(); ?>
            </div>
        <?php endif; ?>

        <form method="post" style="margin-top: 15px;">
            <?php wp_nonce_field('zib_ai_debug_action', 'zib_ai_debug_nonce'); ?>
            <button type="submit" name="zib_ai_debug_test" class="button button-primary" style="font-size: 14px; height: auto; padding: 5px 15px;">
                🚀 立即测试硅基流动连接
            </button>
            <span style="margin-left: 10px; color: #666; font-size: 12px;">
                当前配置模型：<?php echo esc_html(zib_get_option('ai_model') ?: '未设置'); ?>
            </span>
        </form>
    </div>
    <?php
}

// 存储测试结果供显示
$GLOBALS['zib_ai_debug_result'] = '';

function zib_ai_debug_run_test() {
    $result = "=== 开始诊断 ===\n";
    
    // 1. 获取配置
    $api_key = zib_get_option('ai_api_key');
    $api_url = zib_get_option('ai_api_url');
    $model = zib_get_option('ai_model');
    $proxy = zib_get_option('ai_proxy');
    
    $result .= "1. 配置读取:\n";
    $result .= "   - API Key: " . ($api_key ? substr($api_key, 0, 8) . '...' . substr($api_key, -4) : '❌ 未设置') . "\n";
    $result .= "   - API URL: " . ($api_url ?: '❌ 未设置 (使用默认)') . "\n";
    $result .= "   - Model:   " . ($model ?: '❌ 未设置') . "\n";
    $result .= "   - Proxy:   " . ($proxy ?: '未启用') . "\n\n";

    if (!$api_key || !$model) {
        $result .= "❌ 错误: 缺少必要的 API Key 或 模型名称，请在主题设置中完善配置。\n";
        $GLOBALS['zib_ai_debug_result'] = $result;
        return;
    }

    // 默认硅基流动地址
    if (empty($api_url)) {
        $api_url = 'https://api.siliconflow.cn/v1/chat/completions';
        $result .= "   -> 使用默认硅基流动地址: $api_url\n\n";
    }

    // 2. 构建请求
    $body = array(
        'model' => $model,
        'messages' => array(
            array('role' => 'system', 'content' => 'You are a helpful assistant.'),
            array('role' => 'user', 'content' => 'Hello, this is a connectivity test from Zibll Theme. Please reply with "Connection Successful".')
        ),
        'max_tokens' => 50
    );

    $result .= "2. 发送请求:\n";
    $result .= "   - Target: $api_url\n";
    $result .= "   - Payload: " . json_encode($body, JSON_UNESCAPED_UNICODE) . "\n\n";

    // 3. 执行 cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 测试环境暂时忽略证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    );
    
    // 代理设置
    if ($proxy) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        $result .= "   - 使用代理: $proxy\n";
    }
    
    // 添加 User-Agent 防止被某些防火墙拦截
    $headers[] = 'User-Agent: Zibll-Theme-Diagnostic-Tool';
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    $curl_errno = curl_errno($ch);
    curl_close($ch);

    $result .= "3. 响应结果:\n";
    $result .= "   - HTTP 状态码: $http_code\n";
    
    if ($curl_errno) {
        $result .= "   - cURL 错误 ($curl_errno): $curl_error\n\n";
    } else {
        $result .= "   - cURL 连接: 成功\n\n";
    }

    $result .= "4. 原始响应内容:\n";
    if ($response) {
        $json_data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $result .= "   (JSON 格式正确)\n";
            if (isset($json_data['error'])) {
                $result .= "   ❌ API 返回错误:\n";
                $result .= "      类型: " . ($json_data['error']['type'] ?? 'Unknown') . "\n";
                $result .= "      消息: " . ($json_data['error']['message'] ?? 'Unknown') . "\n";
                
                // 特殊判断地区限制
                $msg = strtolower($json_data['error']['message']);
                if (strpos($msg, 'region') !== false || strpos($msg, 'country') !== false || strpos($msg, 'supported') !== false) {
                    $result .= "\n   ⚠️ 检测到地区限制！即使使用了国内服务商，如果您的服务器 IP 在海外或被误判，也可能触发此错误。\n";
                    $result .= "   💡 建议：检查服务器 IP 归属地，或强制开启代理指向国内节点。\n";
                }
            } elseif (isset($json_data['choices'])) {
                $reply = $json_data['choices'][0]['message']['content'] ?? '无内容';
                $result .= "   ✅ 成功！AI 回复: $reply\n";
            }
        } else {
            $result .= "   (非 JSON 格式，可能是 HTML 错误或纯文本)\n";
            $result .= "   " . substr($response, 0, 500) . (strlen($response) > 500 ? '...' : '');
        }
    } else {
        $result .= "   (无响应内容)\n";
    }

    $result .= "\n=== 诊断结束 ===";
    $GLOBALS['zib_ai_debug_result'] = $result;
}

function zib_ai_debug_get_result() {
    return esc_html($GLOBALS['zib_ai_debug_result']);
}
