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
 * 店铺支出控制器
 */
class ShopDisburse extends AdminBase
{
    
    /**
     * 店铺支出列表
     */
    public function shopDisburseList()
    {
        
        $where = $this->logicShopDisburse->getWhere($this->param);
        
        $this->assign('list', $this->logicShopDisburse->getShopDisburseList($where, 'sd.*,s.name as shop_name,dt.name as type_name', 'sd.create_time desc'));
        
        return $this->fetch('shop_disburse_list');
    }
    
    /**
     * 店铺支出添加
     */
    public function shopDisburseAdd()
    {
        
        $this->shopDisburseCommon();
        
        return $this->fetch('shop_disburse_edit');
    }
    
    /**
     * 店铺支出编辑
     */
    public function shopDisburseEdit()
    {
        
        $this->shopDisburseCommon();
        
        $info = $this->logicShopDisburse->getShopDisburseInfo(['id' => $this->param['id']]);
        

        $this->assign('info', $info);
        
        return $this->fetch('shop_disburse_edit');
    }
    
    /**
     * 店铺支出添加与编辑通用方法(获取店铺和支出类型列表)
     */
    public function shopDisburseCommon()
    {
        
        IS_POST && $this->jump($this->logicShopDisburse->shopDisburseEdit($this->param));
        
        $this->assign('shop_list', $this->logicShop->getShopList([], 'id,name', '', false));
        $this->assign('disburse_list',  $this->logicDisburseType->getDisburseTypeList([], 'id,name', '', false));
    }
    



    
    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        
        $this->jump($this->logicAdminBase->setStatus('ShopDisburse', $this->param));
    }
}
