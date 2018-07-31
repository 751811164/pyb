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
 * 班次控制器
 */
class WorkdayArrange extends AdminBase
{

    public function arrangeList()
    {
        $where         = $this->logicWorkdayArrange->getWhere($this->param);
        array_key_exists('sid',$this->param)&&$where['g.shop_id']=$this->param['sid'];
        $this->assign('list', $list = $this->logicWorkdayArrange->getArrangeList($where));

        $shopTree = $this->logicShop->getShopTree();
        $this->assign('shopTree', $shopTree);

        return $this->fetch('arrange_list');
    }

    public function arrangeAdd()
    {
        $this->arrangeCommon();
        return $this->fetch('edit_modal');
    }


    public function arrangeEdit()
    {
        $this->arrangeCommon();
        return $this->fetch('edit_modal');
    }

    public function arrangeCommon()
    {

        IS_POST && $this->jump($this->logicWorkdayArrange->arrangeEdit($this->param));
        $info = [];
//        $where_admin=['id'=>['neq',SYS_ADMINISTRATOR_ID]];
//        $days = new \stdClass();
        if (!empty($this->param['admin_id']))
        {
            $info              = $this->logicWorkdayArrange->getArrangeInfo($this->param);
            $shopInfo = $this->logicAdminBase->authShopPermission($info['admin_id'],'ga.group_id,g.shop_id');
            $info['shop_id']=$shopInfo['shop_id'];
            $info['group_id']=$shopInfo['group_id'];
//            $where["year"]     = date("Y");
//            $where["month"]    = date("n");
//            $where["admin_id"] = $info["admin_id"];
//            $info['admin_id']=$info["admin_id"]?:$this->param["admin_id"];
//            $days = $this->logicArrangeDate->getDateInfo($where);
        }
//        else
//        {
//            //查询未添加过班次的员工(已添加过的员工不在选择列表里面)
//            $data= $this->logicWorkdayArrange->getArrangeInfo([DATA_STATUS_NAME=>DATA_NORMAL],'GROUP_CONCAT(admin_id) as ids')->toArray();
//            if ($data['ids'])
//            {
//                $where_admin['id']=['not in', explode(",",$data['ids'].",".SYS_ADMINISTRATOR_ID)];
//            }
//
//        }
//        $this->assign("adminList", $admin = $this->logicAdmin->getSimpleAdminList($where_admin, 'id,username', '', 0));

        $this->assign("info", $info);
//        $this->assign("days", $days);
    }

    public function viewCalendar()
    {
        $this->param["year"]  = $this->param["year"] ?: date("Y");
        $this->param["month"] = $this->param["month"] ?: date("n");
        $days                 = $this->logicArrangeDate->getDateInfo($this->param);
        $this->assign("days", $days);
        return $this->fetch('view_calendar');
    }


    /**
     * 删除
     */
    public function setStatus()
    {
        if (array_key_exists(DATA_STATUS_NAME,$this->param)&& $this->param[DATA_STATUS_NAME] ==DATA_NORMAL)
        {
            $where[DATA_STATUS_NAME] = DATA_NORMAL;
            $where['admin_id'] = $this->param['admin_id'];
            $this->logicWorkdayArrange->setStatusDisable($where);
        }
        //此处传admin_id 删除workdayArrange 里面的信息
        $this->jump($this->logicAdminBase->setStatus('WorkdayArrange', $this->param));
    }


}
