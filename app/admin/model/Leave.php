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

namespace app\admin\model;

/**
 * 请假管理模型
 */
class Leave extends AdminBase
{
    protected $type=[
        'start_time'=>'timestamp:Y-m-d',
        'end_time'=>'timestamp:Y-m-d',
    ];
    //表单无days字段,要使用自动完成,有days字段,则$auto可省略
//    任何涉及到字段的操作(即使只操作别的字段)都会执行自动完成方法
//    protected $auto = ['days'];

    public function setDaysAttr($val, $data)
    {
            $start = $data['start_time'];
            $end   = $data['end_time'];
            $diff  = date_diff(date_create($end), date_create($start));
            return  $diff->days + 1;//天数加减,起始和截止日都算
    }


    //状态获取器
    public function getCheckStatusTextAttr()
    {
        $status = [ DATA_NO => "<span class='badge bg-yellow'>未审核</span>", DATA_YES => "已审核"];
        return $status[$this->data["check_status"]];
    }

}
