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
class PointLog extends AdminBase
{

    /**
     * 状态获取器
     */
    public function getCheckStatusTextAttr()
    {
        $status = [ DATA_DISABLE => "<span class='badge bg-red'>未审核</span>", DATA_NORMAL => "<span class='badge bg-green'>已审核</span>"];

        return $status[$this->data['check_status']];
    }
}
