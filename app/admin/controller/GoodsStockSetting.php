<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Regoodsitory | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\controller;

/**
 * 控制器
 */
class GoodsStockSetting extends AdminBase
{

    /**
     *  列表
     */
    public function stockList()
    {
//        //店铺列表
//        $where_type[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
//        $where_shop[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
//        $typeList                     = collection($this->logicShopType->getTypeList($where_type, 'id,name', "", false, [], ''))->toArray();
//        $shopList                     = collection($this->logicShop->getSimpleShopList($where_shop, 'id,name,type_id', "", false, [], ''))->toArray();
//
//        foreach ($typeList as $key => $type)
//        {
//            $typeList[$key]["children"] = [];
//            foreach ($shopList as $k => $shop)
//            {
//
//                if ($shop['type_id'] == $type['id'])
//                {
//                    $typeList[$key]["children"][] = $shopList[$k];
//                }
//            }
//        }
        $this->assign("typeList", $shopTree=$this->logicShop->getShopTree());

        $where            = $this->logicGoodsStockSetting->getWhere($this->param);
        $where["gs.shop_id"] = empty($where["gs.shop_id"]) ? SHOP_ID: $where["gs.shop_id"];

        $list = $this->logicGoodsStockSetting->getStockList($where, 'g.*,gf.*,gs.*,c.name category_name,gt.name as type_name,s.name supplier_name,IFNULL(sum(gbs.stock_actual),0) stock_actual');
//                echo model("GoodsStockSetting")->getLastSql();
        $this->assign('list', $list);
        return $this->fetch('stock_list');
    }


    /**
     * 选择商品列表
     */
    public function goodsChoose()
    {

        //分类
        $where_category["level"] =config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));


//        $ids = $list = $this->logicGoodsStockSetting->getStockColumn(["shop_id" => $this->param["shop_id"], DATA_STATUS_NAME => DATA_NORMAL], "goods_id");

        $this->view->engine->layout(false);
        $where         = $this->logicGoodsStockSetting->getGoodsChooseWhere($this->param);
//        $where["g.id"] = ["not in", $ids];
        $list          = $this->logicGoods->getGoodsList($where, 'gf.barcode1,g.id goods_id,gf.name,gf.specification,gf.unit,gp.*,c.name category_name,gt.name type_name,s.name supplier_name');
        // echo model("Goods")->getLastSql();
        $this->assign('list', $list);
        return $this->fetch('goods_choose');
    }


    /**
     * 编辑
     */
    public function stockEdit()
    {

        $this->jump($this->logicGoodsStockSetting->stockEdit($this->param));

    }


    /**
     * 删除
     */
    public function stockDel()
    {
        $this->jump($this->logicGoodsStockSetting->stockDel(['id' => ["in", trim($this->param["ids"], ',')]]));
    }

    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('GoodsStockSetting', $this->param));
    }


}
