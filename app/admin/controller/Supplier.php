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
class Supplier extends AdminBase
{
    /**
     * 列表
     */
    public function supplierList()
    {

        $where=$this->logicSupplier->getWhere($this->param);
        $this->assign('list', $list = $this->logicSupplier->getSupplierList($where));

        //查找已有的省份
       $province_ids= $this->logicSupplier->getSupplierColumn([],"province_id");
       $where_region=function($query)use($province_ids){
           $query->where("id|upid","in",$province_ids);
       };
       //省市
        $regionList= $this->logicRegion->getRegionList($where_region,"id,name,upid pId,level");

        $this->assign("regionList",$regionList);

        return $this->fetch('supplier_list');
    }


    /**
     * 添加
     */
    public function supplierAdd()
    {

        $this->supplierCommon();
        $this->assign('info', []);
        $this->assign('contacts', []);
        return $this->fetch('edit_modal');
    }
    
    /**
     * 编辑
     */
    public function supplierEdit()
    {
        $this->supplierCommon();
        

        $this->assign('info',  $info = $this->logicSupplier->getSupplierInfo(['s.id' => $this->param['id']]));
        $this->assign('contacts',  $contacts = $this->logicSupplierContacts->getContactsList(["supplier_id"=>$this->param['id'], 'c.'.DATA_STATUS_NAME => DATA_NORMAL],true,'',false ));


//
        return $this->fetch('edit_modal');
    }

    /**
     * @throws \Exception
     */
    public function supplierCommon()
    {
        IS_POST && $this->jump($this->logicSupplier->supplierEdit($this->param));
    }


    /**
     * 删除关联联系人
     */
    public function contactDel($id){
        $this->jump($this->logicSupplierContacts->contactsDel(['id' => $id]));
    }

    /**
     * 删除
     */
    public function supplierDel($id = 0)
    {
        
        $this->jump($this->logicSupplier->supplierDel(['id' => $id]));
    }


    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        $this->param["check_time"] = time();
        $this->jump($this->logicAdminBase->setStatus('Supplier', $this->param));
    }






}
