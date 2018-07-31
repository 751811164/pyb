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
 * 支出类型控制器
 */
class DisburseType extends AdminBase
{
    /**
     * 支出类型列表
     */
    public function disburseTypeList()
    {

        $this->assign('list', $this->logicDisburseType->getDisburseTypeList([],true,'',DB_LIST_ROWS));

        return $this->fetch('disburse_type_list');
    }


    /**
     * 支出类型添加
     */
    public function disburseTypeAdd()
    {
        
        IS_POST && $this->jump($this->logicDisburseType->disburseTypeEdit($this->param));
        
        return $this->fetch('disburse_type_edit');
    }
    
    /**
     * 支出类型编辑
     */
    public function disburseTypeEdit()
    {
        
        IS_POST && $this->jump($this->logicDisburseType->disburseTypeEdit($this->param));
        
        $info = $this->logicDisburseType->getDisburseTypeInfo(['id' => $this->param['id']]);
        
        $this->assign('info', $info);
        
        return $this->fetch('disburse_type_edit');
    }
    

    /**
     * 支出类型删除
     */
    public function disburseTypeDel($id = 0)
    {
        
        $this->jump($this->logicDisburseType->disburseTypeDel(['id' => $id]));
    }


    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('DisburseType', $this->param));
    }


//    /**
//     * 排序
//     */
//    public function setSort()
//    {
//
//        $this->jump($this->logicAdminBase->setSort('DisburseType', $this->param));
//    }

}
