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
 * 日期验证器
 */
class ArrangeDate extends AdminBase
{


    // 验证规则
    protected $rule = [
        'admin_id'=>'>:0',
        'year'=>'number',
        'month'=>'number',
        'a_days'=>'require',
        'b_days'=>'require',
        'c_days'=>'require',

    ];

    // 验证提示
    protected $message = [
        'admin_id'=>'请选择员工',
        'year'=>'请选择正确年份',
        'month'=>'请选择正确月份',
        'a_days'=>'请选择A组日期',
        'b_days'=>'请选择B组日期',
        'c_days'=>'请选择C组日期',
    ];

    // 应用场景
    protected $scene = [
        'fastSet'=>['id','refer_id'],
    ];
}
