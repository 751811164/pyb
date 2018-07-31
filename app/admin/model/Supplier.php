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
class Supplier extends AdminBase
{

    public function getSettlementTextAttr()
    {
        $status = [0 => '先款后付',1=>'货到付款',2 => '账期(月结)',3=>'账期(季度结算)',];

        return $status[$this->data["settlement"]];
    }

    /**
     * 身份证文本
     */
    public function getIdentityTextAttr(){
        $text="已上传";
        if (empty($this->data["identity_front_id"])&&empty($this->data["identity_behind_id"])&&empty($this->data["identity_hold_id"]))
        {
            $text="";
        }

        return $text;
    }

    /**
     * 营业执照文本
     */
    public function getBusinessLicenseTextAttr(){
        return empty($this->data["business_license_id"])?"":"已上传";
    }

    /**
     * 商品许可证文本
     */
    public function getGoodsLicenseTextAttr(){

        return empty($this->data["goods_license_id"])?"":"已上传";
    }


}
