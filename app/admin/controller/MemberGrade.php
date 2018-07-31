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
class MemberGrade extends AdminBase
{
    /**
     * 列表
     */
    public function memberGradeList()
    {
        $where=$this->logicMemberGrade->getWhere($this->param);
        $this->assign('list', $this->logicMemberGrade->getMemberGradeList($where));

        return $this->fetch('memberGrade_list');
    }


    /**
     * 添加
     */
    public function memberGradeAdd()
    {

        $this->memberGradeCommon();
        return $this->fetch('edit_modal');
    }
    
    /**
     * 编辑
     */
    public function memberGradeEdit()
    {
        $this->memberGradeCommon();
        
        $info = $this->logicMemberGrade->getMemberGradeInfo(['id' => $this->param['id']]);
        $this->assign('info', $info);
//
        return $this->fetch('edit_modal');
    }

    /**
     * @throws \Exception
     */
    public function memberGradeCommon()
    {
        IS_POST && $this->jump($this->logicMemberGrade->memberGradeEdit($this->param));
    }
    

    /**
     * 删除
     */
    public function memberGradeDel($id = 0)
    {
        
        $this->jump($this->logicMemberGrade->memberGradeDel(['id' => $id]));
    }


    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('MemberGrade', $this->param));
    }






}
