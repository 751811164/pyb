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
 * 会员控制器
 */
class Admin extends AdminBase
{

    /**
     * 会员授权
     * @deprecated 暂不用
     */
    public function adminAuth()
    {

        IS_POST && $this->jump($this->logicAdmin->addToGroup($this->param));

        // 所有的权限组
        $group_list = $this->logicAuthGroup->getAuthGroupList(['admin_id' => ADMIN_ID]);

        // 会员当前权限组
        $admin_group_list = $this->logicAuthGroupAccess->getAdminGroupInfo($this->param['id']);

        // 选择权限组
        $list = $this->logicAuthGroup->selectAuthGroupList($group_list, $admin_group_list);

        $this->assign('list', $list);

        $this->assign('id', $this->param['id']);

        return $this->fetch('admin_auth');
    }

    /**
     * 会员列表
     */
    public function adminList()
    {

        $where = $this->logicAdmin->getWhere($this->param);
        array_key_exists('sid', $this->param) && $where['s.id'] = $this->param['sid'];
        $this->assign('list', $this->logicAdmin->getAdminList($where));

        $shopTree = $this->logicShop->getShopTree();
        $this->assign('shopTree', $shopTree);

        return $this->fetch('admin_list');
    }

    /**
     * 会员导出
     * @Todo
     */
    public function exportAdminList()
    {

        $where = $this->logicAdmin->getWhere($this->param);

        $this->logicAdmin->exportAdminList($where);
    }

    /**
     * 会员添加
     */
    public function adminAdd()
    {
        IS_POST && $this->jump($this->logicAdmin->adminAdd($this->param));

        $this->common();
        return $this->fetch('edit_modal');
    }

    /**
     * 会员编辑
     */
    public function adminEdit()
    {
        IS_POST && $this->jump($this->logicAdmin->adminEdit($this->param));

        $this->common();
        return $this->fetch('edit_modal');
    }

    public function common()
    {
        $info = [];
        if (!empty($this->param['id']))
        {
            $join = [
                [SYS_DB_PREFIX.'auth_group_access ga', 'ga.admin_id = a.id', 'LEFT'],
                [SYS_DB_PREFIX.'auth_group g', 'g.id = ga.group_id', 'LEFT'],
                [SYS_DB_PREFIX.'admin_permission ap', 'ap.admin_id = a.id', 'LEFT'],
                [SYS_DB_PREFIX.'shop s', 's.id = g.shop_id', 'LEFT'],
            ];
            $where['a.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
            $info = $this->logicAdmin->getAdminJoinInfo($where,'a.*,ap.*,group_id,shop_id,type_id',$join);

        }
        elseif (!empty($this->param['sid']))
        {
            $info = $this->logicShop->getShopInfo(['id' => $this->param['sid']], 'id shop_id,type_id');
        }

        $this->assign('info', $info);

    }

    /**
     * 快速设置
     */
    public function fastSet()
    {
        IS_POST && $this->jump($this->logicAdmin->fastSet($this->param));
    }


    /**
     * 创建编号
     *
     */
    public function createCode()
    {

        return $this->logicAdmin->createCode();
    }


    /**
     * @todo
     * 会员删除
     */
    public function adminDel($id = 0)
    {

        return $this->jump($this->logicAdmin->adminDel(['id' => $id]));
    }


    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        return $this->jump($this->logicAdmin->setStatus('Admin', $this->param));
    }


    /**
     * 数据状态设置
     */
    public function setPermissionStatus()
    {
        return $this->jump($this->logicAdminPermission->setStatus('AdminPermission', $this->param));
    }

}
