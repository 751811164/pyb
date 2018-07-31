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
     * @desc 店铺列表
     */
    public function shopList()
    {
        $where = $this->logicshop->getWhere($this->param);
        $this->assign('list', $this->logicShop->getShopList($where,"s.*,st.name as type_name"));
        //店铺类型
       $this->assign("typelist",$this->logicShopType->getTypeList([], true, '', 100));
       $this->assign("js",['map'=>1]);
        return $this->fetch('shop_list');
    }



    /**
     * @desc 编辑
     */
    public function shopEdit()
    {
        IS_POST && $this->jump($this->logicShop->shopSave($this->param));

        //店铺类型
        $typeList = $this->logicShopType->getTypeList([], true, '', 100);
        $this->assign("typelist",$typeList);

        $info =!empty($this->param['id'])? $this->logicShop->getEditModal(['id' => $this->param['id']]):[];
        $this->assign('info', $info);

        return $this->fetch('edit_modal');
    }



    /**
     * 店铺添加
     */
    public function shopAdd()
    {

        IS_POST && $this->jump($this->logicShop->shopSave($this->param));
        //店铺类型
        $typeList = $this->logicShopType->getTypeList([], true, '', 100);
        $this->assign("typelist",$typeList);
        return $this->fetch('edit_modal');
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
     * 审核设置
     */
    public function setCheckStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('Shop', $this->param,'check_status',"审核操作"));
    }

    /**
     * @deprecated 不用
     * 地图选点
     */
    public function shopPoint()
    {
        //不加载模板
        $this->view->engine->layout(false);
        return $this->fetch("shop_point");
    }

}
