<?php
/**
 * Author: Jeary
 * Date: 2018/4/16 9:50
 * Desc: created by PhpStorm
 */

namespace app\admin\controller;


class Performance extends AdminBase
{

    //region    绩效结构

    /**
     * 列表
     * @return mixed
     */
    public function performanceList()
    {

        if (IS_AJAX && array_key_exists('tb', $this->param))
        {
            //查询岗位配置信息
            return $this->getSetting();
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
        return $this->fetch("performance_list");
    }


    /**
     * 加载不同类别的绩效
     * @return mixed
     */
    protected function getSetting()
    {


        $where["group_id"] = $this->param["gid"];

        $logicLayer = "logic".ucfirst(lineToHump($this->param["tb"]));

        $list = $this->$logicLayer->getSettingList($where);
        $this->assign("list", $list);
        return $this->fetch($this->param["tb"]);

    }

    /**
     * 添加
     */
    public function settingAdd()
    {
        $this->settingCommon();
        return $this->fetch($this->param["tb"].'_edit');;
    }


    /**
     * 修改
     */
    public function settingEdit()
    {
        $this->settingCommon();
        return $this->fetch($this->param["tb"].'_edit');
    }


    public function settingCommon()
    {
        $layer      = ucfirst(lineToHump($this->param["tb"]));
        $logicLayer = 'logic'.$layer;
        IS_POST && $this->jump($this->$logicLayer->SettingEdit($this->param));
        $where[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        if (array_key_exists("id", $this->param))
        {
            $where['id'] = $this->param['id'];
            $info        = $this->$logicLayer->getSettingInfo($where);
        }
        else
        {
            $info = [];
        }

        $this->assign('info', $info);

        action($layer.'/common', ['info' => $info]);
    }


    /**
     * 状态设置
     */
    public function setStatus()
    {
        $layer      = ucfirst(lineToHump($this->param["tb"]));
        $logicLayer = 'logic'.$layer;
        $this->jump($this->$logicLayer->setStatus($layer, $this->param));
        //        $this->jump($this->logicAdminBase->setStatus($layer, $this->param));
    }


    /**
     * 搜索商品
     * @return array
     */
    public function searchGoods()
    {
        $this->view->engine->layout(false);
        //        $this->engine(false);
        $where = $this->logicGoods->getWhere($this->param);
        $list  = $this->logicGoods->getGoodsList($where, "g.id,gf.barcode1,gf.name,specification,unit", "", false);

        return collection($list)->toArray();
    }

    //endregion
}