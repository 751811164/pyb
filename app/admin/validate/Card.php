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
class Card extends AdminBase
{

    // 验证规则
    protected $rule = [
        'name' => 'require',
        'type_id'=>'require',

    ];

    // 验证提示
    protected $message = [
        'name' => '类型名不能为空',
        'type_id' => '请选择卡类型',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['name','type_id']
    ];
}
