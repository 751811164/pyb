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
 * 权限控制器
 */
class Auth extends AdminBase
{
    
    /**
     * 权限组列表
     */
    public function groupList()
    {
        $where = $this->logicAuthGroup->getWhere($this->param);
        //$where['admin_id'] = ADMIN_ID;
        $info = $this->logicAuthGroup->getGroupTree();
        $this->assign('info', $info);
        if(array_key_exists('id',$this->param)&&$this->param['id']>=0){
            // 获取未被过滤的菜单树
            $menu_tree = $this->logicAdminBase->getListTree($this->authMenuList);
            // 菜单转换为多选视图，支持无限级
            $menu_view = $this->logicMenu->menuToCheckboxView($menu_tree);
            $this->assign('list', $menu_view);

        }
        return $this->fetch('group_list');
    }

    /**
     * 创建编号
     *
    */
    public function createCode(){

        return $this->logicAuthGroup->createCode();
    }


    /**
     * 添加组
     */
    public function groupAdd(){
        IS_POST && $this->jump($this->logicAuthGroup->groupAdd($this->param));
    }



    /**
     * 菜单授权
     */
    public function menuAuth()
    {
        IS_POST && $this->jump($this->logicAuthGroup->setGroupRules($this->param));
        // 获取未被过滤的菜单树
        $menu_tree = $this->logicAdminBase->getListTree($this->authMenuList);
        // 菜单转换为多选视图，支持无限级
        $menu_view = $this->logicMenu->menuToCheckboxView($menu_tree);
        $this->assign('list', $menu_view);

        $this->assign('id', $this->param['id']);
        return $this->fetch('menu_auth');
    }


    /**
     * 快速设置
     */
    public function fastSet(){
        IS_POST && $this->jump($this->logicAuthGroup->fastSet($this->param));
    }






}
