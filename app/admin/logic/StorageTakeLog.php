<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | ReStorageitory | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\logic;

/**
 * 逻辑
 */
class StorageTakeLog extends AdminBase
{

    /**
     * 获取列表
     */
    public function getLogList($where = [], $field = true, $order = '',$pagenate=0,$join=[])
    {
        return $this->modelStorageTakeLog->getList($where, $field, $order, $pagenate, $join);
    }


}
