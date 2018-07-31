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
 * 商品验证器
 */
class Goods extends AdminBase
{

    // 验证规则
    protected $rule = [

        'single_barcode' => 'unique',
        'single_name' => 'require|checkNameUnique',
        'single_cost_price' => 'require|number',
        'single_retail_price' => 'require|number|checkSingleRetailPrice',
        'single_wholesale_price' => 'require',
        'single_delivery_price' => 'require',
        'single_member_price'=>'checkSingleMemberPrice',

        'pack_barcode' => 'require|unique',
        'pack_name' => 'require|checkNameUnique',
        'pack_num' => 'require',
        'pack_retail_price' => 'require|checkPackRetailPrice',
        'pack_wholesale_price' => 'require',
        'pack_delivery_price' => 'require',
        'pack_member_price'=>'checkPackMemberPrice',
    ];

    // 验证提示
    protected $message = [
        'single_barcode' => '商品条码已存在',


        'single_name.require' => '商品名称不能为空',
        'single_name.checkNameUnique' => '商品名称不能重复',

        'single_cost_price.require' => '商品进货价不能为空且必须为数字',

        'single_retail_price.require' => '商品零售价不能为空',
        'single_retail_price.number' => '商品零售价必须为数字',
        'single_retail_price.checkSingleRetailPrice' => '商品零售价必须大于等于进价',

        'single_wholesale_price' => '商品批发价不能为空且必须为数字',
        'single_delivery_price' => '商品配送价不能为空且必须为数字',
        'single_member_price.checkSingleMemberPrice'=>'商品会员价不能低于进货价或高于零售价',

        'pack_barcode' => '多包装条码已存在',
        'pack_name.require' => '多包装名称不能为空',
        'pack_name.checkNameUnique' => '多包装名称不能重复',
        'pack_num' => '多包装数量不能为空且必须为数字',

        'pack_retail_price.require' => '多包装零售价不能为空',
        'pack_retail_price.checkPackRetailPrice' => '多包装零售价必须大于等于进价',
        'pack_wholesale_price' => '多包装批发价不能为空且必须为数字',
        'pack_delivery_price' => '多包装配送价不能为空且必须为数字',
        'pack_member_price.checkPackMemberPrice'=>'多包装会员价不能低于进货价或高于零售价',


    ];

    // 应用场景
    protected $scene = [
        //普通商品验证
        'add' => [

            'single_barcode',
            'single_name',
            'single_cost_price',
            'single_retail_price',
            'single_wholesale_price',
            'single_delivery_price',
            'single_member_price',

            'pack_barcod',
            'pack_name',
            'pack_num',
            'pack_retail_price',
            'pack_wholesale_price',
            'pack_delivery_price',
            'pack_member_price',
        ],
        'edit' => [
            '',
            'single_barcode',
            'single_name',


            'pack_barcod',
            'pack_name',
            'pack_num',

        ],
    ];


    /**
     * 检测名是否重复
     */
    public function checkNameUnique($value, $rule, $data, $field)
    {
        $res = true;
        $id  = explode("_", $field)[0]."_id";

        $setting = model("GoodsSetting")->where([DATA_STATUS_NAME => DATA_NORMAL])->find();
        if ($setting && $setting["repeat_goods_name"] == 0)
        {
            $rule = "goods_profile,status=1&name={$value},{$data[$id]},id";
            $res  = $this->unique($value, $rule, $data, $field);
        }

        return $res;
    }

    /**
     * 检测单品零售价大于进价(成本价)
     */
    public function checkSingleRetailPrice($value, $rule, $data, $field)
    {

        //sale_egt_cost//大于等于
//        $setting = model("GoodsSetting")->where([DATA_STATUS_NAME => DATA_NORMAL])->find();
//        if ($setting && $setting["sale_egt_cost"] == 0)
//        {
//          return  $value>=$data["single_cost_price"];
//        }
//        return true;
        return  $value>=$data["single_cost_price"];
    }


    /**
     * 检测单品会员价
     * 会员价>进货价  会员价<零售价
     *
     */
    public function checkSingleMemberPrice($value, $rule, $data, $field){
        foreach ($value as $key=>$val){
            if ($val>$data["single_retail_price"]||$val<$data["single_cost_price"])
            {
                return false;
            }
        }
        return true;
    }



    /**
     * 检测多包装零售价
     */
    public function checkPackRetailPrice($value, $rule, $data, $field)
    {
        //sale_egt_cost//大于等于
//        $setting = model("GoodsSetting")->where([DATA_STATUS_NAME => DATA_NORMAL])->find();
//        if ($setting && $setting["sale_egt_cost"] == 0)
//        {
//            return  $value>=$data["single_cost_price"]*$data["pack_num"];
//        }
//        return true;

        return  $value>=$data["single_cost_price"]*$data["pack_num"];
    }

    /**
     * 检测多包装会员价
     * 会员价>进货价  会员价<零售价
     */
    public function checkPackMemberPrice($value, $rule, $data, $field){
        foreach ($value as $key=>$val){
            if ($val>$data["pack_retail_price"]||$val<$data["pack_cost_price"])
            {
                return false;
            }
        }
        return true;
    }



    public function unique($value, $rule, $data, $field)
    {
        //判断条码是否重复
        if (substr_compare($field, 'barcode', -strlen('barcode')) === 0)
        {

            foreach ($value as $key => $val)
            {
                $rule = 'goods_barcode,status=1&barcode='.$val.','.$data['single_barcode_id'][$key].',id';
                $res  = parent::unique($value, $rule, $data, $field);
                if ($res === false)
                {
                    return false;
                }
            }
            return true;
        }
        else
        {
            //其余默认
            return parent::unique($value, $rule, $data, $field);
        }

    }

}

