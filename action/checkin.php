<?php
/**
 * 子比主题 - 签到功能
 * Zibll Theme - Check-in System
 */

if (!$_POST) {
    exit;
}

require dirname(__FILE__) . '/../../../../wp-load.php';

$cuid = get_current_user_id();

if (!is_user_logged_in()) {
    echo json_encode(array('error' => 1, 'msg' => '请先登录'));
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'checkin':
        zib_do_checkin($cuid);
        break;

    default:
        echo json_encode(array('error' => 1, 'msg' => '无效的操作'));
        break;
}

exit;

/**
 * 执行签到
 */
function zib_do_checkin($user_id) {
    $today = date('Y-m-d');
    $last_checkin = get_user_meta($user_id, 'last_checkin_date', true);
    
    if ($last_checkin == $today) {
        echo json_encode(array('error' => 1, 'msg' => '今天已经签到过了，明天再来吧！'));
        return;
    }
    
    // 获取签到奖励配置
    $min_points = _pz('checkin_min_points', 10);
    $max_points = _pz('checkin_max_points', 50);
    $vip_multiplier = _pz('checkin_vip_multiplier', 1);
    
    // 随机奖励积分
    $points = rand($min_points, $max_points);
    
    // VIP 用户加成
    if ($vip_multiplier > 1) {
        $vip_level = zib_get_user_vip_level($user_id);
        if ($vip_level > 0) {
            $points = floor($points * (1 + ($vip_level * 0.1)));
        }
    }
    
    // 更新用户积分
    $current_balance = (int) get_user_meta($user_id, 'user_balance', true);
    $new_balance = $current_balance + $points;
    update_user_meta($user_id, 'user_balance', $new_balance);
    
    // 更新签到日期
    update_user_meta($user_id, 'last_checkin_date', $today);
    
    // 记录签到日志
    $checkin_count = (int) get_user_meta($user_id, 'checkin_count', true);
    $checkin_count++;
    update_user_meta($user_id, 'checkin_count', $checkin_count);
    
    // 添加到余额变动日志
    zib_add_balance_log($user_id, $points, 'checkin', '每日签到奖励');
    
    echo json_encode(array(
        'error' => 0, 
        'msg' => '签到成功！获得 ' . $points . ' 积分',
        'points' => $points,
        'balance' => $new_balance,
        'count' => $checkin_count
    ));
}

/**
 * 获取用户签到状态
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
 * 添加余额变动日志
 */
function zib_add_balance_log($user_id, $amount, $type, $description) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'zibpay_balance_log';
    
    $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'create_time' => current_time('mysql')
        ),
        array('%d', '%d', '%s', '%s', '%s')
    );
}

/**
 * 获取用户 VIP 等级
 */
function zib_get_user_vip_level($user_id) {
    $vip_expire = get_user_meta($user_id, 'vip_expire', true);
    
    if (!$vip_expire || strtotime($vip_expire) < time()) {
        return 0;
    }
    
    $vip_level = get_user_meta($user_id, 'vip_level', true);
    return $vip_level ? $vip_level : 1;
}
