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
class WholesaleGrade extends AdminBase
{
    /**
     * 列表
     */
    public function wholesaleGradeList()
    {
        $where=$this->logicWholesaleGrade->getWhere($this->param);
        $this->assign('list', $this->logicWholesaleGrade->getWholesaleGradeList($where));

        return $this->fetch('wholesaleGrade_list');
    }


    /**
     * 添加
     */
    public function wholesaleGradeAdd()
    {

        $this->wholesaleGradeCommon();
        return $this->fetch('edit_modal');
    }
    
    /**
     * 编辑
     */
    public function wholesaleGradeEdit()
    {
        $this->wholesaleGradeCommon();
        
        $info = $this->logicWholesaleGrade->getWholesaleGradeInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
//
        return $this->fetch('edit_modal');
    }

    /**
     * @throws \Exception
     */
    public function wholesaleGradeCommon()
    {
        IS_POST && $this->jump($this->logicWholesaleGrade->wholesaleGradeEdit($this->param));
    }
    

    /**
     * 删除
     */
    public function wholesaleGradeDel($id = 0)
    {
        
        $this->jump($this->logicWholesaleGrade->wholesaleGradeDel(['id' => $id]));
    }


    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('WholesaleGrade', $this->param));
    }






}
