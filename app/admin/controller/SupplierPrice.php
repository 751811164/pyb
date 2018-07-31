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
class SupplierPrice extends AdminBase
{




    /**
     * 选择供应商
     */
    public function priceList()
    {
        IS_POST && $this->jump($this->logicGoods->batchSingleEdit($this->param));
        $where[DATA_STATUS_NAME] = DATA_NORMAL;

        //供应商
        $this->assign('supplierList', $supplierList=$this->logicSupplier->getSupplierList($where, 'id,name', '', false));
        //会员等级
        $this->assign('memberGradeList',$memberGradeList= $this->logicMemberGrade->getSimpleMemberGradeList($where, 'id,name', '', false));

        $list= $this->logicSupplierPrice->getPriceList(["goods_id"=>$this->param["id"],'sp.'.DATA_STATUS_NAME=>DATA_NORMAL], '*,sp.id', '', false);
        $this->assign('list',$list=collection($list)->toArray());

        return $this->fetch('price_list');
    }




//
//    /**
//     * 添加
//     */
//    public function supplierAdd()
//    {
//
//        $this->supplierCommon();
//        return $this->fetch('edit_modal');
//    }
//

//
    /**
     * @throws \Exception
     */
    public function priceEdit()
    {
        IS_POST && $this->jump($this->logicSupplierPrice->priceEdit($this->param));
    }
//

    /**
     * 删除
     */
    public function priceDel($id = 0)
    {
        
        $this->jump($this->logicSupplierPrice->priceDel(['id' => $id]));
    }


//    /**
//     * 数据状态设置
//     */
//    public function setStatus()
//    {
//
//        $this->jump($this->logicAdminBase->setStatus('SupplierPrice', $this->param));
//    }






}
