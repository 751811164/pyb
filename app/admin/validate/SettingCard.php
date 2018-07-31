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
class SettingCard extends AdminBase
{

    // 验证规则
    protected $rule = [
        'group_id' => 'require',
        'start_time' => 'require',
        'end_time' => 'require',
        'type' => 'require',
        'card_ids' => 'require',

        #region rules
//        'id' => 'require',
        'award' => 'require',
        'amount' => 'require',

        #endregion rules

    ];

    // 验证提示
    protected $message = [
        'group_id' => '请先选择岗位后再添加',
        'start_time' => '请填写有效起始日期',
        'end_time' => '请填写有效截止日期',
        'type' => '请选择提成类型',
        'card_ids' => '请选择会员卡',

//        'id' => '请选择卡',
        'award' => '提成奖励不能为空',
        'amount' => '充值金额不能为空',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['group_id', 'start_time', 'end_time','type','card_ids','award'],
        'storedEdit' => ['group_id', 'start_time', 'end_time','type','card_ids','award','amount'],//储值卡提交验证
    ];
}
