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
 * 班次验证器
 */
class WorkdayArrange extends AdminBase
{


    // 验证规则
    protected $rule = [
        'admin_id'=>'>:0',
        'a_on'=>'require',
        'a_off'=>'require',
//        'b_on'=>'require',
//        'b_off'=>'require',
//        'c_on'=>'require',
//        'c_off'=>'require',
        'a_late'=>'require',
//        'a_leave'=>'number',
//        'b_late'=>'number',
//        'b_leave'=>'number',
//        'c_late'=>'number',
//        'c_leave'=>'number',

    ];

    // 验证提示
    protected $message = [
        'admin_id'=>'请选择员工',
        'a_on'=>'A组上班设置不能为空',
        'a_off'=>'A组下班设置不能为空',
        'b_on'=>'B组上班设置不能为空',
        'b_off'=>'B组下班设置不能为空',
        'c_on'=>'C组上班设置不能为空',
        'c_off'=>'C组下班设置不能为空',
        'a_late'=>'请填写A组合适迟到分钟数',
        'a_leave'=>'请填写A组合适早退分钟数',
        'b_late'=>'请填写B组合适迟到分钟数',
        'b_leave'=>'请填写B组合适早退分钟数',
        'c_late'=>'请填写C组合适迟到分钟数',
        'c_leave'=>'请填写C组合适早退分钟数',


    ];

    // 应用场景
    protected $scene = [

    ];
}
