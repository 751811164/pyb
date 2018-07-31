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
class DeliveryOut extends AdminBase
{
    
    /**
     * 列表
     */
    public function outList()
    {
        $where = $this->logicDeliveryOut->getWhere($this->param);
        $this->assign('list', $this->logicDeliveryOut->getOutList($where));
        // echo(model("DeliveryOut")->getLastSql());
        $this->assign("shopList", $shopList = $this->logicShop->getSimpleShopList([], true, '', false));
        $this->assign("supplierList", $supplierList = $this->logicSupplier->getsupplierList([], true, '', false));

        return $this->fetch('out_list');
    }




        /**
         * 编辑
         */
        public function outEdit()
        {
//            $this->outCommon();
            IS_POST && $this->jump($this->logicDeliveryOut->outEdit($this->param));
            $this->jump(["redirect","",url("index")]);
        }


    /**
     * 删除未审核的订单
     */
    public function outDel()
    {

        $this->jump($this->logicDeliveryOut->outDel($this->param));
    }

    /**
     * 终止订单
     */
    public function outAbort(){

        $this->jump($this->logicDeliveryOut->setOutAbort($this->param));
    }

    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        if ($this->param["status"] == -1)
        {

            //$this->jump($this->logicAdminBase->setStatus('DeliveryOut', $this->param));
        }
        else
        {
            $this->jump([RESULT_ERROR, "请在单据详情里面执行审核操作"]);
        }
    }

    /**
     * 导出
     */
    public function exportOutList()
    {

        $where = $this->logicDeliveryOut->getWhere($this->param);

        $this->logicDeliveryOut->exportOutList($where);
    }

}
