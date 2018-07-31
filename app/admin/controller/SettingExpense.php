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
class SettingExpense extends AdminBase
{


    /**
     * 添加与编辑通用方法
     */
    public function common($info)
    {
        //        $list      = [];
        $list = collection($this->logicExpense->getExpenseList([], true, '', false))->toArray();

        $where_admin["id"]       = ['<>', SYS_ADMINISTRATOR_ID];
        $where_admin["group_id"] = $this->param['gid'];
        $join                    = [
            [SYS_DB_PREFIX.'auth_group_access ga', 'ga.admin_id = a.id', 'LEFT'],
        ];

        $adminList = collection($this->logicAdmin->getSimpleAdminList($where_admin, "a.id,a.username", '', false, $join))->toArray();

        $this->assign('adminList', $adminList);
        $this->assign('list', $list);
    }



}
