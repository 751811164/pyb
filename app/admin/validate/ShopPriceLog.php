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
class ShopPriceLog extends AdminBase
{

    // 验证规则
    protected $rule = [
        'shop_ids' => 'require',
    ];

    // 验证提示
    protected $message = [
        'shop_ids.require' => '请选择调价机构',
    ];

    // 应用场景
    protected $scene = [
        'edit' => [ 'shop_ids']
    ];
}
