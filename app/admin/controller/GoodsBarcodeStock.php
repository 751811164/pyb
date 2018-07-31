<?php
/**
 * Author: Jeary
 * Date: 2018/4/29 10:09
 * Desc: created by PhpStorm
 */

namespace app\admin\controller;


class GoodsBarcodeStock extends AdminBase
{

    public function stockList()
    {
        $searchTypeList = ['shop' => '机构汇总', 'category' => '类别汇总', 'goods' => '商品汇总', 'supplier' => '供应商汇总','storage'=>'条码汇总',];

        $this->assign("searchTypeList", $searchTypeList);
        $this->assign('typeList', $typeList = $this->logicShopType->getTypeList([DATA_STATUS_NAME => DATA_NORMAL], 'id,name', '', false));
        $this->assign('shopList', $shopList = $this->logicShop->getSimpleShopList([DATA_STATUS_NAME => DATA_NORMAL], 'id,name,type_id', '', false));
        $this->assign("supplierList", $supplierList = $this->logicSupplier->getSupplierList([DATA_STATUS_NAME => DATA_NORMAL], true, '', false));
        //分类
        $where_category["level"]          = config('logic_config.category_level');
        $where_category[DATA_STATUS_NAME] = DATA_NORMAL;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));


        if (empty($this->param['search_type'])||!in_array($this->param['search_type'],array_keys($searchTypeList)))
        {
            $searchType='shop';
        }
        else
        {
            $searchType = $this->param['search_type'];
        }

        $this->assign('searchType',$searchType);
        $this->assign('filePath','goods_barcode_stock/'.$searchType.'_stock');

        //非总部看自己门店库粗
        $shop_permission = session("auth_shop_permission");
        if (empty($this->param['shop_id'])&&!IS_ROOT&&$shop_permission['shop_id']!=SHOP_ID )
        {
           $this->param['shop_id'] = $shop_permission['shop_id'];
        }

        $where = $this->logicGoodsBarcodeStock->getWhere($this->param);
        $action= 'getStockBy'.ucfirst($searchType);
        $this->$action($where);
//echo model("goods")->getLastSql();
        return $this->fetch('stock_list');
    }

    /**
     * 按仓库条码库存查询
     */
    protected function getStockByStorage($where=[])
    {
        $field = 'gf.name goods_name,s.name shop_name,gf.unit,gf.specification,stock_actual,gp.cost_price,gp.retail_price,gbs.id,gbs.barcode,sum(st.storage_num-take_num) storage_num';
        $this->assign('list', $list = $this->logicGoodsBarcodeStock->getStockList($where, $field));
    }

    /**
     * 按类别查询
     *
     */
    protected function getStockByCategory($where=[])
    {
        $field = 'c.name category_name,sum(stock_actual) stock_actual,sum(gp.cost_price*stock_actual) cost_price,sum(gp.retail_price*stock_actual) retail_price';
        $join = [
            [SYS_DB_PREFIX.'goods g', 'g.id = gbs.goods_id ', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0', 'LEFT'],
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id=gf.id and gp.shop_id=gbs.shop_id ', 'LEFT'],
            [SYS_DB_PREFIX.'category c', 'c.id=g.category_id', 'LEFT'],
        ];
        $this->assign('list', $list = $this->logicGoodsBarcodeStock->getStockList($where, $field,'',0,$join,'g.category_id'));
    }

    /**
     * 按商品查询
     */
    protected function getStockByGoods($where=[])
    {
        $field = 'gf.name goods_name,gf.unit,gf.specification,c.name category_name,sum(stock_actual) stock_actual,sum(gp.cost_price*stock_actual) cost_price,sum(gp.retail_price*stock_actual) retail_price';
        $join = [
            [SYS_DB_PREFIX.'goods g', 'g.id = gbs.goods_id ', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0', 'LEFT'],
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id=gf.id and gp.shop_id=gbs.shop_id ', 'LEFT'],
            [SYS_DB_PREFIX.'category c', 'c.id=g.category_id', 'LEFT'],
        ];
        $this->assign('list', $list = $this->logicGoodsBarcodeStock->getStockList($where, $field,'',0,$join,'gbs.goods_id'));
//        echo model('goods')->getLastSql();
    }


    /**
     * 按门店查询
     */
    protected function getStockByShop($where=[])
    {
        $field = 's.name shop_name,s.number,sum(stock_actual) stock_actual,sum(gp.cost_price*stock_actual) cost_price,sum(gp.retail_price*stock_actual) retail_price';

        $this->assign('list', $list = $this->logicGoodsBarcodeStock->getStockList($where, $field,'',0,'','gbs.shop_id'));
    }


    /**
     * 按供应商查询
     */
    protected function getStockBySupplier($where=[])
    {
        $field = 's.name supplier_name,s.number,sum(stock_actual) stock_actual,sum(gp.cost_price*stock_actual) cost_price,sum(gp.retail_price*stock_actual) retail_price';
        $join = [
            [SYS_DB_PREFIX.'supplier s', 's.id = gbs.supplier_id ', 'LEFT'],
            [SYS_DB_PREFIX.'goods g', 'g.id = gbs.goods_id ', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0', 'LEFT'],
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id=gf.id and gp.shop_id=gbs.shop_id ', 'LEFT'],
        ];
        $this->assign('list', $list = $this->logicGoodsBarcodeStock->getStockList($where, $field,'',0,$join,'gbs.supplier_id'));
    }

}