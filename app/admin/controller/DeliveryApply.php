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
class DeliveryApply extends AdminBase
{
    
    /**
     * 列表
     */
    public function applyList()
    {
        $where = $this->logicDeliveryApply->getWhere($this->param);
        $this->assign('list', $this->logicDeliveryApply->getApplyList($where));
        // echo(model("DeliveryApply")->getLastSql());
        $this->assign("shopList", $shopList = $this->logicShop->getSimpleShopList([], true, '', false));
        $this->assign("supplierList", $supplierList = $this->logicSupplier->getsupplierList([], true, '', false));

        return $this->fetch('apply_list');
    }




        /**
         * 编辑
         */
        public function applyEdit()
        {
//            $this->applyCommon();
            IS_POST && $this->jump($this->logicDeliveryApply->applyEdit($this->param));
            $this->jump(["redirect","",url("index")]);
        }


    /**
     * 删除未审核的采购订单
     */
    public function applyDel()
    {

        $this->jump($this->logicDeliveryApply->applyDel($this->param));
    }

    /**
     * 终止采购订单
     */
    public function applyAbort(){

        $this->jump($this->logicDeliveryApply->setApplyAbort($this->param));
    }

    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        if ($this->param["status"] == -1)
        {

            //$this->jump($this->logicAdminBase->setStatus('DeliveryApply', $this->param));
        }
        else
        {
            $this->jump([RESULT_ERROR, "请在单据详情里面执行审核操作"]);
        }
    }

    /**
     * 导出
     */
    public function exportApplyList()
    {

        $where = $this->logicDeliveryApply->getWhere($this->param);

        $this->logicDeliveryApply->exportApplyList($where);
    }

}
