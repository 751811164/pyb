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
 * 考勤控制器
 */
class Attendance extends AdminBase
{

    public function attendanceList()
    {
        $where = $this->logicAttendance->getWhere($this->param);
        $this->assign("list", $list = $this->logicAttendance->getAttendanceList($where));


        $this->assign('adminList', $adminList = $this->logicAdmin->getAdminList([], 'a.id,a.username,s.id shop_id'));
        $this->assign('shopList', $this->logicShop->getShopList([], 's.id,s.name'));

        return $this->fetch('attendance_list');
    }



    public function exportAttendanceList(){
        $where = $this->logicAttendance->getWhere($this->param);

        $this->logicAttendance->exportAttendanceList($where);
    }


    /**
     * 设置审核状态
     */
    public function setCheckStatus()
    {
        $this->jump($this->logicAdminBase->setStatus('Attendance', $this->param,'check_status',"审核操作"));

    }

    /**
     * 设置状态
     */
    public function setStatus()
    {
        $this->jump($this->logicAdminBase->setStatus('Attendance', $this->param));
    }



}
