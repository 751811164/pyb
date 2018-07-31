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
class InventoryLog extends AdminBase
{

    // 验证规则
    protected $rule = [
        'barcode' => 'require',
        'shop_id' => 'require',

    ];

    // 验证提示
    protected $message = [
        'barcode.require' => '条码不能为空',
        'shop_id.require' => '必须选择店铺',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['barcode',  'shop_id']
    ];
}
