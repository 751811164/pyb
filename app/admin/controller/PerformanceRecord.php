<?php
/**
 * Author: Jeary
 * Date: 2018/4/16 9:50
 * Desc: created by PhpStorm
 */

namespace app\admin\controller;


class PerformanceRecord extends AdminBase
{


    /**
     * 查看列表
     * @return mixed
     */
    public function recordList()
    {



        if (IS_AJAX && array_key_exists('type_id', $this->param))
        {
            //查询岗位配置信息
            return $this->getRecord();
        }

        $info = $this->logicAuthGroup->getGroupTree();
        $this->assign('info', $info);

        $where[DATA_STATUS_NAME] = DATA_NORMAL;
        if (array_key_exists('gid', $this->param))
        {
            $where['group_id'] = $this->param['gid'];

            $types    = $this->logicSalaryStructure->getStructureInfo($where, 'salary_types');
            $typesId  = !empty($types) ? $types->getAttr('salary_types'): null;
            $typeList = $this->logicSalaryType->getTypeList(['id' => ['in', $typesId]], true, '', false);
        }
        else
        {
            $typeList = $this->logicSalaryType->getTypeList([], true);
        }
        $this->assign("typeList", $typeList);


      $this->assign('salaryTypeList',  $salaryTypeList = $this->logicSalaryType->getTypeList( [],  true, '', false));
        $this->assign('adminList', $adminList = $this->logicAdmin->getSimpleAdminList( ['id'=>['neq',SYS_ADMINISTRATOR_ID]],  true, '', false));

        return $this->fetch("record_list");
    }






    public function getRecord(){

        $where["group_id"] = $this->param["gid"];
        $where["type_id"] = $this->param["type_id"];

       $list= $this->logicPerformanceRecord->getRecordList($where);
//        $logicLayer = "logic".ucfirst(lineToHump($this->param["tb"]));
//
//        $list = $this->$logicLayer->getSettingList($where);
        $this->assign("list", $list);
        return $this->fetch('list');

    }


    public function recordAdd(){
        IS_POST && $this->jump($this->logicPerformanceRecord->recordEdit($this->param));
    }

    /**
     * 状态设置
     */
    public function setStatus()
    {
//        $layer      = ucfirst(lineToHump($this->param["tb"]));
        $layer = "PerformanceRecord";
        $logicLayer = 'logic'.$layer;
        $this->jump($this->$logicLayer->setStatus($layer, $this->param));
        //        $this->jump($this->logicAdminBase->setStatus($layer, $this->param));
    }

}