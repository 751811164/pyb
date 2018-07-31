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
class PurchaseInLog extends AdminBase
{
    /**
     * 列表
     */
    public function logList()
    {
        $info=[DATA_STATUS_NAME=>DATA_DELETE]; $list=[];


        if (!empty($this->param["in_id"]))
        {


            //采购收货单信息
            $info=$this->logicPurchaseIn->getBillJoinInfo(["pi.id"=>$this->param["in_id"]],"pi.*,add.username admin_add_name,check.username admin_check_name,pb.sn bill_sn");
            $where = $this->logicPurchaseInLog->getWhere($this->param);
            //列表明细
            $list=$this->logicPurchaseInLog->getLogList($where,"pl.*,gf.barcode1,gf.name,gf.unit,gf.specification",'pl.id asc',false);
            // 采购订单信息
//            $billInfo=$this->logicPurchaseBill->getBillInfo(["id"=>$info["bill_id"],DATA_STATUS_NAME=>DATA_NORMAL],true);

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

        $where = $this->logicPurchaseInLog->getGoodsChooseWhere($this->param);

        $join = [
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0'],
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id  ', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile pgf', 'pgf.goods_id = g.id and gf.kind=1', 'LEFT'],
            [SYS_DB_PREFIX.'goods_price pgp', 'pgp.profile_id = gf.id ', 'LEFT'],
            [SYS_DB_PREFIX.'category c', 'g.category_id = c.id', 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode gb', 'gb.profile_id = gf.id and gb.status=1', 'LEFT'],

        ];
        $field="c.name category_name,gb.id barcode_id,gb.barcode barcode,gb.barcode barcode1,g.id goods_id,gf.name,gf.specification,gf.unit,
            gp.profile_id,gp.cost_price ,gp.retail_price ,gp.wholesale_price ,gp.delivery_price,gp.member_price1 ,1 num
            ";
        $list  = $this->logicGoods->getSimpleGoodsList($where,$field, 'g.update_time desc',0,$join);
        //echo model("Goods")->getLastSql();
        $this->assign('list', $list);
        return $this->fetch('goods_choose');
    }

    /**
     * 选择采购订单
     */
    public function billChoose(){
        $this->view->engine->layout(false);
        $where["pb.".DATA_STATUS_NAME] =DATA_NORMAL;
        $where["pb.use_status"] =DATA_DISABLE;
//        $where["pb.is_reference"] =DATA_DISABLE;
        $list=  $this->logicPurchaseBill->getBillList($where);
        $this->assign('list', $list);
        return $this->fetch('bill_choose');
    }

    /**
     * 获取采购订单明细
     */
    public function getPurchaseBillLogList(){
        $list=$this->logicPurchaseBillLog->getLogList(
            ["bill_id"=>$this->param["bill_id"],"pl.".DATA_STATUS_NAME=>DATA_NORMAL],
            "pl.*,gf.name,gf.unit,gf.specification,null id",'pl.id asc',false);
        return json($list);
    }

    /**
     * 验证条码是否存在
     */
    public function existBarcode(){
         $this->jump($this->logicPurchaseInLog->existBarcode($this->param));
    }


    /**
     * 编辑
     */
    public function logEdit()
    {

        IS_POST && $this->jump($this->logicPurchaseInLog->logEdit($this->param));

    }

    /**
     * @throws \Exception
     */
    public function logCommon()
    {
        IS_POST && $this->jump($this->logicPurchaseInLog->logEdit($this->param));
//        $this->assign("shopList",$shopList=$this->logicShop->getSimpleShopList([],true,'',false));

    }

    /**
     * 审核
     */
    public function checking(){
        $this->jump($this->logicPurchaseInLog->checking( $this->param));
    }

//    /**
//     * 反审核
//     */
//    public function unchecking(){
//        $this->jump($this->logicPurchaseInLog->unchecking( $this->param));
//    }


    /**
     * 删除
     */
    public function logDel()
    {
        //审核过的商品不能删除
        $this->jump($this->logicPurchaseInLog->logDel(['id' => ["in",trim($this->param["ids"] ,',')],'in_id'=>$this->param["in_id"] ]));
    }



    /**
     * 导出
     */
    public function exportLogList()
    {

        $where = $this->logicPurchaseInLog->getWhere($this->param);

        $this->logicPurchaseInLog->exportLogList($where);
    }



}
