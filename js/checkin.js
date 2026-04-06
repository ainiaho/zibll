/**
 * 签到功能 JavaScript
 */
jQuery(function($) {
    // 签到按钮点击事件
    $('.checkin-btn').on('click', function() {
        var $btn = $(this);
        if ($btn.hasClass('checked') || $btn.prop('disabled')) {
            return false;
        }
        
        var userId = $btn.data('user-id');
        var $tips = $('.checkin-tips');
        
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr10"></i>签到中...');
        
        $.ajax({
            url: zib_ajax.url,
            type: 'POST',
            data: {
                action: 'checkin',
                user_id: userId
            },
            success: function(res) {
                try {
                    var data = typeof res === 'string' ? JSON.parse(res) : res;
                    
                    if (data.error === 0) {
                        // 更新按钮状态
                        $btn.addClass('checked').prop('disabled', true)
                            .html('<i class="fa fa-calendar-check-o mr10"></i>今日已签到');
                        
                        // 更新积分显示
                        $('.balance-num, .checkin-main h3 span').text(data.balance);
                        
                        // 更新统计信息
                        $('.stat-item:first .stat-num').text(data.count);
                        $('.stat-item:last .stat-num').text(new Date().toISOString().split('T')[0]);
                        
                        // 显示成功提示
                        $tips.html('<p class="jb-blue">' + data.msg + '</p>');
                        
                        // 弹出提示
                        if (typeof zib_msg !== 'undefined') {
                            zib_msg.success(data.msg);
                        } else {
                            alert(data.msg);
                        }
                    } else {
                        $btn.prop('disabled', false).html('<i class="fa fa-calendar-check-o mr10"></i>立即签到');
                        $tips.html('<p class="c-red">' + data.msg + '</p>');
                        
                        if (typeof zib_msg !== 'undefined') {
                            zib_msg.error(data.msg);
                        } else {
                            alert(data.msg);
                        }
                    }
                } catch (e) {
                    console.error(e);
                    $btn.prop('disabled', false).html('<i class="fa fa-calendar-check-o mr10"></i>立即签到');
                    
                    if (typeof zib_msg !== 'undefined') {
                        zib_msg.error('签到失败，请稍后再试');
                    } else {
                        alert('签到失败，请稍后再试');
                    }
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i class="fa fa-calendar-check-o mr10"></i>立即签到');
                
                if (typeof zib_msg !== 'undefined') {
                    zib_msg.error('网络错误，请稍后再试');
                } else {
                    alert('网络错误，请稍后再试');
                }
            }
        });
    });
    
    // 充值套餐选择
    $('.package-item').on('click', function() {
        var amount = $(this).data('amount');
        var price = $(this).data('price');
        
        $('#custom-amount').val(amount);
        
        // 移除其他选中状态
        $('.package-item').removeClass('active');
        $(this).addClass('active');
    });
    
    // 自定义充值按钮
    $('.recharge-custom-btn').on('click', function() {
        var amount = $('#custom-amount').val();
        
        if (!amount || amount < 1) {
            if (typeof zib_msg !== 'undefined') {
                zib_msg.error('请输入有效的充值金额');
            } else {
                alert('请输入有效的充值金额');
            }
            return false;
        }
        
        // 计算价格（汇率：1 元=10 积分）
        var price = (amount / 10).toFixed(2);
        
        // 跳转到支付页面或打开支付弹窗
        var rechargeUrl = '/pay/recharge?amount=' + amount + '&price=' + price;
        
        if (typeof zib_pay !== 'undefined') {
            // 如果有支付函数则调用
            zib_pay(rechargeUrl);
        } else {
            window.location.href = rechargeUrl;
        }
    });
});
