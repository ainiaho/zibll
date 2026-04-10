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
 * 注意：知识库管理已合并到主题设置的 AI 管理页面中
 * 不再需要独立的子菜单项
 */
// function zib_ai_add_admin_submenu() {
//     // 获取主题框架的菜单 slug (framework_Zibll)
//     $name = '';
//     if (function_exists('framework_option_args') && isset(framework_option_args()['name'])) {
//         $name = framework_option_args()['name'];
//     }
//     if ('' == $name) {
//         $name = get_option('stylesheet');
//         $name = preg_replace("/\W/", "_", strtolower($name));
//     }
//     $parent_slug = 'framework_' . $name;
//     
//     // 添加为框架页面的子菜单
//     add_submenu_page(
//         $parent_slug,
//         '知识库管理',
//         '知识库管理',
//         'manage_options',
//         'zib-knowledge-base',
//         'zib_ai_knowledge_base_page'
//     );
// }
// add_action('admin_menu', 'zib_ai_add_admin_submenu', 20);

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
 * 后台页面 HTML - 知识库管理页面
 */
function zib_ai_knowledge_base_page() {
    ?>
    <div class="wrap">
        <h1>知识库管理</h1>
        
        <div style="display: flex; gap: 20px;">
            <!-- 左侧：AI 设置面板 -->
            <div style="flex: 1; max-width: 600px;">
                <div class="card">
                    <h2>AI 设置</h2>
                    <form method="post" action="options.php">
                        <?php settings_fields('zib_ai_settings'); ?>
                        <?php do_settings_sections('zib_ai_settings'); ?>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="ai_api_key">API 密钥</label></th>
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <input type="password" id="ai_api_key" name="ai_api_key" 
                                               value="<?php echo esc_attr(get_option('ai_api_key')); ?>" 
                                               class="regular-text" placeholder="sk-..." style="flex: 1;">
                                        <button type="button" class="button" id="test-api-key-btn" onclick="zibQuickTestAPIKey()">
                                            🔍 测试连接
                                        </button>
                                    </div>
                                    <p class="description">输入您的 AI API 密钥（如 OpenAI、DeepSeek 等）</p>
                                    <div id="api-key-test-result" style="display: none; margin-top: 10px; padding: 10px; border-radius: 3px; font-size: 13px;"></div>
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
                
                <!-- AI 连接诊断工具 (硅基流动专用) -->
                <div class="card" style="margin-top: 20px; border-left: 4px solid #dc3232;">
                    <h2 style="color: #dc3232;">🔍 实时连接诊断 (硅基流动专用)</h2>
                    <p>直接测试与硅基流动 API 的连接，绕过前端 AJAX 限制，快速定位问题。</p>
                    
                    <div id="zib-ai-diagnostic-result" style="display: none; margin-top: 15px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd;">
                        <pre id="zib-ai-diagnostic-output" style="white-space: pre-wrap; word-wrap: break-word; font-size: 12px;"></pre>
                    </div>
                    
                    <button type="button" class="button button-primary" onclick="zibRunAIDiagnostic()" style="margin-top: 10px;">
                        🚀 开始诊断
                    </button>
                    <p class="description" style="margin-top: 10px;">
                        <strong>注意：</strong>此工具将使用当前保存的配置直接向 API 发送请求。如显示"地区不支持"，请配置上方的代理服务器或使用国内大模型服务。
                    </p>
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
    
    function zibQuickTestAPIKey() {
        let apiKey = jQuery('#ai_api_key').val().trim();
        let apiEndpoint = jQuery('#ai_api_endpoint').val().trim();
        let model = jQuery('#ai_model').val().trim();
        let proxyEnabled = jQuery('#ai_proxy_enabled').is(':checked');
        let proxyUrl = jQuery('#ai_proxy_url').val().trim();
        let resultDiv = jQuery('#api-key-test-result');
        let btn = jQuery('#test-api-key-btn');
        
        if (!apiKey) {
            resultDiv.show().html('<span style="color: #dc3232;">❌ 请先填写 API 密钥</span>');
            return;
        }
        
        resultDiv.show().html('<span style="color: #f0b849;">⏳ 正在测试连接...</span>');
        btn.prop('disabled', true);
        
        jQuery.post(ajaxurl, {
            action: 'zib_quick_test_api',
            nonce: '<?php echo wp_create_nonce("zib_ai_nonce"); ?>',
            api_key: apiKey,
            api_endpoint: apiEndpoint,
            model: model,
            proxy_enabled: proxyEnabled ? 1 : 0,
            proxy_url: proxyUrl
        }, function(response) {
            btn.prop('disabled', false);
            
            if (response.success) {
                let data = response.data;
                if (data.success) {
                    resultDiv.html('<span style="color: #46b450;">✅ 连接成功！</span><br>' +
                        '<small style="color: #666;">HTTP 状态码：' + data.http_code + 
                        ' | 模型：' + data.model + 
                        ' | 耗时：' + data.time + '秒</small><br>' +
                        '<small style="color: #888;">回复：' + data.content.substring(0, 100) + (data.content.length > 100 ? '...' : '') + '</small>');
                } else if (data.is_region_error) {
                    resultDiv.html('<span style="color: #dc3232;">❌ 地区限制</span><br>' +
                        '<small style="color: #666;">检测到地区不支持，请启用代理或更换国内 API 服务</small><br>' +
                        '<small style="color: #888;">错误：' + data.error_message + '</small>');
                } else {
                    resultDiv.html('<span style="color: #dc3232;">❌ 测试失败</span><br>' +
                        '<small style="color: #666;">HTTP 状态码：' + data.http_code + '</small><br>' +
                        '<small style="color: #888;">错误：' + data.error_message + '</small>');
                }
            } else {
                resultDiv.html('<span style="color: #dc3232;">❌ 请求失败</span><br>' +
                    '<small style="color: #888;">' + response.data.message + '</small>');
            }
        }).fail(function(xhr, status, error) {
            btn.prop('disabled', false);
            resultDiv.html('<span style="color: #dc3232;">❌ AJAX 请求失败</span><br>' +
                '<small style="color: #888;">状态：' + status + ' | 错误：' + error + '</small>');
        });
    }
    
    function zibRunAIDiagnostic() {
        let resultDiv = jQuery('#zib-ai-diagnostic-result');
        let outputPre = jQuery('#zib-ai-diagnostic-output');
        let btn = jQuery('button[onclick="zibRunAIDiagnostic()"]');
        
        resultDiv.show();
        btn.prop('disabled', true).text('⏳ 诊断中...');
        outputPre.text('正在发送请求到硅基流动 API，请稍候...\n');
        
        jQuery.post(ajaxurl, {
            action: 'zib_ai_run_diagnostic',
            nonce: '<?php echo wp_create_nonce("zib_ai_nonce"); ?>'
        }, function(response) {
            btn.prop('disabled', false).text('🚀 开始诊断');
            
            if (response.success) {
                let data = response.data;
                let output = '✅ 诊断完成\n\n';
                output += '━━━━━━━━━━━━━━━━━━━━━━\n';
                output += '【配置信息】\n';
                output += 'API 端点：' + data.config.endpoint + '\n';
                output += '模型名称：' + data.config.model + '\n';
                output += '代理状态：' + (data.config.proxy_enabled ? '已启用 (' + data.config.proxy_url + ')' : '未启用') + '\n';
                output += 'API Key: ' + (data.config.api_key ? data.config.api_key.substring(0, 8) + '...' : '未设置') + '\n\n';
                
                output += '━━━━━━━━━━━━━━━━━━━━━━\n';
                output += '【HTTP 状态码】: ' + data.http_code + '\n\n';
                
                if (data.is_region_error) {
                    output += '❌ 检测到地区限制错误！\n';
                    output += '错误信息：' + data.error_message + '\n\n';
                    output += '💡 解决方案:\n';
                    output += '1. 在上方\"启用代理服务器\"选项中配置代理地址\n';
                    output += '2. 或使用国内大模型服务（如 DeepSeek、通义千问等）\n';
                } else if (data.success) {
                    output += '✅ 连接成功！API 返回正常。\n\n';
                    output += '【AI 回复预览】\n';
                    output += data.response_content.substring(0, 500) + (data.response_content.length > 500 ? '...' : '') + '\n';
                } else {
                    output += '❌ 请求失败\n';
                    output += '错误类型：' + data.error_type + '\n';
                    output += '错误信息：' + data.error_message + '\n';
                }
                
                output += '\n━━━━━━━━━━━━━━━━━━━━━━\n';
                output += '【完整响应内容】\n';
                output += data.raw_response;
                
                outputPre.text(output);
            } else {
                outputPre.text('❌ 诊断失败：' + response.data.message);
            }
        }).fail(function(xhr, status, error) {
            btn.prop('disabled', false).text('🚀 开始诊断');
            outputPre.text('❌ AJAX 请求失败\n状态：' + status + '\n错误：' + error);
        });
    }
    </script>
    <?php
}

/**
 * @deprecated 保留旧函数名以防兼容性问题
 */
function zib_ai_admin_page() {
    zib_ai_knowledge_base_page();
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

/**
 * AJAX: 运行 AI 诊断测试
 */
function zib_ai_run_diagnostic_ajax() {
    check_ajax_referer('zib_ai_nonce', 'nonce');
    
    // 获取配置
    $api_key = get_option('ai_api_key', '');
    $api_endpoint = get_option('ai_api_endpoint', 'https://api.siliconflow.cn/v1/chat/completions');
    $model = get_option('ai_model', 'deepseek-ai/DeepSeek-V3');
    $proxy_enabled = get_option('ai_proxy_enabled', false);
    $proxy_url = get_option('ai_proxy_url', '');
    $system_prompt = get_option('ai_system_prompt', '你是一个有帮助的助手。');
    
    $config = array(
        'api_key' => $api_key,
        'endpoint' => $api_endpoint,
        'model' => $model,
        'proxy_enabled' => $proxy_enabled,
        'proxy_url' => $proxy_url
    );
    
    if (empty($api_key)) {
        wp_send_json_error(array('message' => 'API Key 未设置，请在上方配置中填写'));
        return;
    }
    
    // 构建请求
    $request_body = json_encode(array(
        'model' => $model,
        'messages' => array(
            array('role' => 'system', 'content' => $system_prompt),
            array('role' => 'user', 'content' => '请用一句话回复：测试连接是否正常？')
        ),
        'max_tokens' => 50,
        'temperature' => 0.7
    ));
    
    // 初始化 cURL
    $ch = curl_init($api_endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // 配置代理
    if ($proxy_enabled && !empty($proxy_url)) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy_url);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
    
    // 执行请求
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    $result = array(
        'config' => $config,
        'http_code' => $http_code,
        'raw_response' => $response ? $response : ('cURL Error: ' . $curl_error),
        'success' => false,
        'is_region_error' => false,
        'error_type' => '',
        'error_message' => '',
        'response_content' => ''
    );
    
    if ($response) {
        $data = json_decode($response, true);
        
        // 检查是否为地区限制错误
        $error_msg = '';
        if (isset($data['error']) && isset($data['error']['message'])) {
            $error_msg = $data['error']['message'];
        } elseif (is_string($data) && strpos($data, 'not supported') !== false) {
            $error_msg = $data;
        }
        
        $region_keywords = array('not supported', 'Country', 'region', 'territory', '地区', '不支持');
        $is_region_error = false;
        foreach ($region_keywords as $keyword) {
            if (stripos($error_msg, $keyword) !== false || stripos($response, $keyword) !== false) {
                $is_region_error = true;
                break;
            }
        }
        
        $result['is_region_error'] = $is_region_error;
        
        if ($http_code == 200 && isset($data['choices'][0]['message']['content'])) {
            $result['success'] = true;
            $result['response_content'] = $data['choices'][0]['message']['content'];
        } else {
            $result['error_type'] = 'API Error';
            $result['error_message'] = $error_msg ?: ('HTTP ' . $http_code . ': ' . $curl_error);
        }
    } else {
        $result['error_type'] = 'Network Error';
        $result['error_message'] = $curl_error;
    }
    
    wp_send_json_success($result);
}
add_action('wp_ajax_zib_ai_run_diagnostic', 'zib_ai_run_diagnostic_ajax');

/**
 * AJAX: 快速测试 API 连接
 */
function zib_quick_test_api_ajax() {
    check_ajax_referer('zib_ai_nonce', 'nonce');
    
    // 获取配置
    $api_key = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : get_option('ai_api_key', '');
    $api_endpoint = isset($_POST['api_endpoint']) ? sanitize_text_field($_POST['api_endpoint']) : get_option('ai_api_endpoint', 'https://api.siliconflow.cn/v1/chat/completions');
    $model = isset($_POST['model']) ? sanitize_text_field($_POST['model']) : get_option('ai_model', 'deepseek-ai/DeepSeek-V3');
    $proxy_enabled = isset($_POST['proxy_enabled']) && $_POST['proxy_enabled'] == '1';
    $proxy_url = isset($_POST['proxy_url']) ? sanitize_text_field($_POST['proxy_url']) : get_option('ai_proxy_url', '');
    $system_prompt = get_option('ai_system_prompt', '你是一个有帮助的助手。');
    
    if (empty($api_key)) {
        wp_send_json_error(array('message' => 'API Key 未设置'));
        return;
    }
    
    $start_time = microtime(true);
    
    // 构建请求
    $request_body = json_encode(array(
        'model' => $model,
        'messages' => array(
            array('role' => 'system', 'content' => $system_prompt),
            array('role' => 'user', 'content' => '请简短回复：测试')
        ),
        'max_tokens' => 50,
        'temperature' => 0.7
    ));
    
    // 初始化 cURL
    $ch = curl_init($api_endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // 配置代理
    if ($proxy_enabled && !empty($proxy_url)) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy_url);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
    
    // 执行请求
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    $end_time = microtime(true);
    $time_cost = round($end_time - $start_time, 2);
    curl_close($ch);
    
    $result = array(
        'http_code' => $http_code,
        'time' => $time_cost,
        'model' => $model,
        'success' => false,
        'is_region_error' => false,
        'error_message' => '',
        'content' => ''
    );
    
    if ($response) {
        $data = json_decode($response, true);
        
        // 检查是否为地区限制错误
        $error_msg = '';
        if (isset($data['error']) && isset($data['error']['message'])) {
            $error_msg = $data['error']['message'];
        } elseif (is_string($data) && strpos($data, 'not supported') !== false) {
            $error_msg = $data;
        }
        
        $region_keywords = array('not supported', 'Country', 'region', 'territory', '地区', '不支持');
        $is_region_error = false;
        foreach ($region_keywords as $keyword) {
            if (stripos($error_msg, $keyword) !== false || stripos($response, $keyword) !== false) {
                $is_region_error = true;
                break;
            }
        }
        
        $result['is_region_error'] = $is_region_error;
        
        if ($http_code == 200 && isset($data['choices'][0]['message']['content'])) {
            $result['success'] = true;
            $result['content'] = $data['choices'][0]['message']['content'];
        } else {
            $result['error_message'] = $error_msg ?: ('HTTP ' . $http_code . ': ' . $curl_error);
        }
    } else {
        $result['error_message'] = $curl_error;
    }
    
    wp_send_json_success($result);
}
add_action('wp_ajax_zib_quick_test_api', 'zib_quick_test_api_ajax');
// 允许未登录用户测试（如果前端需要）
// add_action('wp_ajax_nopriv_zib_quick_test_api', 'zib_quick_test_api_ajax');

/**
 * 获取知识库管理 HTML（用于嵌入到主题设置页面）
 */
function zib_get_kb_management_html() {
    ob_start();
    ?>
    <div class="kb-management-container" style="margin-top: 15px;">
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <!-- 左侧：知识库管理 -->
            <div style="flex: 1; min-width: 300px;">
                <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px; color: #333;">📚 知识库管理</h3>
                <p style="color: #666; font-size: 13px; margin-bottom: 15px;">管理用于 AI 回答的知识条目</p>
                
                <button type="button" class="button button-primary" onclick="zibOpenKnowledgeModal()" style="margin-bottom: 15px;">
                    ➕ 添加知识条目
                </button>
                
                <div id="zib-knowledge-list" style="max-height: 400px; overflow-y: auto;">
                    <!-- 知识库列表将通过 AJAX 加载 -->
                    <div style="text-align: center; padding: 20px; color: #999;">加载中...</div>
                </div>
            </div>
            
            <!-- 右侧：测试对话 -->
            <div style="flex: 1; min-width: 300px;">
                <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 16px; color: #333;">💬 测试对话</h3>
                <p style="color: #666; font-size: 13px; margin-bottom: 15px;">测试 AI 结合知识库的回答效果</p>
                
                <div id="zib-ai-test-chat" style="border: 1px solid #ddd; padding: 15px; height: 250px; overflow-y: auto; background: #f9f9f9; border-radius: 3px; margin-bottom: 10px;">
                    <div class="chat-message system">在这里输入问题测试 AI 功能...</div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="zib-ai-test-input" placeholder="输入测试问题..." 
                           style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" 
                           onkeypress="if(event.keyCode==13) zibTestAIChat()">
                    <button type="button" class="button" onclick="zibTestAIChat()">发送</button>
                </div>
            </div>
        </div>
        
        <!-- 添加/编辑知识条目模态框 -->
        <div id="zib-knowledge-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100000;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; width: 600px; max-width: 90%; border-radius: 5px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
                <h3 id="knowledge-modal-title" style="margin-top: 0;">添加知识条目</h3>
                <input type="hidden" id="knowledge-item-id" value="">
                
                <p>
                    <label><strong>标题</strong></label><br>
                    <input type="text" id="knowledge-title" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;">
                </p>
                
                <p>
                    <label><strong>分类</strong></label><br>
                    <input type="text" id="knowledge-category" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;" placeholder="例如：常见问题、产品说明">
                </p>
                
                <p>
                    <label><strong>标签</strong>（逗号分隔）</label><br>
                    <input type="text" id="knowledge-tags" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;" placeholder="关键词 1, 关键词 2">
                </p>
                
                <p>
                    <label><strong>内容</strong></label><br>
                    <textarea id="knowledge-content" rows="8" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; box-sizing: border-box;"></textarea>
                </p>
                
                <p>
                    <label><strong>状态</strong></label><br>
                    <select id="knowledge-status" style="padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
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
                border-radius: 3px;
            }
            #zib-knowledge-list .knowledge-item h4 {
                margin: 0 0 10px 0;
                color: #333;
            }
            #zib-knowledge-list .knowledge-meta {
                font-size: 12px;
                color: #666;
                margin-bottom: 10px;
            }
            #zib-knowledge-list .knowledge-item .button {
                margin-right: 5px;
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
                    if (response.data.length === 0) {
                        html = '<p style="text-align: center; color: #999; padding: 20px;">暂无知识条目，点击上方按钮添加</p>';
                    } else {
                        response.data.forEach(function(item) {
                            html += '<div class="knowledge-item">';
                            html += '<h4>' + item.title + '</h4>';
                            html += '<div class="knowledge-meta">分类：' + (item.category || '未分类') + ' | 标签：' + (item.tags || '无') + '</div>';
                            html += '<div style="margin-top: 10px;">';
                            html += '<button class="button" onclick="zibEditKnowledge(' + item.id + ')">编辑</button> ';
                            html += '<button class="button" onclick="zibDeleteKnowledge(' + item.id + ')">删除</button>';
                            html += '</div></div>';
                        });
                    }
                    jQuery('#zib-knowledge-list').html(html);
                } else {
                    jQuery('#zib-knowledge-list').html('<p style="color: red;">加载失败，请刷新页面重试</p>');
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
                    alert('保存失败：' + (response.data || '未知错误'));
                }
            });
        }
        
        function zibEditKnowledge(id) {
            zibOpenKnowledgeModal(id);
        }
        
        function zibDeleteKnowledge(id) {
            if (confirm('确定要删除这个知识条目吗？')) {
                jQuery.post(ajaxurl, {
                    action: 'zib_ai_delete_knowledge',
                    id: id,
                    nonce: '<?php echo wp_create_nonce("zib_ai_nonce"); ?>'
                }, function(response) {
                    if (response.success) {
                        alert('删除成功！');
                        zibLoadKnowledgeList();
                    } else {
                        alert('删除失败：' + (response.data || '未知错误'));
                    }
                });
            }
        }
        
        function zibTestAIChat() {
            let input = jQuery('#zib-ai-test-input');
            let message = input.val().trim();
            if (!message) return;
            
            // 添加用户消息
            jQuery('#zib-ai-test-chat').append('<div class="chat-message user">' + message + '</div>');
            input.val('');
            
            // 滚动到底部
            let chatBox = document.getElementById('zib-ai-test-chat');
            chatBox.scrollTop = chatBox.scrollHeight;
            
            // 显示加载中
            jQuery('#zib-ai-test-chat').append('<div class="chat-message system" id="chat-loading">AI 正在思考...</div>');
            chatBox.scrollTop = chatBox.scrollHeight;
            
            // 发送请求
            jQuery.post(ajaxurl, {
                action: 'zib_ai_chat',
                message: message,
                nonce: '<?php echo wp_create_nonce("zib_ai_nonce"); ?>'
            }, function(response) {
                jQuery('#chat-loading').remove();
                
                if (response.success) {
                    jQuery('#zib-ai-test-chat').append('<div class="chat-message assistant">' + response.data + '</div>');
                } else {
                    jQuery('#zib-ai-test-chat').append('<div class="chat-message system" style="color: red;">错误：' + (response.data || '请求失败') + '</div>');
                }
                chatBox.scrollTop = chatBox.scrollHeight;
            });
        }
        </script>
    </div>
    <?php
    return ob_get_clean();
}
