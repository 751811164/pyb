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
class PurchaseInLog extends AdminBase
{

    // 验证规则
    protected $rule = [
        'shop_id' => 'require',
        'supplier_id' => 'require',
        'goods_id' => 'require|priceEqual',
        'barcode' => 'existBarcode',
        'produce_date' => 'checkDate'
    ];

    // 验证提示
    protected $message = [
        'shop_id.require' => '请选择机构',
        'supplier_id.require' => '请选择供应商',
        'goods_id.require' => '请选择商品',
        'goods_id.priceEqual' => '一品多码商品,进价不一致',
        'barcode.existBarcode' => '条码不存在,请在商品档案添加',
        'produce_date' => '生产日期不正确'

    ];

    // 应用场景
    protected $scene = [
        'edit' => ['shop_id', 'supplier_id', 'goods_id', 'barcode', 'produce_date']
    ];


    /**
     * 验证同款商品价格是否一致
     */
    public function priceEqual($goods_ids, $rule, $data, $field)
    {

        foreach ($goods_ids as $key => $id)
        {

            for ($i = $key + 1; $i < count($goods_ids); $i++)
            {
                if ($id == $goods_ids[$i] && $data['cost_price'][$key] != $data['cost_price'][$i])
                {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 判断条码是否存在
     */
    public function existBarcode($barcodes, $rule, $data, $field)
    {
        $goodsBarcodes = [];
        $barcodeList   = model("goods_barcode")->alias("gb")->field("gb.barcode")->join([[SYS_DB_PREFIX.'goods_profile gf', 'gf.id = gb.profile_id']])->join([
            [
                SYS_DB_PREFIX.'goods g',
                'g.id = gf.goods_id',
            ]
        ])->where("g.status", 1)->where("gb.status", 1)->where("gf.goods_id", 'in', $data['goods_id'])->select();

        foreach ($barcodeList as $key => $barcode)
        {
            array_push($goodsBarcodes, $barcode['barcode']);

        }

        foreach ($barcodes as $key => $value)
        {
            if (!in_array($value, $goodsBarcodes))
            {
                return false;
            }
        }
        return true;
    }

    public function checkDate($dates, $rule, $data, $field)
    {
        foreach ($dates as $date)
        {
            if (empty($date))
            {
                return false;
            }
        }

        return true;
    }

}


