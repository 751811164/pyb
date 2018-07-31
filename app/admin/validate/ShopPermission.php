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
 * 店铺验证器
 */
class ShopPermission extends AdminBase
{


    // 验证规则
    protected $rule = [

//        'prices_view' => 'require',
//        'goods_alter_price' => 'require',
//        'goods_discount' => 'require',
        //'goods_license_id' => 'gt:0',


        'shop_id' =>'require',
        'refer_id' =>'require'
    ];

    // 验证提示
    protected $message = [
        'id' => '请选择机构',
        'refer_id' => '参考机构不能为空',
        'prices_view.require' => '请选择价格查看权限',
        'goods_alter_price.require' => '请选择改价规则',
        'goods_discount.require' => '请选择打折规则',

        //'goods_license_id.gt' => '食品流通许可证不能为空',
    ];

    // 应用场景
    protected $scene = [
        'add' => ['shop_id'],
        'fastSet'=>['id','refer_id'],
    ];
}
