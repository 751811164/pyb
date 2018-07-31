<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\validate;

/**
 * 验证器
 */
class SettingCoupon extends AdminBase
{

    // 验证规则
    protected $rule = [
        'group_id' => 'require',
        'start_time' => 'require',
        'end_time' => 'require',
        'coupon_ids' => 'require',

        #region rules
        'coupon_id' => 'require',
        'award' => 'require',

        #endregion rules

    ];

    // 验证提示
    protected $message = [
        'group_id' => '请先选择岗位后再添加',
        'start_time' => '请填写有效起始日期',
        'end_time' => '请填写有效截止日期',
        'coupon_ids' => '请选择礼券',

        'coupon_id' => '请选择礼券',
        'award' => '提成奖励不能为空',

    ];

    // 应用场景
    protected $scene = [
        'edit' => ['group_id', 'start_time', 'end_time','coupon_ids','coupon_id','award'],
    ];
}
