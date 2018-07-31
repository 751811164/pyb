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

namespace app\admin\controller;

/**
 * 控制器
 */
class InventoryLog extends AdminBase
{
    /**
     * 列表
     */
    public function logList()
    {
        $info=[DATA_STATUS_NAME=>DATA_DELETE]; $list=[];

        if (!empty($this->param["inventory_id"]))
        {
            //单信息
            $info=$this->logicInventory->getInventoryJoinInfo(["i.id"=>$this->param["inventory_id"]],"i.*,add.username admin_add_name,check.username admin_check_name,sn ");
            $where = $this->logicInventoryLog->getWhere($this->param);
            //列表明细
            $list=$this->logicInventoryLog->getLogList($where,"il.*,gf.barcode1,gf.name,gf.unit,gf.specification,g.size,g.color",'il.id asc',false);

        }
        $this->assign('list', $list);
        $this->assign("info",$info);

        $this->assign('typeList',$typeList= $this->logicShopType->getTypeList([DATA_STATUS_NAME=>DATA_NORMAL], 'id,name', '', false));
        $this->assign('shopList',$shopList= $this->logicShop->getSimpleShopList([DATA_STATUS_NAME=>DATA_NORMAL], 'id,name,type_id', '', false));
        $this->assign("supplierList", $supplierList = $this->logicSupplier->getsupplierList([], true, '', false));
        return $this->fetch('log_list');
    }

    /**
     * 选择店铺价商品
     * 按条码显示 列表出现 一品多码
     */
    public function goodsChoose(){
        $this->view->engine->layout(false);
        //分类
        $where_category["level"] =config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));

        $where = $this->logicInventoryLog->getGoodsChooseWhere($this->param);
        $shop_id= $this->param['shop_id'];

        $join = [
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0'],
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id  ', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile pgf', 'pgf.goods_id = g.id and gf.kind=1', 'LEFT'],
            [SYS_DB_PREFIX.'goods_price pgp', 'pgp.profile_id = gf.id ', 'LEFT'],
            [SYS_DB_PREFIX.'category c', 'g.category_id = c.id', 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode gb', 'gb.profile_id = gf.id and gb.status=1', 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode_stock gbs', 'gbs.barcode = gb.barcode and gbs.shop_id='.$shop_id, 'LEFT'],

        ];
        $field="c.name category_name,gb.id barcode_id,gb.barcode barcode,gb.barcode barcode1,g.id goods_id,gf.name,gf.specification,gf.unit,
            gp.profile_id,gp.cost_price ,gp.retail_price ,gp.wholesale_price ,gp.delivery_price,gp.member_price1 ,0 num,gbs.stock_actual
            ";
        $list  = $this->logicGoods->getSimpleGoodsList($where,$field, 'g.update_time desc',0,$join);
        //echo model("Goods")->getLastSql();
        $this->assign('list', $list);
        return $this->fetch('goods_choose');
    }

    /**
     * 未盘点明细
     */
    public function unlogList(){

        if (empty($this->param['ids'])||empty($this->param['shop_id']))
        {
            $this->jump([RESULT_ERROR,'请选择复盘的商品和机构',url('inventory/uninventorylist')]);
        }

        $shop_id= $this->param['shop_id'];

        $join = [
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0'],
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id  ', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile pgf', 'pgf.goods_id = g.id and gf.kind=1', 'LEFT'],
            [SYS_DB_PREFIX.'goods_price pgp', 'pgp.profile_id = gf.id ', 'LEFT'],
            [SYS_DB_PREFIX.'category c', 'g.category_id = c.id', 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode gb', 'gb.profile_id = gf.id and gb.status=1', 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode_stock gbs', 'gbs.barcode = gb.barcode and gbs.shop_id='.$shop_id, 'RIGHT'],

        ];
        $field="c.name category_name,gb.id barcode_id,gb.barcode barcode,gb.barcode barcode1,g.id goods_id,gf.name,gf.specification,gf.unit,
            gp.profile_id,gp.cost_price ,gp.retail_price ,gp.wholesale_price ,gp.delivery_price,gp.member_price1 ,0 num,gbs.stock_actual
            ";
        $list  = $this->logicGoods->getSimpleGoodsList(['gbs.id'=>['in',$this->param['ids']]],$field, 'g.update_time desc',false,$join);
        //echo model("Goods")->getLastSql();
        $this->assign('list', $list);
        $this->assign('shopList',$shopList= $this->logicShop->getSimpleShopList([DATA_STATUS_NAME=>DATA_NORMAL], 'id,name,type_id', '', false));

        return $this->fetch('unlog_list');

    }


    /**
     * 编辑
     */
    public function logEdit()
    {
        $this->common();
        IS_POST && $this->jump($this->logicInventoryLog->logEdit($this->param));
    }


    /**
     * 审核
     */
    public function checking(){
        $this->common();
        $this->jump($this->logicInventoryLog->checking( $this->param));
    }

    /**
     * 审核
     */
    public function multiChecking(){
        $this->common();
        $this->jump($this->logicInventoryLog->multiChecking( $this->param));
    }

    /**
     * 复盘
     */
    public function rechecking(){
        $this->common();
        $this->jump($this->logicInventoryLog->rechecking( $this->param));
    }

    public function common(){
        if (!IS_ROOT&&!session('admin_info')['take_stock'])
        {
            $this->jump([RESULT_ERROR,'您没有盘点权限']);
        }
    }

    /**
     * 删除
     */
    public function logDel()
    {
        //审核过的商品不能删除
        $this->jump($this->logicInventoryLog->logDel(['id' => ["in",trim($this->param["ids"] ,',')],'inventory_id'=>$this->param["inventory_id"] ]));
    }



    /**
     * 导出
     */
    public function exportLogList()
    {

        $where = $this->logicInventoryLog->getWhere($this->param);

        $this->logicInventoryLog->exportLogList($where);
    }



}
