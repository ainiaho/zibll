<?php
/**
 * AI 与知识库功能前端展示
 * 
 * @package Zibll Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 在前端加载 AI 聊天窗口
 */
function zib_ai_frontend_chatbox() {
    // 兼容旧配置项 ai_enabled 和新配置项 ai_chatbox_enabled
    $ai_enabled = _pz('ai_enabled', false);
    $chatbox_style = _pz('ai_chatbox_style', 'float'); // float, search, both
    
    // 根据显示样式决定是否显示
    if (!$ai_enabled) {
        return;
    }
    
    // 如果设置为仅在搜索页显示，且当前不是搜索页，则不显示
    if ($chatbox_style === 'search' && !is_search()) {
        return;
    }
    
    // 如果设置为仅在搜索页显示或同时显示，或者是悬浮模式，都显示
    // (float 和 both 都会显示悬浮按钮)
    
    ?>
    <div id="zib-ai-chatbox" style="position: fixed; bottom: 80px; right: 20px; width: 350px; background: white; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.15); z-index: 9999; display: none;">
        <div style="padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px 10px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0;">AI 智能助手</h4>
            <button onclick="zibToggleChatbox()" style="background: none; border: none; color: white; cursor: pointer; font-size: 18px;">&times;</button>
        </div>
        
        <div id="zib-ai-chat-messages" style="height: 350px; overflow-y: auto; padding: 15px; background: #f9f9f9;">
            <div class="zib-ai-message assistant">
                您好！我是 AI 助手，有什么可以帮您的吗？
            </div>
        </div>
        
        <div style="padding: 15px; border-top: 1px solid #eee; background: white; border-radius: 0 0 10px 10px;">
            <div style="display: flex; gap: 10px;">
                <input type="text" id="zib-ai-input" placeholder="输入您的问题..." 
                       style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none;"
                       onkeypress="if(event.keyCode==13) zibSendAIMessage()">
                <button onclick="zibSendAIMessage()" 
                        style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    发送
                </button>
            </div>
        </div>
    </div>
    
    <button id="zib-ai-chat-toggle" onclick="zibToggleChatbox()" 
            style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; cursor: pointer; box-shadow: 0 5px 15px rgba(0,0,0,0.2); z-index: 9999; font-size: 24px;">
        💬
    </button>
    
    <style>
    .zib-ai-message {
        margin: 10px 0;
        padding: 12px 15px;
        border-radius: 10px;
        max-width: 80%;
        word-wrap: break-word;
    }
    .zib-ai-message.user {
        background: #667eea;
        color: white;
        margin-left: auto;
    }
    .zib-ai-message.assistant {
        background: white;
        border: 1px solid #e0e0e0;
        margin-right: auto;
    }
    .zib-ai-message.loading {
        background: #f0f0f0;
        color: #666;
        font-style: italic;
    }
    #zib-ai-chat-messages {
        scroll-behavior: smooth;
    }
    /* 搜索页面 AI 总结样式 */
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
    </style>
    
    <script type="text/javascript">
    // 全局函数定义
    function zibToggleChatbox() {
        let chatbox = document.getElementById('zib-ai-chatbox');
        if (chatbox.style.display === 'none') {
            chatbox.style.display = 'block';
            document.getElementById('zib-ai-input').focus();
        } else {
            chatbox.style.display = 'none';
        }
    }
    
    function zibSendAIMessage() {
        let input = document.getElementById('zib-ai-input');
        let message = input.value.trim();
        if (!message) return;
        
        // 显示用户消息
        zibAppendMessage('user', message);
        input.value = '';
        
        // 保存历史
        zibSaveChatHistory('user', message);
        
        // 显示加载状态
        let loadingId = zibAppendMessage('loading', '思考中...');
        
        // 获取历史对话
        let history = [];
        let messages = document.querySelectorAll('.zib-ai-message:not(.loading)');
        messages.forEach(function(msg) {
            if (msg.classList.contains('user')) {
                history.push({role: 'user', content: msg.textContent});
            } else if (msg.classList.contains('assistant')) {
                history.push({role: 'assistant', content: msg.textContent});
            }
        });
        
        // 限制历史记录数量（最近 10 条）
        history = history.slice(-10);
        
        // 发送请求
        jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
            action: 'zib_ai_chat',
            nonce: '<?php echo wp_create_nonce("zib_ai_nonce"); ?>',
            message: message,
            history: JSON.stringify(history)
        }, function(response) {
            // 移除加载消息
            document.getElementById(loadingId).remove();
            
            if (response.success) {
                zibAppendMessage('assistant', response.data.content);
                zibSaveChatHistory('assistant', response.data.content);
            } else {
                // 修复错误显示：处理 object 对象的情况
                let errorMsg = '抱歉，出现未知错误';
                if (response.data) {
                    if (typeof response.data === 'string') {
                        errorMsg = response.data;
                    } else if (typeof response.data.message === 'string') {
                        errorMsg = response.data.message;
                    } else if (typeof response.data === 'object') {
                        try {
                            errorMsg = JSON.stringify(response.data);
                        } catch(e) {
                            errorMsg = '抱歉，出现错误：[object Object]';
                        }
                    }
                }
                zibAppendMessage('assistant', '抱歉，出现错误：' + errorMsg);
            }
            
            // 滚动到底部
            let chatMessages = document.getElementById('zib-ai-chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }).fail(function(xhr, status, error) {
            // 处理 AJAX 请求失败的情况
            document.getElementById(loadingId).remove();
            let errorMsg = '网络请求失败';
            if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                errorMsg = xhr.responseJSON.data.message;
            } else if (xhr.responseText) {
                errorMsg = xhr.responseText;
            }
            zibAppendMessage('assistant', '抱歉，出现错误：' + errorMsg);
        });
    }
    
    function zibAppendMessage(role, content) {
        let chatMessages = document.getElementById('zib-ai-chat-messages');
        let msgDiv = document.createElement('div');
        msgDiv.className = 'zib-ai-message ' + role;
        msgDiv.id = 'msg-' + Date.now();
        msgDiv.textContent = content;
        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return msgDiv.id;
    }
    
    function zibSaveChatHistory(role, content) {
        let history = [];
        let stored = localStorage.getItem('zib_ai_chat_history');
        if (stored) {
            try {
                history = JSON.parse(stored);
            } catch(e) {}
        }
        
        history.push({role: role, content: content});
        
        // 限制存储的消息数量
        history = history.slice(-20);
        
        localStorage.setItem('zib_ai_chat_history', JSON.stringify(history));
    }
    
    // 搜索页面 AI 总结功能
    function zibLoadSearchSummary() {
        let summaryContainer = document.getElementById('ai-search-summary');
        if (!summaryContainer) return;
        
        let keyword = summaryContainer.getAttribute('data-keyword');
        if (!keyword) return;
        
        jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
            action: 'zib_ai_search_summary',
            nonce: '<?php echo wp_create_nonce("zib_ai_nonce"); ?>',
            keyword: keyword
        }, function(response) {
            if (response.success) {
                summaryContainer.innerHTML = '<div class="ai-summary-content">' + response.data.content + '</div>';
            } else {
                summaryContainer.innerHTML = '<div class="ai-summary-error"><i class="fa fa-exclamation-triangle"></i> ' + response.data.message + '</div>';
            }
        });
    }
    
    // 页面加载时从 localStorage 恢复历史对话
    jQuery(document).ready(function($) {
        let history = localStorage.getItem('zib_ai_chat_history');
        if (history) {
            try {
                let messages = JSON.parse(history);
                messages.forEach(function(msg) {
                    zibAppendMessage(msg.role, msg.content);
                });
            } catch(e) {
                console.error('Failed to load chat history');
            }
        }
        
        // 如果是搜索页面，加载 AI 总结
        zibLoadSearchSummary();
    });
    </script>
    <?php
}
add_action('wp_footer', 'zib_ai_frontend_chatbox');

/**
 * 在主题选项中添加 AI 聊天框开关
 */
function zib_ai_add_theme_options($options) {
    // 查找 AI 智能助手部分的位置，在其后添加聊天框选项
    $new_options = array();
    $found_ai_heading = false;
    $added_chatbox_options = false;
    
    foreach ($options as $option) {
        $new_options[] = $option;
        
        // 找到 AI 智能助手的 heading
        if (isset($option['type']) && $option['type'] === 'heading' && 
            isset($option['name']) && strpos($option['name'], 'AI 智能助手') !== false) {
            $found_ai_heading = true;
        }
        
        // 在 AI 功能启用选项后添加聊天框专用选项
        if ($found_ai_heading && !$added_chatbox_options && 
            isset($option['id']) && $option['id'] === 'ai_enabled') {
            
            $new_options[] = array(
                'name' => '显示样式',
                'id' => 'ai_chatbox_style',
                'desc' => '选择 AI 聊天框的显示方式',
                'std' => 'float',
                'type' => 'radio',
                'options' => array(
                    'float' => '悬浮按钮（右下角）',
                    'search' => '仅在搜索页显示',
                    'both' => '同时显示'
                )
            );
            
            $added_chatbox_options = true;
        }
    }
    
    return $new_options;
}
add_filter('of_options', 'zib_ai_add_theme_options');