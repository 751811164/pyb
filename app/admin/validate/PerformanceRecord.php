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
class PerformanceRecord extends AdminBase
{

    // 验证规则
    protected $rule = [
        'start_time' => 'require',
        'end_time' => 'require',
        'admin_id' => '>:0',
        'type_id' => '>:0',
    ];

    // 验证提示
    protected $message = [
        'admin_id' => '请选择员工',
        'type_id' => '请选择类型',
        'start_time' => '请填写有效起始日期',
        'end_time' => '请填写有效截止日期',



    ];

    // 应用场景
    protected $scene = [
        'edit' => ['admin_id','type_id', 'start_time', 'end_time'],

    ];
}
