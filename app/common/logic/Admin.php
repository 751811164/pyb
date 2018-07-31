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

namespace app\common\logic;

/**
 * 会员逻辑
 */
class Admin extends LogicBase
{
    
    /**
     * 获取会员信息
     */
    public function getAdminInfo($where = [], $field = true)
    {
        
        return $this->modelAdmin->getInfo($where, $field);
    }
}
