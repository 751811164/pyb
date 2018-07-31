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
class SettingSalary extends AdminBase
{

    // 验证规则
    protected $rule = [
        'group_id' => 'require',
        'trial_salary' => 'require',
        'trial_period' => 'require',
        'regular_salary' => 'require',
        'increase_period' => 'require',
        'increase_amount' => 'require',
        'increase_times' => 'require',

    ];

    // 验证提示
    protected $message = [
        'group_id' => '请先选择岗位后再添加',
        'trial_salary' => '试用底薪不能为空',
        'trial_period' => '试用周期不能为空',
        'regular_salary' => '转正底薪不能为空',
        'increase_period' => '加薪周期不能为空',
        'increase_amount' => '加薪金额不能为空',
        'increase_times' => '加薪次数不能为空',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['group_id', 'trial_salary', 'trial_period','regular_salary','increase_period','increase_amount','increase_times'],
    ];
}
