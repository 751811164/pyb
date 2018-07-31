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
 * 店铺权限控制器
 */
class ShopPermission extends AdminBase
{

    /**
     * 机构设置
     * @return mixed
     */
    public function shopTree()
    {


        IS_POST&&$this->jump($this->logicShopPermission->permissionsEdit($this->param));

        //        菜单树
        $where  = [];
        $result = $this->logicShopPermission->getShopTree($where);
        $this->assign('list', $result['typeList']);
        $this->assign('shopList', $result['shopList']);
        $info = [];


        //分类
        //        $pids        = $this->logicCategory->getCategoryColumn([DATA_STATUS_NAME => DATA_NORMAL], "pid");
        //        $where["id"] = ["not in", array_unique($pids)];
        $where_category["level"] =config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));

        //权限信息
        if (!empty($this->param['id']))
        {
            $info = $this->logicShopPermission->getPermissionInfo(['shop_id' => $this->param['id']]);
        }
        $this->assign('info', gettype($info) == 'object' ? $info->toArray(): []);

        return $this->fetch('shop_tree');
    }

    /**
     * 权限设置
     * @return mixed
     * @throws \Exception
     */
    public function permissions()
    {
        IS_POST&&$this->jump($this->logicShopPermission->permissionsEdit($this->param));


    }

    /**
     * 快捷设置
     */
    public function fastSet(){

        IS_POST&&$this->jump($this->logicShopPermission->fastSet($this->param));

    }



}
