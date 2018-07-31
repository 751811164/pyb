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
 * 商品模型
 */
class Goods extends AdminBase
{

    /**
     * 关联
     * @return \think\model\relation\HasMany
     */
    public function goodsProfiles()
    {
        return $this->hasMany('goodsProfile','goods_id');
    }

    /**
     * 关联
     * @return \think\model\relation\HasMany
     */
    public function goodsGroups()
    {
        return $this->hasManyThrough('GoodsGroup','GoodsProfile','goods_id','profile_id','id');
    }

    /**
     * 组合商品状态获取器
     */
    public function getComboTextAttr()
    {
        $status = [ DATA_NO => "否", DATA_YES => "<span class='badge bg-blue'>是</span>"];
        return $status[$this->data["combo"]];
    }

    /**
     * 商品状态获取器
     * @return mixed
     */
    public function getStatusTextAttr()
    {
        $status = [DATA_DELETE => '回收站', DATA_DISABLE => "<span class='badge bg-red'>停售</span>", DATA_NORMAL => "<span class='badge bg-green'>在售</span>"];

        return $status[$this->data[DATA_STATUS_NAME]];

    }

    /**
     * 打折状态
     */
    public function getDiscountTextAttr()
    {
        $status = [ DATA_NO => "否", DATA_NORMAL => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['is_discount']];
    }

    /**
     * 议价状态
     */
    public function getBargainTextAttr()
    {
        $status = [ DATA_NO => "否", DATA_NORMAL => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['is_bargain']];

    }
    /**
     * 赠送状态
     */
    public function getPresentationTextAttr()
    {
        $status = [ DATA_NO => "否", DATA_NORMAL => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['is_presentation']];

    }
    /**
     * 参与储值状态
     */
    public function getStoredTextAttr()
    {
        $status = [ DATA_NO => "否", DATA_NORMAL => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['is_stored']];

    }
    /**
     * 参与积分状态
     */
    public function getPointTextAttr()
    {
        $status = [ DATA_NO => "否", DATA_NORMAL => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['is_point']];

    }
    /**
     * 打折状态
     */
    public function getControlStockTextAttr()
    {
        $status = [ DATA_NO => "否", DATA_NORMAL => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['is_control_stock']];

    }




}
