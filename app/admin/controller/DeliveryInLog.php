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
class DeliveryInLog extends AdminBase
{
    /**
     * 列表
     */
    public function logList()
    {
        $info=[DATA_STATUS_NAME=>DATA_DELETE]; $list=[];

        if (!empty($this->param["in_id"]))
        {

            //单信息
            $info=$this->logicDeliveryIn->getInJoinInfo(["di.id"=>$this->param["in_id"]],"di.*,add.username admin_add_name,check.username admin_check_name,di.sn,do.sn out_sn ");
            $where = $this->logicDeliveryInLog->getWhere($this->param);
            //列表明细
            if ($info['status']==1)
            {
                $join = [
                    [SYS_DB_PREFIX.'delivery_in di', 'di.id = dil.in_id', 'LEFT'],
                    [SYS_DB_PREFIX.'goods g', 'g.id = dil.goods_id', 'LEFT'],
                    [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],
                ];
                $field="dil.*,gf.barcode1,gf.name,gf.unit,gf.specification,g.size,g.color";
            }
            else
            {              

                $join = [
                    [SYS_DB_PREFIX.'delivery_in di', 'di.id = dil.in_id', 'LEFT'],
                    [SYS_DB_PREFIX.'goods g', 'g.id = dil.goods_id', 'LEFT'],
                    [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],
                    [SYS_DB_PREFIX.'goods_barcode_stock cs', 'dil.barcode=cs.barcode and di.out_shop_id=cs.shop_id', 'LEFT'],
                    [SYS_DB_PREFIX.'goods_barcode_stock ts', 'dil.barcode=ts.barcode and di.in_shop_id=ts.shop_id', 'LEFT'],
                ];
                $field="dil.*,gf.barcode1,gf.name,gf.unit,gf.specification,g.size,g.color,cs.stock_actual current_stock,ts.stock_actual target_stock";

            }

            $list=$this->logicDeliveryInLog->getLogList($where,$field,'dil.id',false,$join);

        }
        else
        {
            //查询当前人员对应店铺
            $shopPermission= $this->logicAdminBase->authShopPermission(ADMIN_ID,'s.id');
            $info['apply_shop_id']=empty($shopPermission)?1:$shopPermission['id'];

        }
        $this->assign('list', $list);
        $this->assign("info",$info);

        $this->assign('typeList',$typeList= $this->logicShopType->getTypeList([DATA_STATUS_NAME=>DATA_NORMAL], 'id,name', '', false));
        $this->assign('shopList',$shopList= $this->logicShop->getSimpleShopList([DATA_STATUS_NAME=>DATA_NORMAL], 'id,name,type_id', '', false));
        $this->assign("supplierList", $supplierList = $this->logicSupplier->getsupplierList([], true, '', false));
        return $this->fetch('log_list');
    }

    /**
     * 选择店铺价商品
     * 按条码显示 列表出现 一品多码
     */
    public function goodsChoose(){
        $this->view->engine->layout(false);
        //分类
        $where_category["level"] =config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));

        $where = $this->logicDeliveryInLog->getGoodsChooseWhere($this->param);
        $in_shop_id= $this->param['in_shop_id'];
        $out_shop_id= $this->param['out_shop_id'];

        $join = [
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0'],
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id  ', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile pgf', 'pgf.goods_id = g.id and gf.kind=1', 'LEFT'],
            [SYS_DB_PREFIX.'goods_price pgp', 'pgp.profile_id = gf.id ', 'LEFT'],
            [SYS_DB_PREFIX.'category c', 'g.category_id = c.id', 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode gb', 'gb.profile_id = gf.id and gb.status=1', 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode_stock cs', 'cs.barcode = gb.barcode and cs.shop_id='.$in_shop_id, 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode_stock ts', 'ts.barcode = gb.barcode and ts.shop_id='.$out_shop_id, 'LEFT'],

        ];
        $field="c.name category_name,gb.barcode barcode,gb.barcode barcode1,g.id goods_id,gf.name,gf.specification,gf.unit,
            gp.profile_id,gp.cost_price ,gp.retail_price ,gp.wholesale_price ,gp.delivery_price,gp.member_price1 ,1 num,cs.stock_actual current_stock,ts.stock_actual target_stock
            ";
        $list  = $this->logicGoods->getSimpleGoodsList($where,$field, 'g.update_time desc',0,$join);
        //echo model("Goods")->getLastSql();
        $this->assign('list', $list);
        return $this->fetch('goods_choose');
    }


    /**
     * 选择配送单
     */
    public function outChoose(){
        $this->view->engine->layout(false);

        $where["do.in_shop_id"] =$this->param['in_shop_id'];
        $where["do.out_shop_id"] =$this->param['out_shop_id'];
        $where["do.".DATA_STATUS_NAME] =DATA_NORMAL;
        $where["do.use_status"] =DATA_DISABLE;

        $list= $this->logicDeliveryOut->getOutList($where);
//        echo model('goods')->getLastSql();
        $this->assign('list',$list);
        return $this->fetch('out_choose');
    }


    /**
     * 获取入库单明细
     */
    public function getDeliveryOutLogList(){

        $join = [
            [SYS_DB_PREFIX.'delivery_out do', 'do.id = dol.out_id', 'LEFT'],
            [SYS_DB_PREFIX.'goods g', 'g.id = dol.goods_id', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode_stock cs', 'dol.barcode=cs.barcode and do.in_shop_id=cs.shop_id', 'LEFT'],//角色交换 入库者当前库存
            [SYS_DB_PREFIX.'goods_barcode_stock ts', 'dol.barcode=ts.barcode and do.out_shop_id=ts.shop_id', 'LEFT'],//角色交换   配送者目标库存
        ];
        $field="dol.barcode,dol.goods_id,dol.num,dol.delivery_price,dol.remark,gf.barcode1,gf.name,gf.unit,gf.specification,g.size,g.color,cs.stock_actual current_stock,ts.stock_actual target_stock";

        $list=$this->logicDeliveryOutLog->getLogList(
            ["out_id"=>$this->param["out_id"],"dol.".DATA_STATUS_NAME=>DATA_NORMAL],
            $field,'dol.id',false,$join);
        return json($list);
    }


    /**
     * 编辑
     */
    public function logEdit()
    {

        IS_POST && $this->jump($this->logicDeliveryInLog->logEdit($this->param));

    }



    /**
     * 审核
     */
    public function checking(){
        $this->jump($this->logicDeliveryInLog->checking( $this->param));
    }



    /**
     * 删除
     */
    public function logDel()
    {
        //审核过的商品不能删除
        $this->jump($this->logicDeliveryInLog->logDel(['id' => ["in",trim($this->param["ids"] ,',')],'in_id'=>$this->param["in_id"] ]));
    }



    /**
     * 导出
     */
    public function exportLogList()
    {

        $where = $this->logicDeliveryInLog->getWhere($this->param);

        $this->logicDeliveryInLog->exportLogList($where);
    }



}
