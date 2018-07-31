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
class SettingPerformance extends AdminBase
{

    // 验证规则
    protected $rule = [
        'group_id' => 'require',
        'start_time' => 'require',
        'end_time' => 'require',
        'type' => 'require',
        'target' => 'require',
        'brokerage' => 'require',
        'over' => 'require',


    ];

    // 验证提示
    protected $message = [
        'group_id' => '请先选择岗位后再添加',
        'start_time' => '请填写有效起始日期',
        'end_time' => '请填写有效截止日期',
        'type' => '请选择提成类型',
        'target' => '销售目标不能为空',
        'brokerage' => '提成不能为空',
        'over' => '超额提成不能为空',

    ];

    // 应用场景
    protected $scene = [
        'edit' => ['group_id', 'start_time', 'end_time','type','target','brokerage','over'],
    ];

}
