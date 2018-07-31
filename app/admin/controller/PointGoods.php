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
 * 积分商品控制器
 */
class PointGoods extends AdminBase
{


    /**
     * 积分商品列表
     */
    public function pointGoodsList()
    {
        $where = $this->logicPointGoods->getWhere($this->param);
        $this->assign('list', $list = $this->logicPointGoods->getPointGoodsList($where, 'c.name category_name,b.name brand_name,gb.barcode,gp.retail_price,gf.name,add.username admin_add_name,pg.*', 'pg.status'));

        return $this->fetch('point_Goods_list');
    }


//    /**
//     * 积分商品添加
//     */
//    public function pointGoodsAdd()
//    {
//
//        if (IS_POST)
//        {
//            $this->jump($this->logicPointGoods->PointGoodsEdit($this->param));
//        }
//
//        return $this->fetch('edit_modal');
//    }

    /**
     * 选择店铺价商品
     * 按条码显示 列表出现 一品多码
     */
    public function goodsChoose(){
        $this->view->engine->layout(false);
        //分类
        $where_category["level"] =config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));

        $where = $this->logicPurchaseBackLog->getGoodsChooseWhere($this->param);
        $where['']=['not exists','select id from '.SYS_DB_PREFIX.'point_goods where gb.id = barcode_id and status<>-1'];
        $join = [
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0'],
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id ', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile pgf', 'pgf.goods_id = g.id and gf.kind=1', 'LEFT'],
            [SYS_DB_PREFIX.'goods_price pgp', 'pgp.profile_id = gf.id ', 'LEFT'],
            [SYS_DB_PREFIX.'category c', 'g.category_id = c.id', 'LEFT'],
            [SYS_DB_PREFIX.'brand b', 'g.brand_id = b.id and b.status=1', 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode gb', 'gb.profile_id = gf.id and gb.status=1', 'LEFT'],

        ];
        $field="c.name category_name,b.name brand_name,gb.id barcode_id,gb.barcode ,gb.barcode barcode1,g.id goods_id,gf.name,gf.specification,gf.unit,
            gp.profile_id,gp.cost_price ,gp.retail_price ,gp.wholesale_price ,gp.delivery_price,gp.member_price1
            ";

        $list  = $this->logicGoods->getSimpleGoodsList($where,$field, 'g.update_time desc',0,$join);

//        echo model("Goods")->getLastSql();
        $this->assign('list', $list);
        return $this->fetch('goods_choose');
    }

    /**
     * 设置积分兑换数量
     */
    public function setPointNum(){
        if (IS_POST)
        {
            $this->jump($this->logicPointGoods->setPointNum($this->param));
        }
    }



    /**
     * 积分商品保存
     */
    public function pointGoodsEdit()
    {
        if (IS_POST)
        {
            $this->jump($this->logicPointGoods->PointGoodsEdit($this->param));
        }

    }

    /**
     * 积分商品删除
     */
    public function pointGoodsDel()
    {

        $this->jump($this->logicPointGoods->PointGoodsDel($this->param));
    }


    /**
     * 审核状态设置
     */
    public function checking()
    {

        $this->jump($this->logicPointGoods->checking($this->param));
    }

    public function setStatus()
    {
        $this->jump($this->logicAdminBase->setStatus('PointGoods', $this->param));
    }

}
