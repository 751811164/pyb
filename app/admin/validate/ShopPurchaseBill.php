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
 * 证器
 */
class ShopPurchaseBill extends AdminBase
{

    // 验证规则
    protected $rule = [
        'shop_id' => 'require',
    ];

    // 验证提示
    protected $message = [
        'shop.require' => '请选择制单机构',
    ];

    // 应用场景
    protected $scene = [
        'edit' => [ 'shop_id']
    ];
}
