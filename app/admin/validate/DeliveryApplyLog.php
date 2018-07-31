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
class DeliveryApplyLog extends AdminBase
{

    // 验证规则
    protected $rule = [
        'valid_time' => 'require',
        'barcode' => 'require',

    ];

    // 验证提示
    protected $message = [
        'valid_time' => '有效期不能为空',
        'barcode' => '请选择商品',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['valid_time','barcode']
    ];
}
