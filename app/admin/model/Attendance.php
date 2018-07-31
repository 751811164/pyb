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
 * 模型
 */
class Attendance extends AdminBase
{


    //状态获取器
    public function getCheckStatusTextAttr()
    {
        $status = [ DATA_NO => "<span class='badge bg-yellow'>未审核</span>", DATA_YES => "已审核"];
        return $status[$this->data["check_status"]];
    }

}
