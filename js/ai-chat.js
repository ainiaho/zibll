/**
 * AI 智能助手前端功能
 * 提供 AI 聊天界面和交互功能
 */

jQuery(function($) {
    'use strict';

    // AI 聊天配置
    const aiConfig = {
        ajaxUrl: zib_ajax.ajaxurl,
        nonce: zib_ajax.nonce,
        enabled: zib_ajax.ai_enabled || false
    };

    // 创建 AI 聊天窗口 HTML
    function createAiChatBox() {
        if (!aiConfig.enabled) return;

        const chatBoxHtml = `
            <div id="zib-ai-chat" class="zib-ai-chat">
                <div class="ai-chat-toggle">
                    <svg viewBox="0 0 24 24" width="24" height="24">
                        <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12c0 1.54.36 3 .97 4.29L2 22l5.71-.97C9 21.64 10.46 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm0 18c-1.38 0-2.67-.33-3.82-.91l-.27-.14-2.86.49.49-2.86-.14-.27C4.33 14.67 4 13.38 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8-3.59 8-8 8z"/>
                        <path fill="currentColor" d="M12 6c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/>
                    </svg>
                    <span class="toggle-text">AI 助手</span>
                </div>
                <div class="ai-chat-window">
                    <div class="ai-chat-header">
                        <h3>AI 智能助手</h3>
                        <button class="ai-chat-close">&times;</button>
                    </div>
                    <div class="ai-chat-messages">
                        <div class="ai-message ai-message-system">
                            <div class="message-content">您好！我是 AI 智能助手，有什么可以帮助您的吗？</div>
                        </div>
                    </div>
                    <div class="ai-chat-input">
                        <textarea placeholder="输入您的问题..." rows="2"></textarea>
                        <button class="ai-send-btn">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                            发送
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('body').append(chatBoxHtml);
        initAiEvents();
    }

    // 初始化事件
    function initAiEvents() {
        const $chat = $('#zib-ai-chat');
        const $toggle = $chat.find('.ai-chat-toggle');
        const $close = $chat.find('.ai-chat-close');
        const $window = $chat.find('.ai-chat-window');
        const $sendBtn = $chat.find('.ai-send-btn');
        const $input = $chat.find('textarea');
        const $messages = $chat.find('.ai-chat-messages');

        // 切换聊天窗口
        $toggle.on('click', function() {
            $window.toggleClass('active');
            $toggle.toggleClass('active');
        });

        // 关闭按钮
        $close.on('click', function() {
            $window.removeClass('active');
            $toggle.removeClass('active');
        });

        // 发送消息
        $sendBtn.on('click', function() {
            sendMessage();
        });

        // 回车发送 (Shift+Enter 换行)
        $input.on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // 发送消息函数
        function sendMessage() {
            const message = $input.val().trim();
            if (!message) return;

            // 添加用户消息
            appendMessage(message, 'user');
            $input.val('');

            // 显示加载状态
            const loadingId = appendLoading();

            // 获取对话历史
            const history = getConversationHistory();

            // 发送 AJAX 请求
            $.ajax({
                url: aiConfig.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ai_chat',
                    nonce: aiConfig.nonce,
                    message: message,
                    history: JSON.stringify(history)
                },
                success: function(response) {
                    removeLoading(loadingId);
                    
                    if (response.error) {
                        appendMessage('错误：' + response.msg, 'error');
                    } else {
                        appendMessage(response.response, 'ai');
                    }
                },
                error: function() {
                    removeLoading(loadingId);
                    appendMessage('请求失败，请稍后重试', 'error');
                }
            });
        }

        // 追加消息到聊天窗口
        function appendMessage(content, type) {
            const messageHtml = `
                <div class="ai-message ai-message-${type}">
                    <div class="message-avatar">
                        ${type === 'user' ? '<svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>' : 
                          type === 'ai' ? '<svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>' : 
                          '<svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>'}
                    </div>
                    <div class="message-content">${formatMessage(content)}</div>
                </div>
            `;
            $messages.append(messageHtml);
            scrollToBottom();
        }

        // 添加加载动画
        function appendLoading() {
            const id = 'loading-' + Date.now();
            const loadingHtml = `
                <div class="ai-message ai-message-loading" id="${id}">
                    <div class="message-avatar">
                        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
                    </div>
                    <div class="message-content">
                        <div class="typing-indicator">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                </div>
            `;
            $messages.append(loadingHtml);
            scrollToBottom();
            return id;
        }

        // 移除加载动画
        function removeLoading(id) {
            $('#' + id).remove();
        }

        // 滚动到底部
        function scrollToBottom() {
            $messages.scrollTop($messages[0].scrollHeight);
        }

        // 格式化消息（简单处理换行和链接）
        function formatMessage(content) {
            return content
                .replace(/\n/g, '<br>')
                .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
        }

        // 获取对话历史
        function getConversationHistory() {
            const history = [];
            $messages.find('.ai-message:not(.ai-message-system):not(.ai-message-loading)').each(function() {
                const $msg = $(this);
                if ($msg.hasClass('ai-message-user')) {
                    history.push({
                        role: 'user',
                        content: $msg.find('.message-content').text()
                    });
                } else if ($msg.hasClass('ai-message-ai')) {
                    history.push({
                        role: 'assistant',
                        content: $msg.find('.message-content').text()
                    });
                }
            });
            return history.slice(-10); // 只保留最近 10 条
        }
    }

    // 初始化 AI 聊天
    createAiChatBox();
});
