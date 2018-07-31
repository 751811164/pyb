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
class SalaryStructure extends AdminBase
{
    /**
     * 列表
     */
    public function structureList()
    {
        $where = [];
        array_key_exists('sid', $this->param) && $where['s.id'] = $this->param['sid'];

        $this->assign('list', $list = $this->logicSalaryStructure->getStructureList($where));

        //        $info = $this->logicAuthGroup->getGroupTree();
        //        $this->assign('info', $info);

        $shopTree = $this->logicShop->getShopTree();
        $this->assign('shopTree', $shopTree);

        return $this->fetch('structure_list');
    }

    /**
     * 添加
     */
    public function structureAdd()
    {
        $this->structureCommon();
        return $this->fetch('edit_modal');
    }


    /**
     * 编辑
     */
    public function structureEdit()
    {
        $this->structureCommon();
        return $this->fetch('edit_modal');
    }

    public function structureCommon()
    {

        IS_POST && $this->jump($this->logicSalaryStructure->structureEdit($this->param));


        $salaryList = $this->logicSalaryType->getTypeList([], true, "", false, [], '');
        foreach ($salaryList as $key => $val)
        {
            $salaryList[$key]->setAttr('describeJson', json_decode($val['describe'], true));
        }
        $this->assign("salaryList", $salaryList);
        $info = [];
        if (!empty($this->param['id']))
        {
            $join                          = [
                [SYS_DB_PREFIX.'auth_group g', 'g.id = ss.group_id', 'LEFT'],
                [SYS_DB_PREFIX.'shop s', 's.id = g.shop_id', 'LEFT'],
            ];
            $where['ss.id']                = $this->param['id'];
            $where['g.'.DATA_STATUS_NAME]  = ['neq', DATA_DELETE];
            $where['ss.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
            $info                          = $this->logicSalaryStructure->getStructureJoinInfo($where, 'ss.*,g.shop_id,s.type_id', $join);
        }

        else if (!empty($this->param['sid']))
        {
            $info = $this->logicShop->getShopInfo(['id' => $this->param['sid']], 'id shop_id,type_id');
        }
        $this->assign('info', $info);
    }


    /**
     * 删除
     */
    public function setStatus()
    {
        if (array_key_exists(DATA_STATUS_NAME, $this->param) && $this->param[DATA_STATUS_NAME] == DATA_NORMAL)
        {
            $where['group_id']       = $this->param['group_id'];
            $where[DATA_STATUS_NAME] = DATA_NORMAL;
            $this->logicSalaryStructure->setStatusDisable($where);
        }

        $this->jump($this->logicAdminBase->setStatus('SalaryStructure', $this->param));

    }


}
