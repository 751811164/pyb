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

namespace app\admin\model;

/**
 * 店铺权限模型
 */
class ShopPermission extends AdminBase
{

    protected $type = [

//        'prices_view' => 'array',
//        'goods_alter_price' => 'array',
//        'goods_discount' => 'array',
            'category_ids'=>'json',

    ];
}
