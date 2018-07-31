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
class PurchaseBillLog extends AdminBase
{

    // 验证规则
    protected $rule = [
        'shop_id' => 'require',
        'supplier_id' => 'require',
        'goods_id' => 'require|priceEqual',
    ];

    // 验证提示
    protected $message = [
        'shop_id.require' => '请选择机构',
        'supplier_id.require' => '请选择供应商',
        'goods_id.require' => '请选择商品',
        'goods_id.priceEqual'=>'同款商品的进价不一致'

    ];

    // 应用场景
    protected $scene = [
        'edit' => [ 'shop_id','supplier_id','goods_id']
    ];


    /**
     * 验证同款商品价格是否一致
     */
    public function priceEqual($goods_ids,$rule,$data,$field){

        foreach ($goods_ids as $key =>$id){

            for ( $i = $key+1; $i < count($goods_ids); $i++) {
                if ($id==$goods_ids[$i] && $data['cost_price'][$key]!=$data['cost_price'][$i])
                {
                    return false;
                }
            }
        }
        return true;
    }

}


