<?php
get_header();
// 获取搜索关键词和分类信息
global $wp_query;
$s = isset($wp_query->query_vars['s']) ? $wp_query->query_vars['s'] : '';
$cat = isset($wp_query->query_vars['cat']) ? $wp_query->query_vars['cat'] : '';
?>
<?php if (function_exists('dynamic_sidebar')) {
echo '<div class="container fluid-widget">';
dynamic_sidebar('all_top_fluid');
dynamic_sidebar('search_top_fluid');
echo '</div>';
}
?>
<main class="container search-page-wrap">
<div class="content-wrap">
<div class="content-layout">
<?php if (function_exists('dynamic_sidebar')) {
dynamic_sidebar('search_top_content');
}
?>
<div class="ajaxpager">
<!-- 搜索主卡片 -->
<div class="search-card">
<div class="search-header-section">
<div class="title-h-left"><b>搜索精彩内容</b></div>
</div>
<div class="search-input-wrapper">
<?php zib_get_search(); ?>
</div>
</div>

<?php
// AI 智能总结模块
if (function_exists('zib_ai_frontend_chatbox') && _pz('ai_enabled', false) && $s) {
echo '<div class="search-card search-ai-section" style="margin-top: 0;">';
echo '<div class="title-h-left"><i class="fa fa-robot"></i> AI 智能总结</div>';
echo '<div id="ai-search-summary" class="padding10" data-keyword="' . esc_attr($s) . '">';
echo '<div class="text-center muted-color"><i class="fa fa-spinner fa-spin"></i> 正在生成针对 "' . esc_html($s) . '" 的智能总结...</div>';
echo '</div>';
echo '</div>';

// 确保加载聊天框组件（如果尚未加载）
zib_ai_frontend_chatbox();
}

if (!have_posts()) {
echo '<div class="search-card search-no-results">';
echo '<img class="search-null-img" src="' . get_stylesheet_directory_uri() . '/img/search-null.png">';
echo '<p class="muted-color separator">未找到相关结果</p>';
echo '<div class="search-suggestions">';
echo '<p>建议您：</p>';
echo '<ul style="text-align:left;display:inline-block;color:var(--muted-2-color);font-size:14px;line-height:2;">';
echo '<li>检查输入是否正确</li>';
echo '<li>尝试更简短的关键词</li>';
echo '<li>更换其他关键词试试</li>';
echo '</ul>';
echo '</div>';
echo '</div>';
} else {
$tt = '全部内容';
if ($s) {
/**保存搜索关键词 */
zib_update_search_keywords($s);
$tt = '包含"<b class="focus-color search-keyword">' . $s . '</b>"的全部内容';
}
if ($cat) {
$cat_a = get_category($cat);
if ($cat_a) {
$tt = '在分类<b class="focus-color">"' . $cat_a->cat_name . '"</b>中' . $tt;
}
}
echo '<div class="search-card search-results-section">';
echo '<div class="box-body nobottom"><div class="title-h-left">' . $tt . '</div></div>';
$args = array(
'no_margin' => true,
'is_card' => false,
);
zib_posts_list($args);
zib_paging();
echo '</div>';
}
?>
</div>
<?php if (function_exists('dynamic_sidebar')) {
dynamic_sidebar('search_bottom_content');
}
?>
</div>
<?php get_sidebar(); ?>
</main>
<?php if (function_exists('dynamic_sidebar')) {
echo '<div class="container fluid-widget">';
dynamic_sidebar('search_bottom_fluid');
dynamic_sidebar('all_bottom_fluid');
echo '</div>';
}
?>
<?php get_footer();
