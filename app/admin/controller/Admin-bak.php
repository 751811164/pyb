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
        
        $this->assign('list', $this->logicAdmin->getAdminList($where));
        
        return $this->fetch('admin_list');
    }
    
    /**
     * 会员导出
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
        
        return $this->fetch('admin_add');
    }
    
    /**
     * 会员编辑
     */
    public function adminEdit()
    {
        
        IS_POST && $this->jump($this->logicAdmin->adminEdit($this->param));
        
        $info = $this->logicAdmin->getAdminInfo(['id' => $this->param['id']]);
        
        $this->assign('info', $info);
        
        return $this->fetch('admin_edit');
    }
    
    /**
     * 会员删除
     */
    public function adminDel($id = 0)
    {
        
        return $this->jump($this->logicAdmin->adminDel(['id' => $id]));
    }
}
