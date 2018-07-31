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
class PurchaseBill extends AdminBase
{


    public function index()
    {
        $step = "step".(empty($this->param["step"]) ? "1": $this->param["step"]);
        $this->$step();
        return $this->fetch($step);
    }

    /**
     * 采购向导1
     */
    public function step1()
    {
        $where[DATA_STATUS_NAME] = DATA_NORMAL;
        //店铺
        $this->assign('typeList', $typeList = $this->logicShopType->getTypeList($where, 'id,name', '', false));
        $this->assign('shopList', $shopList = $this->logicShop->getSimpleShopList($where, 'id,name,type_id', '', false));

        //品牌
        $this->assign('brandList', $this->logicBrand->getBrandList($where, 'id,name', '', false));
        //供应商
        $this->assign('supplierList', $supplierList = $this->logicSupplier->getSupplierList(['s.'.DATA_STATUS_NAME => DATA_NORMAL], 'id,name', '', false));

        //分类
//        $pids        = $this->logicCategory->getCategoryColumn([DATA_STATUS_NAME => DATA_NORMAL], "pid");
//        $where["id"] = ["not in", array_unique($pids)];
        $where_category["level"] =config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));

        //人员所在店铺
        if (ADMIN_ID !=IS_ROOT)
        {
            $join = [
                [SYS_DB_PREFIX.'auth_group_access ga', 'ga.group_id = g.id'],
            ];
            $res  = $this->logicAuthGroup->getGroupInfo(["ga.admin_id" => ADMIN_ID], "shop_id", $join);
            $adminInfo=session("admin_info");
            $adminInfo["shop_id"] = empty($res) ? null: $res["shop_id"];
            $this->assign("adminInfo",$adminInfo);
        }


    }

    /**
     * 采购向导2
     */
    public function step2()
    {
//        dump($this->param);
        if (!isset($this->param["purchase_type"]))
        {
            $this->jump([RESULT_ERROR,"访问有误"]);
        }
        if (!isset($this->param["shop_ids"]))
        {
            $this->jump([RESULT_ERROR,"请选择统计范围"]);
        }

        session("billInfo",$this->request->post());
        if ($this->param["purchase_type"] == 1)
        {
            //@TODO
            //按销售额采购
        }
        else
        {
            //按库存上限-实际库存采购
            !empty($this->param["shop_ids"]) && $where["gs.shop_id"] = ["in", $this->param["shop_ids"]];
            !empty($this->param["category_id"]) && $where["g.category_id"] = ["in", $this->param["category_id"]];
            !empty($this->param["brand_id"]) && $where["g.brand_id"] = ["in", $this->param["brand_id"]];
            !empty($this->param["supplier_id"]) && $where["g.supplier_id"] =  $this->param["supplier_id"];
            $field='g.*,gf.*,gs.*,c.name category_name,gt.name as type_name,s.name supplier_name,
            sum(gs.stock_ceil) total_stock_ceil,sum(gs.stock_floor) total_stock_floor, IFNULL(sum(gbs.stock_actual),0) total_stock_actual';
           $list= $this->logicGoodsStockSetting->getStockList($where,$field,'',false,[],'g.id');
        }
        $this->assign("list", $list);
//        echo model("GoodsStockSetting")->getLastSql();

    }

    /**
     * 列表
     */
    public function billList()
    {
        $where = $this->logicPurchaseBill->getWhere($this->param);
        $this->assign('list', $this->logicPurchaseBill->getBillList($where));
        // echo(model("PurchaseBill")->getLastSql());
        $this->assign("shopList", $shopList = $this->logicShop->getSimpleShopList([], true, '', false));
        $this->assign("supplierList", $supplierList = $this->logicSupplier->getsupplierList([], true, '', false));

        return $this->fetch('bill_list');
    }




        /**
         * 编辑
         */
        public function billEdit()
        {
//            $this->billCommon();
            IS_POST && $this->jump($this->logicPurchaseBill->billEdit($this->param));

//            $info = $this->logicPurchaseBill->getPurchaseBillInfo(['id' => $this->param['id']]);
//            $this->assign('info', $info);
//
//            return $this->fetch('edit_modal');
            $this->jump(["redirect","",url("index")]);
        }

//        /**
//         * @throws \Exception
//         */
//        public function billCommon()
//        {
//            IS_POST && $this->jump($this->logicPurchaseBill->billEdit($this->param));
//            $this->assign("shopList",$shopList=$this->logicShop->getSimpleShopList([],true,'',false));
//
//        }


    /**
     * 删除未审核的采购订单
     */
    public function billDel()
    {

        $this->jump($this->logicPurchaseBill->billDel($this->param));
    }

    /**
     * 终止采购订单
     */
    public function billAbort(){

        $this->jump($this->logicPurchaseBill->setBillAbort($this->param));
    }

    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        if ($this->param["status"] == -1)
        {

            //$this->jump($this->logicAdminBase->setStatus('PurchaseBill', $this->param));
        }
        else
        {
            $this->jump([RESULT_ERROR, "请在单据详情里面执行审核操作"]);
        }
    }

    /**
     * 导出
     */
    public function exportBillList()
    {

        $where = $this->logicPurchaseBill->getWhere($this->param);

        $this->logicPurchaseBill->exportBillList($where);
    }

}
