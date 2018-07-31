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

namespace app\admin\logic;

/**
 * 考勤逻辑
 */
class Attendance extends AdminBase
{

    /**
     * 班次信息
     */
    public function getAttendanceInfo($where = [], $field = true)
    {
        $info = $this->modelAttendance->getInfo($where, $field);
        return $info;
    }

    /**
     * 获取员工班次列表
     */
    public function getAttendanceList($where = [], $field = 'a.username,a.number,a.portrait_id,g.name group_name,s.name shop_name,wa.*,at.*,a.id admin_id', $order = '', $paginate = DB_LIST_ROWS)
    {

        $this->modelAttendance->alias('at');

        $join = [
            [SYS_DB_PREFIX.'admin a', 'a.id = at.admin_id'],
            [SYS_DB_PREFIX.'auth_group_access ga', 'ga.admin_id = a.id', 'LEFT'],
            [SYS_DB_PREFIX.'auth_group g', 'g.id = ga.group_id', 'LEFT'],
            [SYS_DB_PREFIX.'shop s', 's.id = g.shop_id', 'LEFT'],
            [SYS_DB_PREFIX.'workday_arrange wa', 'wa.admin_id = at.admin_id and wa.status=1', 'LEFT'],
        ];

        $where['a.'.DATA_STATUS_NAME]  = ['neq', DATA_DELETE];
        $where['at.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['a.id']                 = ['neq', SYS_ADMINISTRATOR_ID];

        $list = $this->modelAttendance->getList($where, $field, $order, $paginate, $join);
        var_dump($list->toArray());
        return $list->each($this->calcAttendanceInfo());
    }

    //计算考勤信息
    function calcAttendanceInfo()
    {
        return function($item) {
            //          $item['aa']=1;
            $timestamp = strtotime($item['create_time']);
            //1.查询请假信息
//            $leave_where = " admin_id= {$item['admin_id']} and '". $timestamp."' BETWEEN start_time and  end_time and status =1";

                        $leave_where["admin_id"]=$item['admin_id'];
                        $leave_where[DATA_STATUS_NAME]=DATA_NORMAL;
                        $leave_where['start_time']= ['<',$timestamp];
                        $leave_where['end_time']= ['>=',strtotime( date("Y-m-d",$timestamp))];

            $leaveInfo = $this->logicLeave->getLeaveInfo($leave_where);//请假信息

//echo $this->logicLeave->getLastSql();
            // 2.判断班次和请假情况


            $arr = ['a', 'b', 'c'];

            #region foreach

            foreach ($arr as $key)
            {

                if (!empty($leaveInfo))//有请假
                {
                    $item[$key.'_on_text']  = "请假";
                    $item[$key.'_off_text'] = "请假";
                    $item['leave_days']     = $leaveInfo['days'];
                }
                else//没请假
                {
                    #region 上班
                    if (empty($item[$key.'_on_img']))//上班未打卡
                    {
                        $item[$key.'_on_text'] = "<span class='text-red'>未打卡</span>";
                    }
                    else
                    {

                        $diff = strtotime($item[$key.'_on_time']) - strtotime($item[$key.'_on']);
                        if ($diff < 0)//正常
                        {
                            $item[$key.'_on_text'] = '正常';
                        }
                        elseif ($diff < $item[$key.'_late']*60)//异常
                        {
                            $item[$key.'_on_text'] = "<span class='text-yellow'>迟到</span>";
                        }
                        else//超出范围
                        {
                            $item[$key.'_on_text'] = "<span class='text-red'>异常</span>";
                        }
                    }
                    #endregion 上班

                    #region 下班
                    if (empty($item[$key.'_off_img']))//下班未打卡
                    {
                        $item[$key.'_off_text'] = "未打卡";
                    }
                    else
                    {
                        //计算迟到

                        $diff = (strtotime($item[$key.'_off']) - strtotime($item[$key.'_off_time'])) ;
                        if ($diff <= 0)
                        {
                            $item[$key.'_off_text'] = '正常';
                        }
                        elseif ($diff < strtotime($item[$key.'_off'])-strtotime($item[$key.'_on'])- $item[$key.'_late']*60)
                        {
                            $item[$key.'_off_text'] = "<span class='text-yellow'>早退</span>";
                        }else
                        {
                            $item[$key.'_off_text'] = "<span class='text-red'>异常</span>";
                        }
                    }
                    #endregion 下班

                    $item['leave_days']     = "无";
                }

            }
            #endregion


        };
    }


    public function getSimpleArrageList($where = [], $field = true, $order, $paginate = DB_LIST_ROWS)
    {
        if (empty($where[DATA_STATUS_NAME]))
        {
            $where[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }

        return $this->modelAttendance->getList($where, $field, $order, $paginate);
    }


    /**
     * 获取班次列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];

        !empty($data['search_data']) && $where['a.username|a.number'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['admin_id']) && $where['a.id'] = $data['admin_id'];
        !empty($data['shop_id']) && $where['s.id'] = $data['shop_id'];
        //        !empty($data['start_time']) && !empty($data['end_time']) && $where["( FROM_UNIXTIME(at.create_time,'%Y-%m-%d'))"] = ['between', [$data['start_time'], $data['end_time']]];

        !empty($data['start_time']) && !empty($data['end_time']) && $where["at.create_time"] = ["between time", [$data["start_time"], $data["end_time"]." 23:59:59"]];//精确到秒

        return $where;
    }

    /**
     * @param array  $where
     * @param string $field
     * @param string $order
     */
    public function exportAttendanceList($where = [], $field = 'a.username,a.number,a.portrait_id,g.name group_name,s.name shop_name,at.*,a.id admin_id', $order = '')
    {
        $list = $this->getAttendanceList($where, $field, $order, false);

        //       $titles = "编号,名字,性别,生日,身份证,手机,住址,注册时间";
        //       $keys   = "number,username,gender,birthday,identity,mobile,address,create_time";
        //
        //       action_log('导出', '导出考勤列表');
        //
        //       export_excel($titles, $keys, $list, '考勤记录列表');
    }


}

