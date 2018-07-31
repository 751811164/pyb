<?php
/**
 * Author: Jeary
 * Date: 2018/4/29 10:09
 * Desc: created by PhpStorm
 */

namespace app\admin\logic;


class Test extends AdminBase
{

    public function test()
    {
      $where["a.id"]=   [['>', 0], ['<>', 10], 'or'];
      $where['a.username']='aaa';
       $admin= $this->logicAdmin->getSimpleAdminList($where);
       //WHERE ( `a`.`id` > 0 or `a`.`id` <> 10 ) AND `a`.`username` = 'aaa' AND `status` <> -1


//        $map['_string'] = 'username="aa" ';
//        $sql            = $this->logicAdmin->where(function($query) {
//                $query->where(['id' => [['>', 0], ['<>', 10], 'or']])->whereOr("username", "aaaa");
//            })->where($map)->buildSql();

       echo model('admin')->getLastSql();

    }

    public function duplicate()
    {
        return $info = $this->modelGoods->with(['goodsProfiles' => ['GoodsPrices', 'GoodsBarcodes', 'GoodsGroups']])->find();
    }

    public function setAll()
    {
        $sql = " INSERT INTO `ob_goods_stock` (
        `shop_id`,
	`goods_id`,
	`stock_actual`
)
VALUES
(20, 112, 13),
	(20, 111, 10) ON DUPLICATE KEY UPDATE `stock_actual` = `stock_actual` + VALUES(stock_actual)";
        $res = $this->modelGoodsStockSetting->execute($sql);
        echo model("GoodsStockSetting")->getLastSql();
        dump($res);
    }
}