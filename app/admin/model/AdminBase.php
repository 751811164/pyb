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

use app\common\model\ModelBase;

/**
 * Admin基础模型
 */
class AdminBase extends ModelBase
{
    /**
     * 进货价
     */
    protected function getCostPriceAttr()
    {
        $adminInfo = session("admin_info");
        if (ADMIN_ID != IS_ROOT && empty($adminInfo["price"]) && empty($adminInfo["price"]["cost_price"]))
        {
            $this->data["cost_price"] = config("logic_config.price_hidden_str");
        }

        return $this->data["cost_price"];
    }

    /**
     * 零售价
     */
    protected function getRetailPriceAttr()
    {
        $adminInfo = session("admin_info");
        if (ADMIN_ID != IS_ROOT && empty($adminInfo["price"]) && empty($adminInfo["price"]["retail_price"]))
        {
            $this->data["retail_price"] = config("logic_config.price_hidden_str");
        }

        return $this->data["retail_price"];

    }

//    /**
//     * 毛利率
//     */
//    protected function getGrossPriceAttr()
//    {
//        return config("logic_config.price_hidden_str");
//    }

    /**
     * 批发价
     */
    protected function getWholesalePriceAttr()
    {
        $adminInfo = session("admin_info");
        if (ADMIN_ID != IS_ROOT && empty($adminInfo["price"]) && empty($adminInfo["price"]["wholesale_price"]))
        {
            $this->data["wholesale_price"] = config("logic_config.price_hidden_str");
        }

        return $this->data["wholesale_price"];

    }

    /**
     * 配送价
     */
    protected function getDeliveryPriceAttr()
    {
        $adminInfo = session("admin_info");
        if (ADMIN_ID != IS_ROOT && empty($adminInfo["price"]) && empty($adminInfo["price"]["delivery_price"]))
        {
            $this->data["delivery_price"] = config("logic_config.price_hidden_str");
        }
        return $this->data["delivery_price"];

    }

    /**
     * 会员价
     */
    protected function getMemberPriceAttr()
    {

        $adminInfo = session("admin_info");

        if (ADMIN_ID != IS_ROOT && empty($adminInfo["price"]) && empty($adminInfo["price"]["member_price"]))
        {
            $memberPrice =json_decode($this->data["member_price"],1);
            foreach ($memberPrice as $key => $item)
            {
                $memberPrice[$key]["price"] = config("logic_config.price_hidden_str");
            }
            $this->data["member_price"] = json_encode($memberPrice);
        }

        return $this->data["member_price"];

    }


}
