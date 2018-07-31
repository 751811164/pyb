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
class SalaryStructure extends AdminBase
{

    // 验证规则
    protected $rule = [
        'group_id' => 'require',


    ];

    // 验证提示
    protected $message = [
        'group_id' => '请先选择岗位后再添加',


    ];

    // 应用场景
    protected $scene = [
        'reachEdit' => ['group_id', ],
    ];
}
