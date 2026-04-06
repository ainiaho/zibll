<?php
/**
 * AI 与知识库管理后台页面
 * 
 * @package Zibll Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 添加管理菜单
 */
function zib_ai_add_admin_menu() {
    // 获取正确的父菜单 slug
    $parent_slug = 'framework_Zibll';
    
    add_submenu_page(
        $parent_slug,
        'AI 与知识库',
        'AI 与知识库',
        'manage_options',
        'zib-ai',
        'zib_ai_admin_page'
    );
}
add_action('admin_menu', 'zib_ai_add_admin_menu');

/**
 * 注册设置
 */
function zib_ai_register_settings() {
    register_setting('zib_ai_settings', 'ai_api_key');
    register_setting('zib_ai_settings', 'ai_api_endpoint');
    register_setting('zib_ai_settings', 'ai_model');
    register_setting('zib_ai_settings', 'ai_system_prompt');
    register_setting('zib_ai_settings', 'ai_knowledge_base_enabled');
    register_setting('zib_ai_settings', 'ai_max_tokens');
    register_setting('zib_ai_settings', 'ai_proxy_enabled');
    register_setting('zib_ai_settings', 'ai_proxy_url');
}
add_action('admin_init', 'zib_ai_register_settings');

/**
 * 后台页面 HTML
 */
function zib_ai_admin_page() {
    ?>
    <div class="wrap">
        <h1>AI 与知识库管理</h1>
        
        <div style="display: flex; gap: 20px;">
            <!-- 左侧：设置面板 -->
            <div style="flex: 1; max-width: 600px;">
                <form method="post" action="options.php">
                    <?php settings_fields('zib_ai_settings'); ?>
                    <?php do_settings_sections('zib_ai_settings'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="ai_api_key">API 密钥</label></th>
                            <td>
                                <input type="password" id="ai_api_key" name="ai_api_key" 
                                       value="<?php echo esc_attr(get_option('ai_api_key')); ?>" 
                                       class="regular-text" placeholder="sk-...">
                                <p class="description">输入您的 AI API 密钥（如 OpenAI、DeepSeek 等）</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="ai_api_endpoint">API 端点</label></th>
                            <td>
                                <input type="url" id="ai_api_endpoint" name="ai_api_endpoint" 
                                       value="<?php echo esc_attr(get_option('ai_api_endpoint', 'https://api.openai.com/v1/chat/completions')); ?>" 
                                       class="large-text">
                                <p class="description">AI API 的请求地址</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="ai_model">模型名称</label></th>
                            <td>
                                <input type="text" id="ai_model" name="ai_model" 
                                       value="<?php echo esc_attr(get_option('ai_model', 'gpt-3.5-turbo')); ?>" 
                                       class="regular-text" placeholder="gpt-3.5-turbo">
                                <p class="description">例如：gpt-3.5-turbo, gpt-4, deepseek-chat 等</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="ai_max_tokens">最大 Token 数</label></th>
                            <td>
                                <input type="number" id="ai_max_tokens" name="ai_max_tokens" 
                                       value="<?php echo esc_attr(get_option('ai_max_tokens', 2000)); ?>" 
                                       class="small-text" min="100" max="8000">
                                <p class="description">单次回复的最大 Token 数量</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="ai_system_prompt">系统提示词</label></th>
                            <td>
                                <textarea id="ai_system_prompt" name="ai_system_prompt" 
                                          rows="4" class="large-text"><?php echo esc_textarea(get_option('ai_system_prompt', '你是一个有帮助的助手，基于知识库内容回答用户问题。如果知识库中没有相关信息，请如实告知。')); ?></textarea>
                                <p class="description">设定 AI 的角色和行为准则</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">启用知识库</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="ai_knowledge_base_enabled" value="1" 
                                           <?php checked(get_option('ai_knowledge_base_enabled'), 1); ?>>
                                    启用知识库检索增强
                                </label>
                                <p class="description">启用后，AI 会先检索知识库相关内容再回答</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="ai_proxy_enabled">启用代理</label></th>
                            <td>
                                <label>
                                    <input type="checkbox" id="ai_proxy_enabled" name="ai_proxy_enabled" value="1" 
                                           <?php checked(get_option('ai_proxy_enabled'), 1); ?>>
                                    启用代理服务器
                                </label>
                                <p class="description">如果 API 服务商限制您所在的地区，请启用代理</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="ai_proxy_url">代理地址</label></th>
                            <td>
                                <input type="text" id="ai_proxy_url" name="ai_proxy_url" 
                                       value="<?php echo esc_attr(get_option('ai_proxy_url', '')); ?>" 
                                       class="large-text" placeholder="http://127.0.0.1:7890">
                                <p class="description">输入您的代理服务器地址（如：http://127.0.0.1:7890）</p>
                            </td>
                        </tr>
                    </table>
                    
                    <?php submit_button('保存设置'); ?>
                </form>
            </div>
            
            <!-- 右侧：知识库管理 -->
            <div style="flex: 1;">
                <div class="card">
                    <h2>知识库管理</h2>
                    <p>管理用于 AI 回答的知识条目</p>
                    
                    <button type="button" class="button button-primary" onclick="zibOpenKnowledgeModal()">
                        添加知识条目
                    </button>
                    
                    <div id="zib-knowledge-list" style="margin-top: 20px;">
                        <!-- 知识库列表将通过 AJAX 加载 -->
                    </div>
                </div>
                
                <div class="card" style="margin-top: 20px;">
                    <h2>测试对话</h2>
                    <div id="zib-ai-test-chat" style="border: 1px solid #ddd; padding: 15px; height: 300px; overflow-y: auto; background: #f9f9f9;">
                        <div class="chat-message system">在这里测试 AI 对话功能...</div>
                    </div>
                    <div style="margin-top: 10px; display: flex; gap: 10px;">
                        <input type="text" id="zib-ai-test-input" placeholder="输入测试问题..." 
                               style="flex: 1; padding: 8px;" onkeypress="if(event.keyCode==13) zibTestAIChat()">
                        <button type="button" class="button" onclick="zibTestAIChat()">发送</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 添加/编辑知识条目模态框 -->
    <div id="zib-knowledge-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; width: 600px; max-width: 90%; border-radius: 5px;">
            <h3 id="knowledge-modal-title">添加知识条目</h3>
            <input type="hidden" id="knowledge-item-id" value="">
            
            <p>
                <label><strong>标题</strong></label><br>
                <input type="text" id="knowledge-title" style="width: 100%; padding: 8px;">
            </p>
            
            <p>
                <label><strong>分类</strong></label><br>
                <input type="text" id="knowledge-category" style="width: 100%; padding: 8px;" placeholder="例如：常见问题、产品说明">
            </p>
            
            <p>
                <label><strong>标签</strong>（逗号分隔）</label><br>
                <input type="text" id="knowledge-tags" style="width: 100%; padding: 8px;" placeholder="关键词 1, 关键词 2">
            </p>
            
            <p>
                <label><strong>内容</strong></label><br>
                <textarea id="knowledge-content" rows="8" style="width: 100%; padding: 8px;"></textarea>
            </p>
            
            <p>
                <label><strong>状态</strong></label><br>
                <select id="knowledge-status" style="padding: 8px;">
                    <option value="publish">发布</option>
                    <option value="draft">草稿</option>
                </select>
            </p>
            
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="button" onclick="zibCloseKnowledgeModal()">取消</button>
                <button type="button" class="button button-primary" onclick="zibSaveKnowledge()">保存</button>
            </div>
        </div>
    </div>
    
    <style>
        .chat-message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .chat-message.user {
            background: #e3f2fd;
            margin-left: 20%;
        }
        .chat-message.assistant {
            background: #f5f5f5;
            margin-right: 20%;
        }
        .chat-message.system {
            color: #666;
            font-style: italic;
        }
        #zib-knowledge-list .knowledge-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            background: white;
        }
        #zib-knowledge-list .knowledge-item h4 {
            margin: 0 0 10px 0;
        }
        #zib-knowledge-list .knowledge-meta {
            font-size: 12px;
            color: #666;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // 加载知识库列表
        zibLoadKnowledgeList();
    });
    
    function zibLoadKnowledgeList() {
        jQuery.post(ajaxurl, {
            action: 'zib_ai_get_knowledge_list',
            nonce: '<?php echo wp_create_nonce("zib_ai_nonce"); ?>'
        }, function(response) {
            if (response.success) {
                let html = '';
                response.data.forEach(function(item) {
                    html += '<div class="knowledge-item">';
                    html += '<h4>' + item.title + '</h4>';
                    html += '<div class="knowledge-meta">分类：' + (item.category || '未分类') + ' | 标签：' + (item.tags || '无') + '</div>';
                    html += '<div style="margin-top: 10px;">';
                    html += '<button class="button" onclick="zibEditKnowledge(' + item.id + ')">编辑</button> ';
                    html += '<button class="button" onclick="zibDeleteKnowledge(' + item.id + ')">删除</button>';
                    html += '</div></div>';
                });
                jQuery('#zib-knowledge-list').html(html || '<p>暂无知识条目</p>');
            }
        });
    }
    
    function zibOpenKnowledgeModal(id = null) {
        jQuery('#knowledge-modal-title').text(id ? '编辑知识条目' : '添加知识条目');
        jQuery('#knowledge-item-id').val(id || '');
        
        if (id) {
            jQuery.post(ajaxurl, {
                action: 'zib_ai_get_knowledge_item',
                id: id,
                nonce: '<?php echo wp_create_nonce("zib_ai_nonce"); ?>'
            }, function(response) {
                if (response.success) {
                    let item = response.data;
                    jQuery('#knowledge-title').val(item.title);
                    jQuery('#knowledge-category').val(item.category);
                    jQuery('#knowledge-tags').val(item.tags);
                    jQuery('#knowledge-content').val(item.content);
                    jQuery('#knowledge-status').val(item.status);
                }
            });
        } else {
            jQuery('#knowledge-title').val('');
            jQuery('#knowledge-category').val('');
            jQuery('#knowledge-tags').val('');
            jQuery('#knowledge-content').val('');
            jQuery('#knowledge-status').val('publish');
        }
        
        jQuery('#zib-knowledge-modal').show();
    }
    
    function zibCloseKnowledgeModal() {
        jQuery('#zib-knowledge-modal').hide();
    }
    
    function zibSaveKnowledge() {
        let id = jQuery('#knowledge-item-id').val();
        let data = {
            action: id ? 'zib_ai_update_knowledge' : 'zib_ai_add_knowledge',
            nonce: '<?php echo wp_create_nonce("zib_ai_nonce"); ?>',
            title: jQuery('#knowledge-title').val(),
            category: jQuery('#knowledge-category').val(),
            tags: jQuery('#knowledge-tags').val(),
            content: jQuery('#knowledge-content').val(),
            status: jQuery('#knowledge-status').val()
        };
        
        if (id) {
            data.id = id;
        }
        
        jQuery.post(ajaxurl, data, function(response) {
            if (response.success) {
                alert('保存成功！');
                zibCloseKnowledgeModal();
                zibLoadKnowledgeList();
            } else {
                alert('保存失败：' + response.data.message);
            }
        });
    }
    
    function zibEditKnowledge(id) {
        zibOpenKnowledgeModal(id);
    }
    
    function zibDeleteKnowledge(id) {
        if (!confirm('确定要删除这个知识条目吗？')) return;
        
        jQuery.post(ajaxurl, {
            action: 'zib_ai_delete_knowledge',
            id: id,
            nonce: '<?php echo wp_create_nonce("zib_ai_nonce"); ?>'
        }, function(response) {
            if (response.success) {
                zibLoadKnowledgeList();
            } else {
                alert('删除失败：' + response.data.message);
            }
        });
    }
    
    function zibTestAIChat() {
        let input = jQuery('#zib-ai-test-input');
        let message = input.val().trim();
        if (!message) return;
        
        let chatBox = jQuery('#zib-ai-test-chat');
        chatBox.append('<div class="chat-message user">' + message + '</div>');
        input.val('');
        
        jQuery.post(ajaxurl, {
            action: 'zib_ai_chat',
            nonce: '<?php echo wp_create_nonce("zib_ai_nonce"); ?>',
            message: message,
            history: '[]'
        }, function(response) {
            if (response.success) {
                chatBox.append('<div class="chat-message assistant">' + response.data.content + '</div>');
            } else {
                chatBox.append('<div class="chat-message assistant" style="color: red;">错误：' + response.data.message + '</div>');
            }
            chatBox.scrollTop(chatBox[0].scrollHeight);
        });
    }
    </script>
    <?php
}

/**
 * AJAX: 获取知识库列表
 */
function zib_ai_get_knowledge_list_ajax() {
    check_ajax_referer('zib_ai_nonce', 'nonce');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'zib_ai_knowledge';
    
    $results = $wpdb->get_results("SELECT id, title, category, tags, status, created_at FROM $table_name ORDER BY created_at DESC LIMIT 50");
    
    wp_send_json_success($results);
}
add_action('wp_ajax_zib_ai_get_knowledge_list', 'zib_ai_get_knowledge_list_ajax');

/**
 * AJAX: 获取单个知识条目
 */
function zib_ai_get_knowledge_item_ajax() {
    check_ajax_referer('zib_ai_nonce', 'nonce');
    
    $id = intval($_POST['id'] ?? 0);
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'zib_ai_knowledge';
    
    $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
    
    if ($item) {
        wp_send_json_success($item);
    } else {
        wp_send_json_error(array('message' => '未找到该条目'));
    }
}
add_action('wp_ajax_zib_ai_get_knowledge_item', 'zib_ai_get_knowledge_item_ajax');

/**
 * AJAX: 添加知识条目
 */
function zib_ai_add_knowledge_ajax() {
    check_ajax_referer('zib_ai_nonce', 'nonce');
    
    $data = array(
        'title' => sanitize_text_field($_POST['title'] ?? ''),
        'content' => wp_kses_post($_POST['content'] ?? ''),
        'category' => sanitize_text_field($_POST['category'] ?? ''),
        'tags' => sanitize_text_field($_POST['tags'] ?? ''),
        'status' => sanitize_text_field($_POST['status'] ?? 'publish')
    );
    
    if (empty($data['title']) || empty($data['content'])) {
        wp_send_json_error(array('message' => '标题和内容不能为空'));
    }
    
    $result = Zib_Knowledge_Base::add_item($data);
    
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }
    
    wp_send_json_success(array('id' => $result));
}
add_action('wp_ajax_zib_ai_add_knowledge', 'zib_ai_add_knowledge_ajax');

/**
 * AJAX: 更新知识条目
 */
function zib_ai_update_knowledge_ajax() {
    check_ajax_referer('zib_ai_nonce', 'nonce');
    
    $id = intval($_POST['id'] ?? 0);
    
    $data = array(
        'title' => sanitize_text_field($_POST['title'] ?? ''),
        'content' => wp_kses_post($_POST['content'] ?? ''),
        'category' => sanitize_text_field($_POST['category'] ?? ''),
        'tags' => sanitize_text_field($_POST['tags'] ?? ''),
        'status' => sanitize_text_field($_POST['status'] ?? 'publish')
    );
    
    if (empty($data['title']) || empty($data['content'])) {
        wp_send_json_error(array('message' => '标题和内容不能为空'));
    }
    
    $result = Zib_Knowledge_Base::update_item($id, $data);
    
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }
    
    wp_send_json_success();
}
add_action('wp_ajax_zib_ai_update_knowledge', 'zib_ai_update_knowledge_ajax');

/**
 * AJAX: 删除知识条目
 */
function zib_ai_delete_knowledge_ajax() {
    check_ajax_referer('zib_ai_nonce', 'nonce');
    
    $id = intval($_POST['id'] ?? 0);
    
    $result = Zib_Knowledge_Base::delete_item($id);
    
    if ($result === false) {
        global $wpdb;
        wp_send_json_error(array('message' => $wpdb->last_error));
    }
    
    wp_send_json_success();
}
add_action('wp_ajax_zib_ai_delete_knowledge', 'zib_ai_delete_knowledge_ajax');
