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
class PurchaseBillLog extends AdminBase
{
    /**
     * 列表
     */
    public function logList()
    {
        $bilInfo=[DATA_STATUS_NAME=>DATA_DELETE]; $list=[];
        //采购向导
        if (IS_POST)
        {
            $where_log = $this->logicPurchaseBillLog->getGoodsChooseWhere($this->param);
            $where_log["g.id"] = ["in", $this->param['goods_id']];

            $join = [
                [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0'],
                [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id ', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile pgf', 'pgf.goods_id = g.id and gf.kind=1', 'LEFT'],
                [SYS_DB_PREFIX.'goods_price pgp', 'pgp.profile_id = gf.id ', 'LEFT'],
                [SYS_DB_PREFIX.'category c', 'g.category_id = c.id', 'LEFT'],
                [SYS_DB_PREFIX.'goods_barcode gb', 'gb.profile_id = gf.id and gb.status=1', 'LEFT'],
            ];

            $field="c.name category_name,gb.id barcode_id,gb.barcode barcode,gb.barcode barcode1,g.id goods_id,gf.name,gf.id profile_id,gf.specification,gf.unit,
            gp.cost_price ,gp.retail_price ,gp.wholesale_price ,gp.delivery_price,gp.member_price1  ,1 num";

            $list  = $this->logicGoods->getSimpleGoodsList($where_log,$field, 'g.update_time desc',false,$join);

//            $profile_ids=[];
            foreach ($list as $key=>$item){
//                array_push($profile_ids,$item['profile_id']) ;
                foreach ($this->param['goods_id'] as $k=> $val){
                    if ($val==$item['goods_id'])
                    {
                        $list[$key]['num']=$this->param['num'][$k];
                    }
                }
            }
            //店铺价格
//            $this->logicGoodsPrice->shopGoodsPrice($this->param['shop_id'],$list);

        }

        if (!empty($this->param["bill_id"]))
        {

            //单据信息
            $bilInfo=$this->logicPurchaseBill->getBillJoinInfo(["pb.id"=>$this->param["bill_id"]],"pb.*,add.username admin_add_name,check.username admin_check_name");

            $where = $this->logicPurchaseBillLog->getWhere($this->param);
            //采购列表
            $list=$this->logicPurchaseBillLog->getLogList($where,"pl.*,gf.barcode1,gf.name,gf.unit,gf.specification",'pl.id asc',false);
//            $list= collection($list)->toArray();

        }
        $this->assign('list', $list);
        $this->assign("billInfo",$bilInfo);

        $this->assign('typeList',$typeList= $this->logicShopType->getTypeList([DATA_STATUS_NAME=>DATA_NORMAL], 'id,name', '', false));
        $this->assign('shopList',$shopList= $this->logicShop->getSimpleShopList([DATA_STATUS_NAME=>DATA_NORMAL], 'id,name,type_id', '', false));
        $this->assign("supplierList", $supplierList = $this->logicSupplier->getsupplierList([], true, '', false));
        return $this->fetch('log_list');
    }

    /**
     * 选择采购商品
     */
    public function goodsChoose(){
        $this->view->engine->layout(false);
        //分类
//        $pids                 = $this->logicCategory->getCategoryColumn([DATA_STATUS_NAME => DATA_NORMAL], "pid");
//        $where_category["id"] = ["not in", array_unique($pids)];
        $where_category["level"] =config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));

        $where = $this->logicPurchaseBillLog->getGoodsChooseWhere($this->param);


        //商品原价
        $join = [
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0'],
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id ', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile pgf', 'pgf.goods_id = g.id and gf.kind=1', 'LEFT'],
            [SYS_DB_PREFIX.'goods_price pgp', 'pgp.profile_id = gf.id ', 'LEFT'],
            [SYS_DB_PREFIX.'category c', 'g.category_id = c.id', 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode gb', 'gb.profile_id = gf.id and gb.status=1', 'LEFT'],

        ];
        $field="c.name category_name,gb.id barcode_id,gb.barcode barcode,gb.barcode barcode1,g.id goods_id,gf.name,gf.specification,gf.unit,
           gp.profile_id, gp.cost_price ,gp.retail_price ,gp.wholesale_price ,gp.delivery_price,gp.member_price1 ,1 num
            ";

        $list  = $this->logicGoods->getSimpleGoodsList($where,$field, 'g.update_time desc',0,$join);

        //店铺价格
//        $this->logicGoodsPrice->shopGoodsPrice($this->param['shop_id'],$list);

       // echo model("Goods")->getLastSql();
        $this->assign('list', $list);
        return $this->fetch('goods_choose');
    }



    /**
     * 编辑
     */
    public function logEdit()
    {

        IS_POST && $this->jump($this->logicPurchaseBillLog->logEdit($this->param));

    }

    /**
     * @throws \Exception
     */
    public function logCommon()
    {
        IS_POST && $this->jump($this->logicPurchaseBillLog->logEdit($this->param));
//        $this->assign("shopList",$shopList=$this->logicShop->getSimpleShopList([],true,'',false));

    }

    /**
     * 审核
     */
    public function checking(){
        $this->jump($this->logicPurchaseBillLog->checking( $this->param));
    }

    /**
     * 反审核
     */
    public function unchecking(){
        $this->jump($this->logicPurchaseBillLog->unchecking( $this->param));
    }


    /**
     * 删除
     */
    public function logDel()
    {
        //审核过的商品不能删除
        $this->jump($this->logicPurchaseBillLog->logDel(['id' => ["in",trim($this->param["ids"] ,',')],'bill_id'=>$this->param["bill_id"] ]));
    }


    /**
     * 导出
     */
    public function exportLogList()
    {

        $where = $this->logicPurchaseBillLog->getWhere($this->param);

        $this->logicPurchaseBillLog->exportLogList($where);
    }



}
