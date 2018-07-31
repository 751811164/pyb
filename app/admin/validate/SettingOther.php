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
class SettingOther extends AdminBase
{

    // 验证规则
    protected $rule = [
        'admin_id' => 'require',
        'start_time' => 'require',
        'end_time' => 'require',
        'award_ids' => 'require',

        #region rules
        'award_id' => 'require',
        'award' => 'require',

        #endregion rules

    ];

    // 验证提示
    protected $message = [
        'admin_id' => '请选择员工',
        'start_time' => '请填写有效起始日期',
        'end_time' => '请填写有效截止日期',
        'award_ids' => '请选择奖励类型',

        'award_id' => '请选择奖励类型',
        'award' => '奖励金额不能为空',

    ];

    // 应用场景
    protected $scene = [
        'edit' => ['admin_id', 'start_time', 'end_time','award_ids','award_id','award'],
    ];
}
