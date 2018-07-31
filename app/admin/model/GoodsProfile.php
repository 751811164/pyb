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
 * 模型
 */
class GoodsProfile extends AdminBase
{
    



    /**
     * 一对多条码
     * @return \think\model\relation\HasMany
     */
    public function goodsBarcodes()
    {
        return $this->hasMany('GoodsBarcode','profile_id');
    }

    /**
     * 一对多商品组合数量
     * @return \think\model\relation\HasMany
     */
    public function goodsGroups()
    {
        return $this->hasMany('goodsGroup','profile_id');
    }


    /**
     * 一对多价格
     * @return \think\model\relation\HasMany
     */
    public function goodsPrices()
    {
        return $this->hasMany('goodsPrice','profile_id');
    }
}
