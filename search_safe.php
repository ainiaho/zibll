<?php
/**
 * 搜索页面模板 (纯净版 - 用于排查 AI 导致的崩溃)
 * 已暂时移除所有 AI 相关调用，确保核心功能可用
 */
get_header(); 

// 确保全局变量定义，防止未定义警告
global $wp_query, $s, $cat;
$s = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$cat = get_query_var('cat');
?>

<div class="container">
    <div class="content-wrap">
        <div class="content main">
            
            <!-- 搜索标题 -->
            <header class="page-header">
                <h1 class="page-title">
                    <?php printf(__('搜索：%s', 'zibll'), '<span>' . esc_html($s) . '</span>'); ?>
                </h1>
            </header>

            <!-- 搜索结果循环 -->
            <?php if (have_posts()) : ?>
                <div class="posts-list">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('templates/post/list'); ?>
                    <?php endwhile; ?>
                    
                    <!-- 分页 -->
                    <?php zib_paging(); ?>
                </div>
            <?php else : ?>
                <!-- 无结果提示 -->
                <div class="not-found">
                    <p><?php _e('未找到相关内容，请尝试其他关键词。', 'zibll'); ?></p>
                </div>
            <?php endif; ?>

        </div>
        
        <!-- 侧边栏 -->
        <?php get_sidebar(); ?>
    </div>
</div>

<?php 
// 【调试用】AI 功能已暂时禁用，以排除语法错误导致的页面崩溃
// 待 ai-frontend.php 修复后，可恢复下方代码
/*
if (zib_get_option('zib_ai_open', true) && !empty($s) && function_exists('zib_ai_frontend_chatbox')) {
    zib_ai_frontend_chatbox();
}
*/
get_footer(); 
?>
