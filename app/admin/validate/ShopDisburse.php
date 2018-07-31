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
class ShopDisburse extends AdminBase
{

    // 验证规则
    protected $rule = [
        'shop_id' => 'require|>:0',
        'disburse_type_id' => 'require|>:0',
        'amount' => 'require|>:0',
        'describe'=>'require',

    ];

    // 验证提示
    protected $message = [
        'shop_id' => '必须选择店铺',
        'disburse_type_id' => '必须选择支出类型',
        'amount' => '支出金额必须大于0',
        'describe.require'=>'请填写支出描述信息',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['shop_id', 'disburse_type_id', 'amount','describe']
    ];
}
