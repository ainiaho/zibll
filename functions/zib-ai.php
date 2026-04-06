<?php
/**
 * AI 智能助手功能模块
 * 提供 AI 对话、智能问答功能
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AI 配置选项
 */
function zib_ai_get_options() {
    return array(
        'api_key' => _pz('ai_api_key', ''),
        'api_url' => _pz('ai_api_url', 'https://api.openai.com/v1/chat/completions'),
        'model' => _pz('ai_model', 'gpt-3.5-turbo'),
        'max_tokens' => _pz('ai_max_tokens', 1000),
        'temperature' => _pz('ai_temperature', 0.7),
        'system_prompt' => _pz('ai_system_prompt', '你是一个有帮助的助手，服务于一个 WordPress 网站。请友好、专业地回答用户的问题。'),
        'enabled' => _pz('ai_enabled', false),
        'knowledge_base_enabled' => _pz('kb_enabled', false),
    );
}

/**
 * 调用 AI API
 */
function zib_ai_chat($messages, $options = array()) {
    $ai_options = zib_ai_get_options();
    
    if (!$ai_options['enabled']) {
        return array('error' => 'AI 功能未启用');
    }
    
    $options = wp_parse_args($options, array(
        'model' => $ai_options['model'],
        'max_tokens' => $ai_options['max_tokens'],
        'temperature' => $ai_options['temperature'],
    ));
    
    // 添加系统提示
    array_unshift($messages, array(
        'role' => 'system',
        'content' => $ai_options['system_prompt']
    ));
    
    $request_body = array(
        'model' => $options['model'],
        'messages' => $messages,
        'max_tokens' => (int)$options['max_tokens'],
        'temperature' => (float)$options['temperature'],
    );
    
    $args = array(
        'body' => json_encode($request_body),
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $ai_options['api_key'],
        ),
        'timeout' => 30,
        'sslverify' => false,
    );
    
    $response = wp_remote_post($ai_options['api_url'], $args);
    
    if (is_wp_error($response)) {
        return array('error' => $response->get_error_message());
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (isset($data['error'])) {
        return array('error' => $data['error']['message']);
    }
    
    if (isset($data['choices'][0]['message']['content'])) {
        return array(
            'response' => $data['choices'][0]['message']['content'],
            'usage' => isset($data['usage']) ? $data['usage'] : array(),
        );
    }
    
    return array('error' => '无法解析 AI 响应');
}

/**
 * 搜索知识库
 */
function zib_kb_search($query, $limit = 5) {
    if (!_pz('kb_enabled', false)) {
        return array();
    }
    
    $args = array(
        'post_type' => 'zib_knowledge',
        'posts_per_page' => $limit,
        's' => $query,
        'post_status' => 'publish',
    );
    
    $query_result = new WP_Query($args);
    $results = array();
    
    if ($query_result->have_posts()) {
        while ($query_result->have_posts()) {
            $query_result->the_post();
            $results[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'excerpt' => get_the_excerpt(),
                'permalink' => get_permalink(),
                'category' => get_the_terms(get_the_ID(), 'zib_kb_category') ?: array(),
            );
        }
        wp_reset_postdata();
    }
    
    return $results;
}

/**
 * 获取相关知识库内容用于 AI 上下文
 */
function zib_kb_get_context($query, $limit = 3) {
    $results = zib_kb_search($query, $limit);
    $context = '';
    
    foreach ($results as $result) {
        $content = get_post_field('post_content', $result['id']);
        $context .= "相关知识：{$result['title']}\n";
        $context .= wp_trim_words($content, 100) . "\n\n";
    }
    
    return $context;
}

/**
 * AI 聊天增强版（包含知识库）
 */
function zib_ai_chat_with_kb($user_message, $conversation_history = array()) {
    $ai_options = zib_ai_get_options();
    
    // 搜索相关知识库
    $kb_context = '';
    if ($ai_options['knowledge_base_enabled']) {
        $kb_context = zib_kb_get_context($user_message);
    }
    
    // 构建消息
    $messages = $conversation_history;
    $messages[] = array(
        'role' => 'user',
        'content' => ($kb_context ? "参考以下知识：\n{$kb_context}\n" : '') . $user_message
    );
    
    return zib_ai_chat($messages);
}

/**
 * 注册知识库文章类型
 */
function zib_kb_register_post_type() {
    if (!_pz('kb_enabled', false)) {
        return;
    }
    
    $labels = array(
        'name' => '知识库',
        'singular_name' => '知识文章',
        'add_new' => '添加新知识',
        'add_new_item' => '添加新知识文章',
        'edit_item' => '编辑知识文章',
        'new_item' => '新知识文章',
        'view_item' => '查看知识文章',
        'search_items' => '搜索知识库',
        'not_found' => '未找到知识文章',
        'not_found_in_trash' => '回收站中没有知识文章',
        'menu_name' => '知识库',
    );
    
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'knowledge'),
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-book',
        'menu_position' => 5,
    );
    
    register_post_type('zib_knowledge', $args);
    
    // 注册分类法
    $cat_labels = array(
        'name' => '知识分类',
        'singular_name' => '知识分类',
        'search_items' => '搜索分类',
        'all_items' => '所有分类',
        'edit_item' => '编辑分类',
        'update_item' => '更新分类',
        'add_new_item' => '添加新分类',
        'menu_name' => '知识分类',
    );
    
    $cat_args = array(
        'labels' => $cat_labels,
        'hierarchical' => true,
        'rewrite' => array('slug' => 'kb-category'),
        'show_in_rest' => true,
    );
    
    register_taxonomy('zib_kb_category', 'zib_knowledge', $cat_args);
}
add_action('init', 'zib_kb_register_post_type');

/**
 * 获取热门知识文章
 */
function zib_kb_get_popular($limit = 10) {
    $args = array(
        'post_type' => 'zib_knowledge',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );
    
    $query = new WP_Query($args);
    $posts = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $posts[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'excerpt' => get_the_excerpt(),
                'permalink' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
            );
        }
        wp_reset_postdata();
    }
    
    return $posts;
}

/**
 * 获取知识分类列表
 */
function zib_kb_get_categories() {
    $categories = get_terms(array(
        'taxonomy' => 'zib_kb_category',
        'hide_empty' => true,
    ));
    
    $result = array();
    if (!is_wp_error($categories) && !empty($categories)) {
        foreach ($categories as $cat) {
            $result[] = array(
                'id' => $cat->term_id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'count' => $cat->count,
                'link' => get_term_link($cat),
            );
        }
    }
    
    return $result;
}
