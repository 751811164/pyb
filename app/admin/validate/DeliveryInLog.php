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
class DeliveryInLog extends AdminBase
{

//    // 验证规则
    protected $rule = [
        'barcode' => 'require',

    ];

    // 验证提示
    protected $message = [
        'barcode' => '请选择商品',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['barcode']
    ];
}
