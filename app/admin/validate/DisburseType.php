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
 * POS机验证器
 */
class DisburseType extends AdminBase
{

    // 验证规则
    protected $rule = [
        'name' => 'require',

    ];

    // 验证提示
    protected $message = [
        'name' => '类型名不能为空',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['name']
    ];
}
