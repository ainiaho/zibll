<?php
/**
 * AI 对话与知识库功能
 * 
 * @package Zibll Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AI 配置选项
 */
class Zib_AI_Config {
    
    /**
     * 获取 API 密钥
     */
    public static function get_api_key() {
        return zib_get_option('ai_api_key', '');
    }
    
    /**
     * 获取 API 端点
     */
    public static function get_api_endpoint() {
        return zib_get_option('ai_api_endpoint', 'https://api.openai.com/v1/chat/completions');
    }
    
    /**
     * 获取模型名称
     */
    public static function get_model() {
        return zib_get_option('ai_model', 'gpt-3.5-turbo');
    }
    
    /**
     * 获取系统提示词
     */
    public static function get_system_prompt() {
        $default = '你是一个有帮助的助手，基于知识库内容回答用户问题。如果知识库中没有相关信息，请如实告知。';
        return zib_get_option('ai_system_prompt', $default);
    }
    
    /**
     * 是否启用知识库
     */
    public static function is_knowledge_base_enabled() {
        return zib_get_option('ai_knowledge_base_enabled', false);
    }
    
    /**
     * 获取最大上下文长度
     */
    public static function get_max_tokens() {
        return zib_get_option('ai_max_tokens', 2000);
    }
}

/**
 * 知识库管理
 */
class Zib_Knowledge_Base {
    
    /**
     * 初始化数据库表
     */
    public static function init_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zib_ai_knowledge';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            content longtext NOT NULL,
            category varchar(100) DEFAULT '',
            tags varchar(500) DEFAULT '',
            status varchar(20) DEFAULT 'publish',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY category (category),
            KEY status (status),
            FULLTEXT KEY search_index (title, content, tags)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * 添加知识条目
     */
    public static function add_item($data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zib_ai_knowledge';
        
        $defaults = array(
            'title' => '',
            'content' => '',
            'category' => '',
            'tags' => '',
            'status' => 'publish'
        );
        
        $data = wp_parse_args($data, $defaults);
        
        $result = $wpdb->insert($table_name, $data);
        
        if ($result === false) {
            return new WP_Error('db_error', $wpdb->last_error);
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * 更新知识条目
     */
    public static function update_item($id, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zib_ai_knowledge';
        
        $result = $wpdb->update($table_name, $data, array('id' => $id));
        
        if ($result === false) {
            return new WP_Error('db_error', $wpdb->last_error);
        }
        
        return true;
    }
    
    /**
     * 删除知识条目
     */
    public static function delete_item($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zib_ai_knowledge';
        
        return $wpdb->delete($table_name, array('id' => $id));
    }
    
    /**
     * 搜索知识库
     */
    public static function search($keyword, $limit = 5) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zib_ai_knowledge';
        
        $keyword = esc_sql($keyword);
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT id, title, content, category, tags 
             FROM $table_name 
             WHERE status = 'publish' 
             AND (MATCH(title, content, tags) AGAINST(%s IN NATURAL LANGUAGE MODE)
                  OR title LIKE %s 
                  OR content LIKE %s)
             ORDER BY id DESC 
             LIMIT %d",
            $keyword,
            '%' . $keyword . '%',
            '%' . $keyword . '%',
            $limit
        ));
        
        return $results;
    }
    
    /**
     * 获取相关知识用于 AI 上下文
     */
    public static function get_relevant_context($query, $limit = 3) {
        $results = self::search($query, $limit);
        
        $context = array();
        foreach ($results as $item) {
            $context[] = array(
                'title' => $item->title,
                'content' => wp_trim_words($item->content, 200)
            );
        }
        
        return $context;
    }
    
    /**
     * 获取所有分类
     */
    public static function get_categories() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'zib_ai_knowledge';
        
        return $wpdb->get_col("SELECT DISTINCT category FROM $table_name WHERE status = 'publish' AND category != '' ORDER BY category");
    }
}

/**
 * AI API 处理
 */
class Zib_AI_Handler {
    
    /**
     * 发送请求到 AI API
     */
    public static function chat($messages, $options = array()) {
        $api_key = Zib_AI_Config::get_api_key();
        $endpoint = Zib_AI_Config::get_api_endpoint();
        $model = Zib_AI_Config::get_model();
        $max_tokens = Zib_AI_Config::get_max_tokens();
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', '未配置 API 密钥');
        }
        
        $body = array(
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $max_tokens,
            'temperature' => 0.7
        );
        
        $args = array(
            'method' => 'POST',
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key
            ),
            'body' => json_encode($body),
            'timeout' => 30
        );
        
        $response = wp_remote_request($endpoint, $args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error('api_error', $body['error']['message']);
        }
        
        if (isset($body['choices'][0]['message']['content'])) {
            return array(
                'content' => $body['choices'][0]['message']['content'],
                'usage' => isset($body['usage']) ? $body['usage'] : array()
            );
        }
        
        return new WP_Error('invalid_response', 'API 返回格式错误');
    }
    
    /**
     * 处理用户问题（包含知识库检索）
     */
    public static function process_query($user_message, $conversation_history = array()) {
        $system_prompt = Zib_AI_Config::get_system_prompt();
        
        // 如果启用了知识库，检索相关内容
        if (Zib_AI_Config::is_knowledge_base_enabled()) {
            $knowledge = Zib_Knowledge_Base::get_relevant_context($user_message);
            
            if (!empty($knowledge)) {
                $knowledge_context = "相关知识库内容：\n\n";
                foreach ($knowledge as $item) {
                    $knowledge_context .= "【{$item['title']}】\n{$item['content']}\n\n";
                }
                
                $system_prompt .= "\n\n" . $knowledge_context;
            }
        }
        
        // 构建消息数组
        $messages = array(
            array('role' => 'system', 'content' => $system_prompt)
        );
        
        // 添加历史对话
        $messages = array_merge($messages, $conversation_history);
        
        // 添加当前问题
        $messages[] = array('role' => 'user', 'content' => $user_message);
        
        return self::chat($messages);
    }
}

/**
 * AJAX 处理函数
 */
function zib_ai_chat_ajax() {
    check_ajax_referer('zib_ai_nonce', 'nonce');
    
    $message = sanitize_text_field($_POST['message'] ?? '');
    $history = isset($_POST['history']) ? json_decode(stripslashes($_POST['history']), true) : array();
    
    if (empty($message)) {
        wp_send_json_error(array('message' => '请输入问题'));
    }
    
    $result = Zib_AI_Handler::process_query($message, $history);
    
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }
    
    wp_send_json_success(array(
        'content' => $result['content'],
        'usage' => $result['usage'] ?? array()
    ));
}
add_action('wp_ajax_zib_ai_chat', 'zib_ai_chat_ajax');
add_action('wp_ajax_nopriv_zib_ai_chat', 'zib_ai_chat_ajax');

/**
 * 初始化
 */
function zib_ai_init() {
    Zib_Knowledge_Base::init_table();
}
add_action('after_switch_theme', 'zib_ai_init');
add_action('admin_init', 'zib_ai_init');
