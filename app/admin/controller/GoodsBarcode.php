<?php
/**
 * Author: Jeary
 * Date: 2018/4/29 10:09
 * Desc: created by PhpStorm
 */

namespace app\admin\controller;


class GoodsBarcode extends AdminBase
{


    /**
     * 创建编号
     *
     */
    public function createCode(){

        return $this->logicGoodsBarcode->createCode($this->param);
    }
}