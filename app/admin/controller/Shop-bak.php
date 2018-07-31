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
 * 店铺控制器
 */
class Shop extends AdminBase
{

    /**
     *  店铺列表
     */
    public function shopList()
    {
        $where = $this->logicshop->getWhere($this->param);
        $this->assign('list', $this->logicShop->getShopList($where));
        return $this->fetch('shop_list');
    }

    /**
     * 店铺添加
     */
    public function shopAdd()
    {

        IS_POST && $this->jump($this->logicShop->shopEdit($this->param));

        return $this->fetch('shop_edit');
    }

    /**
     * 店铺编辑
     */
    public function shopEdit()
    {

        IS_POST && $this->jump($this->logicShop->shopEdit($this->param));

        $info = $this->logicShop->getShopInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('shop_edit');
    }

    /**
     * 店铺删除
     */
    public function shopDel($id = 0)
    {

        $this->jump($this->logicShop->shopDel(['id' => $id]));
    }

    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('Shop', $this->param));
    }

    /**
     * 地图选点
     */
    public function shopPoint()
    {
        //不加载模板
        $this->view->engine->layout(false);
        return $this->fetch("shop_point");
    }

}
