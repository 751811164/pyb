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
class ShopPriceBill extends AdminBase
{
    /**
     * 列表
     */
    public function billList()
    {
        $where=$this->logicShopPriceBill->getWhere($this->param);
        $this->assign('list', $this->logicShopPriceBill->getBillList($where));
       // echo(model("ShopPriceBill")->getLastSql());
        $this->assign("shopList",$shopList=$this->logicShop->getSimpleShopList([],true,'',false));

        return $this->fetch('bill_list');
    }


    /**
     * 添加
     */
    public function billAdd()
    {

        $this->billCommon();
        return $this->fetch('edit_modal');
    }
    
    /**
     * 编辑
     */
    public function billEdit()
    {
        $this->billCommon();
        
        $info = $this->logicShopPriceBill->getShopPriceBillInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);

        return $this->fetch('edit_modal');
    }

    /**
     * @throws \Exception
     */
    public function billCommon()
    {
        IS_POST && $this->jump($this->logicShopPriceBill->billEdit($this->param));
        $this->assign("shopList",$shopList=$this->logicShop->getSimpleShopList([],true,'',false));

    }
    

    /**
     * 删除
     */
    public function billDel($id = 0)
    {
        
        $this->jump($this->logicShopPriceBill->billDel(['id' => $id]));
    }


    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        if ($this->param["status"]==-1)
        {
            
        $this->jump($this->logicAdminBase->setStatus('ShopPriceBill', $this->param));
        }
        else
        {
            $this->jump([RESULT_ERROR,"请在单据详情里面执行审核操作"]);
        }
    }





}
