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
use think\Db;

/**
 * 请假控制器
 */
class Leave extends AdminBase
{

    public function leaveList()
    {
        $where = $this->logicLeave->getWhere($this->param);
        $this->assign("list", $list = $this->logicLeave->getLeaveList($where));
        return $this->fetch('leave_list');
    }

    public function leaveAdd()
    {
        $this->common();
        return $this->fetch('edit_modal');
    }


    public function leaveEdit()
    {
        $this->common();
        return $this->fetch('edit_modal');
    }

    public function common()
    {
        IS_POST && $this->jump($this->logicLeave->LeaveEdit($this->param));
        $this->assign('adminList', $adminList = $this->logicAdmin->getAdminList([], 'a.id,a.username,s.id shop_id'));
        $this->assign('shopList', $this->logicShop->getShopList([], 's.id,s.name'));
        $info=[];
        if (array_key_exists('id',$this->param)&&!empty($this->param['id']))
        {
         $info= $this->logicLeave->getLeaveInfo($this->param);
        }
        $this->assign('info',$info);
    }



    /**
     * 设置审核状态
     */
    public function setCheckStatus()
    {
        $this->jump($this->logicAdminBase->setStatus('Leave', $this->param,'check_status',"审核操作"));

    }

    /**
     * 设置状态
     */
    public function setStatus()
    {
        $this->jump($this->logicAdminBase->setStatus('Leave', $this->param));
    }



}
