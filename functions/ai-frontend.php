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
    if (!_pz('ai_chatbox_enabled', false)) {
        return;
    }
    
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
                zibAppendMessage('assistant', '抱歉，出现错误：' + response.data.message);
            }
            
            // 滚动到底部
            let chatMessages = document.getElementById('zib-ai-chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
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
    });
    </script>
}
add_action('wp_footer', 'zib_ai_frontend_chatbox');

/**
 * 在主题选项中添加 AI 聊天框开关
 */
function zib_ai_add_theme_options($sections) {
    $sections[] = array(
        'title' => 'AI 聊天框',
        'id' => 'ai_chatbox',
        'icon' => 'fa-robot',
        'fields' => array(
            array(
                'title' => '启用 AI 聊天框',
                'desc' => '在网站右下角显示 AI 智能助手聊天窗口',
                'id' => 'ai_chatbox_enabled',
                'type' => 'switch',
                'default' => false
            ),
            array(
                'title' => '欢迎语',
                'desc' => '聊天框打开时显示的欢迎消息',
                'id' => 'ai_chatbox_welcome',
                'type' => 'text',
                'default' => '您好！我是 AI 助手，有什么可以帮您的吗？'
            )
        )
    );
    
    return $sections;
}
// 如果主题支持选项扩展，则添加此过滤器
// add_filter('zib_theme_options_sections', 'zib_ai_add_theme_options');

?>