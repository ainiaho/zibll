<?php
/**
 * 子比主题 - 签到和会员充值功能
 * Zibll Theme - Check-in and Recharge System
 */

/**挂钩到用户中心 */
if (_pz('checkin_enabled', true)) {
    add_action('author_info_tab', 'zib_checkin_user_info_tab', 9);
    add_action('author_info_tab_con', 'zib_checkin_user_info_tab_con', 9);
}

/**
 * 签到选项卡
 */
function zib_checkin_user_info_tab($user_id) {
    echo '<li><a class="muted-2-color but hollow" data-toggle="tab" href="#author-tab-checkin"><i class="fa fa-calendar-check-o hide-sm fa-fw" aria-hidden="true"></i>每日签到</a></li>';
}

/**
 * 签到选项卡内容
 */
function zib_checkin_user_info_tab_con($user_id) {
    $status = zib_get_checkin_status($user_id);
    $balance = (int) get_user_meta($user_id, 'user_balance', true);
    $min_points = _pz('checkin_min_points', 10);
    $max_points = _pz('checkin_max_points', 50);
    
    $checked_class = $status['checked_today'] ? ' checked' : '';
    $btn_text = $status['checked_today'] ? '今日已签到' : '立即签到';
    $btn_disabled = $status['checked_today'] ? ' disabled' : '';
    
    ?>
    <div class="tab-pane fade" id="author-tab-checkin">
        <div class="theme-box checkin-box">
            <div class="box-body">
                <div class="title-h-left"><b>每日签到</b></div>
            </div>
            <div class="box-body notop nobottom">
                <div class="checkin-main text-center" style="padding: 40px 20px;">
                    <div class="checkin-avatar" style="margin-bottom: 20px;">
                        <?php echo zib_get_data_avatar($user_id, '80'); ?>
                    </div>
                    <h3 style="margin: 15px 0;">当前积分：<span class="jb-blue" style="font-size: 24px;"><?php echo $balance; ?></span></h3>
                    <p class="muted-2-color" style="margin-bottom: 30px;">
                        每日签到可获得 <?php echo $min_points; ?>-<?php echo $max_points; ?> 积分，VIP 用户享有额外加成
                    </p>
                    
                    <div class="checkin-stats row" style="margin-bottom: 30px;">
                        <div class="col-xs-6">
                            <div class="stat-item" style="background: var(--muted-bg-color); padding: 15px; border-radius: 8px;">
                                <div class="stat-num jb-blue" style="font-size: 20px; font-weight: bold;"><?php echo $status['checkin_count']; ?></div>
                                <div class="stat-label muted-2-color em09">累计签到</div>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="stat-item" style="background: var(--muted-bg-color); padding: 15px; border-radius: 8px;">
                                <div class="stat-num jb-green" style="font-size: 20px; font-weight: bold;"><?php echo $status['last_checkin'] ? $status['last_checkin'] : '从未'; ?></div>
                                <div class="stat-label muted-2-color em09">上次签到</div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="but jb-blue padding-lg checkin-btn<?php echo $checked_class; ?>"<?php echo $btn_disabled; ?> data-user-id="<?php echo $user_id; ?>">
                        <i class="fa fa-calendar-check-o mr10"></i><?php echo $btn_text; ?>
                    </button>
                    
                    <div class="checkin-tips muted-2-color em09" style="margin-top: 20px;">
                        <p>温馨提示：每天都可以来签到领取积分哦~</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="theme-box checkin-rules" style="margin-top: 20px;">
            <div class="box-body">
                <div class="title-h-left"><b>签到规则</b></div>
            </div>
            <div class="box-body notop nobottom muted-2-color em09" style="padding: 15px;">
                <ul style="line-height: 1.8;">
                    <li>1. 每天可签到一次，获得随机积分奖励</li>
                    <li>2. VIP 用户签到可获得额外积分加成</li>
                    <li>3. 积分可用于付费阅读、购买资源等</li>
                    <li>4. 请遵守社区规则，禁止刷积分行为</li>
                </ul>
            </div>
        </div>
    </div>
    <?php
}

/**
 * 会员充值选项卡
 */
if (_pz('recharge_enabled', true)) {
    add_action('author_info_tab', 'zib_recharge_user_info_tab', 10);
    add_action('author_info_tab_con', 'zib_recharge_user_info_tab_con', 10);
}

function zib_recharge_user_info_tab($user_id) {
    echo '<li><a class="muted-2-color but hollow" data-toggle="tab" href="#author-tab-recharge"><i class="fa fa-credit-card hide-sm fa-fw" aria-hidden="true"></i>会员充值</a></li>';
}

function zib_recharge_user_info_tab_con($user_id) {
    $balance = (int) get_user_meta($user_id, 'user_balance', true);
    $pay_mark = _pz('pay_mark', '￥');
    
    // 获取充值套餐配置
    $packages = _pz('recharge_packages', array(
        array('amount' => 100, 'price' => 10, 'label' => '100 积分'),
        array('amount' => 500, 'price' => 45, 'label' => '500 积分'),
        array('amount' => 1000, 'price' => 80, 'label' => '1000 积分'),
        array('amount' => 5000, 'price' => 350, 'label' => '5000 积分'),
    ));
    
    ?>
    <div class="tab-pane fade" id="author-tab-recharge">
        <div class="theme-box recharge-box">
            <div class="box-body">
                <div class="title-h-left"><b>账户充值</b></div>
            </div>
            <div class="box-body notop nobottom">
                <div class="recharge-balance text-center" style="padding: 30px 20px; background: var(--muted-bg-color); margin: 20px; border-radius: 8px;">
                    <h4 class="muted-2-color" style="margin-bottom: 15px;">当前余额</h4>
                    <div class="balance-num" style="font-size: 36px; font-weight: bold; color: var(--main-color);"><?php echo $balance; ?></div>
                    <div class="balance-unit muted-2-color">积分</div>
                </div>
                
                <div class="recharge-packages" style="padding: 20px;">
                    <h4 style="margin-bottom: 20px;">选择充值套餐</h4>
                    <div class="row">
                        <?php 
                        if (is_array($packages) && !empty($packages)) {
                            foreach ($packages as $index => $pkg) {
                                $amount = isset($pkg['amount']) ? $pkg['amount'] : 0;
                                $price = isset($pkg['price']) ? $pkg['price'] : 0;
                                $label = isset($pkg['label']) ? $pkg['label'] : $amount . '积分';
                                $discount = $amount > 0 && $price > 0 ? round(($amount - $price) / $amount * 100) : 0;
                                
                                ?>
                                <div class="col-xs-6 col-md-3" style="margin-bottom: 15px;">
                                    <div class="package-item theme-box package-<?php echo $index; ?>" style="position: relative; cursor: pointer;" data-amount="<?php echo $amount; ?>" data-price="<?php echo $price; ?>">
                                        <?php if ($discount > 0) { ?>
                                        <span class="package-badge" style="position: absolute; top: 10px; right: 10px; background: #ff473a; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px;">省<?php echo $discount; ?>%</span>
                                        <?php } ?>
                                        <div class="box-body text-center" style="padding: 20px 10px;">
                                            <div class="package-amount" style="font-size: 24px; font-weight: bold; color: var(--main-color); margin-bottom: 10px;"><?php echo $label; ?></div>
                                            <div class="package-price" style="font-size: 18px; color: #ff473a;"><?php echo $pay_mark; ?><?php echo $price; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="col-xs-12"><p class="text-center muted-2-color">暂无充值套餐</p></div>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="recharge-custom" style="padding: 0 20px 20px;">
                    <h4 style="margin-bottom: 15px;">自定义充值金额</h4>
                    <div class="row">
                        <div class="col-xs-8">
                            <input type="number" id="custom-amount" class="form-control" placeholder="输入充值积分数值" min="1" step="1">
                        </div>
                        <div class="col-xs-4">
                            <button type="button" class="but jb-blue btn-block recharge-custom-btn">充 值</button>
                        </div>
                    </div>
                    <p class="muted-2-color em09" style="margin-top: 10px;">汇率：<?php echo $pay_mark; ?>1 = 10 积分</p>
                </div>
            </div>
        </div>
        
        <div class="theme-box recharge-notice" style="margin-top: 20px;">
            <div class="box-body">
                <div class="title-h-left"><b>充值说明</b></div>
            </div>
            <div class="box-body notop nobottom muted-2-color em09" style="padding: 15px;">
                <ul style="line-height: 1.8;">
                    <li>1. 选择充值套餐或输入自定义充值金额</li>
                    <li>2. 点击充值后跳转到支付页面完成支付</li>
                    <li>3. 支付成功后积分自动到账</li>
                    <li>4. 如遇问题请联系客服处理</li>
                </ul>
            </div>
        </div>
    </div>
    <?php
}

/**
 * 获取签到状态
 */
function zib_get_checkin_status($user_id) {
    $today = date('Y-m-d');
    $last_checkin = get_user_meta($user_id, 'last_checkin_date', true);
    $checkin_count = (int) get_user_meta($user_id, 'checkin_count', true);
    
    return array(
        'checked_today' => ($last_checkin == $today),
        'last_checkin' => $last_checkin,
        'checkin_count' => $checkin_count
    );
}

/**
 * 创建余额日志表
 */
function zib_create_balance_log_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zibpay_balance_log';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        amount int(11) NOT NULL,
        type varchar(50) NOT NULL,
        description varchar(255) DEFAULT '',
        create_time datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY user_id (user_id),
        KEY type (type)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'zib_create_balance_log_table');
register_activation_hook(__FILE__, 'zib_create_balance_log_table');
