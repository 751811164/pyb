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
class DeliveryIn extends AdminBase
{
    
    /**
     * 列表
     */
    public function inList()
    {
        $where = $this->logicDeliveryIn->getWhere($this->param);
        $this->assign('list', $this->logicDeliveryIn->getInList($where));
        // echo(model("DeliveryIn")->getLastSql());
        $this->assign("shopList", $shopList = $this->logicShop->getSimpleShopList([], true, '', false));
        $this->assign("supplierList", $supplierList = $this->logicSupplier->getsupplierList([], true, '', false));

        return $this->fetch('in_list');
    }




        /**
         * 编辑
         */
        public function inEdit()
        {
//            $this->inCommon();
            IS_POST && $this->jump($this->logicDeliveryIn->inEdit($this->param));
            $this->jump(["redirect","",url("index")]);
        }


    /**
     * 删除未审核的订单
     */
    public function inDel()
    {

        $this->jump($this->logicDeliveryIn->inDel($this->param));
    }

    /**
     * 终止订单
     */
    public function inAbort(){

        $this->jump($this->logicDeliveryIn->setInAbort($this->param));
    }

    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        if ($this->param["status"] == -1)
        {

            //$this->jump($this->logicAdminBase->setStatus('DeliveryIn', $this->param));
        }
        else
        {
            $this->jump([RESULT_ERROR, "请在单据详情里面执行审核操作"]);
        }
    }

    /**
     * 导出
     */
    public function exportInList()
    {

        $where = $this->logicDeliveryIn->getWhere($this->param);

        $this->logicDeliveryIn->exportInList($where);
    }

}
