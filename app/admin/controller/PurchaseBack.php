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
class PurchaseBack extends AdminBase
{

    /**
     * 列表
     */
    public function billList()
    {
        $where = $this->logicPurchaseBack->getWhere($this->param);
        $this->assign('list', $this->logicPurchaseBack->getBillList($where));
        // echo(model("PurchaseBack")->getLastSql());
        $this->assign("shopList", $shopList = $this->logicShop->getSimpleShopList([], true, '', false));
        $this->assign("supplierList", $supplierList = $this->logicSupplier->getsupplierList([], true, '', false));

        return $this->fetch('bill_list');
    }




        /**
         * 编辑
         */
        public function billEdit()
        {
            IS_POST && $this->jump($this->logicPurchaseBack->billEdit($this->param));
        }


    /**
     * 删除未审核的采购入库单
     */
    public function billDel()
    {
        $this->jump($this->logicPurchaseBack->billDel($this->param));
    }


    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        if ($this->param["status"] == -1)
        {

            //$this->jump($this->logicAdminBase->setStatus('PurchaseBack', $this->param));
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

        $where = $this->logicPurchaseBack->getWhere($this->param);

        $this->logicPurchaseBack->exportBillList($where);
    }

}
