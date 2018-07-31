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
class ShopPriceLog extends AdminBase
{
    /**
     * 列表
     */
    public function logList()
    {
        $bilInfo=[DATA_STATUS_NAME=>DATA_DELETE]; $list=[];
        if (!empty($this->param["bill_id"]))
        {

            //单据信息
            $bilInfo=$this->logicShopPriceBill->getBillJoinInfo(["pb.id"=>$this->param["bill_id"]],"pb.*,add.username admin_add_name,check.username admin_check_name");
            //调价列表
            $list=$this->logicShopPriceLog->getLogList(["bill_id"=>$this->param["bill_id"],"pl.".DATA_STATUS_NAME=>DATA_NORMAL],"pl.*,gf.barcode1,gf.name,gf.unit,gf.specification",'',false);
            $list= collection($list)->toArray();

        }
        $this->assign('list', $list);
        $this->assign("billInfo",$bilInfo);
        $where[DATA_STATUS_NAME]=DATA_NORMAL;
        //会员等级
        $this->assign('memberGradeList',$memberGradeList= $this->logicMemberGrade->getSimpleMemberGradeList($where, 'id,name', '', false));
        $this->assign('typeList',$typeList= $this->logicShopType->getTypeList($where, 'id,name', '', false));
        $this->assign('shopList',$shopList= $this->logicShop->getSimpleShopList($where, 'id,name,type_id', '', false));

        return $this->fetch('log_list');
    }

    /**
     * 选择调价商品
     */
    public function goodsChoose(){
        $this->view->engine->layout(false);
        //分类

        $where_category["level"] = config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));

        $where = $this->logicShopPriceLog->getGoodsChooseWhere($this->param);

        $join = [
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0'],
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id ', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile pgf', 'pgf.goods_id = g.id and gf.kind=1', 'LEFT'],
            [SYS_DB_PREFIX.'goods_price pgp', 'pgp.profile_id = gf.id ', 'LEFT'],
            [SYS_DB_PREFIX.'category c', 'g.category_id = c.id', 'LEFT'],
        ];
        $field="c.name category_name,gf.barcode1,g.id goods_id,gf.name,gf.specification,gf.unit,gp.*,
            gp.cost_price single_cost_price,gp.retail_price single_retail_price,gp.wholesale_price single_wholesale_price,gp.delivery_price single_delivery_price,gp.member_price single_member_price,
            pgp.cost_price pack_cost_price,pgp.retail_price pack_retail_price,pgp.wholesale_price pack_wholesale_price,pgp.delivery_price  pack_delivery_price,pgp.member_price pack_member_price
            ";

        $list  = $this->logicGoods->getSimpleGoodsList($where,$field, 'g.update_time desc',0,$join);
        //echo model("Goods")->getLastSql();
        $this->assign('list', $list);
        return $this->fetch('goods_choose');
    }



    /**
     * 编辑
     */
    public function logEdit()
    {

        IS_POST && $this->jump($this->logicShopPriceLog->logEdit($this->param));

    }

    /**
     * @throws \Exception
     */
    public function logCommon()
    {
        IS_POST && $this->jump($this->logicShopPriceLog->logEdit($this->param));
//        $this->assign("shopList",$shopList=$this->logicShop->getSimpleShopList([],true,'',false));

    }

    /**
     * 审核
     */
    public function checking(){
//        1:
        $this->jump($this->logicShopPriceLog->checking( $this->param));
    }

    /**
     * 删除
     */
    public function logDel()
    {
        //审核过的商品不能删除
        $this->jump($this->logicShopPriceLog->logDel(['id' => ["in",trim($this->param["ids"] ,',')],'bill_id'=>$this->param["bill_id"] ]));
    }






}
