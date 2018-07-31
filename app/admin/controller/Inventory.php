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
class Inventory extends AdminBase
{

    /**
     * 列表
     */
    public function inventoryList()
    {
        $where = $this->logicInventory->getWhere($this->param);
        $this->assign('list', $this->logicInventory->getInventoryList($where));
        // echo(model("Inventory")->getLastSql());
        $this->assign("shopList", $shopList = $this->logicShop->getSimpleShopList([], true, '', false));

        return $this->fetch('inventory_list');
    }

    /**
     * 未盘点列表
     */
    public function uninventoryList()
    {



        $this->assign("shopList", $shopList = $this->logicShop->getSimpleShopList([], true, '', false));

        $date             = getdate();

        if (!empty($this->param['start_time']) || !empty($this->param['end_time']))
        {
            $end_time             = empty($this->param['end_time']) ? mktime(0, 0, 0, $date['mon'], $date['mday'] + 1, $date['year']): $this->param['end_time']." 23:59:59";
            $start_time           = empty($this->param['start_time']) ? 0: $this->param['start_time'];
            $where['create_time'] = ['between time', [$start_time, $end_time]];
        }
        else
        {
            //默认查询一个月内的单据
            $start_time           = mktime(0, 0, 0, $date['mon'], 1, $date['year']);
            $where['create_time'] = ['> time', $start_time];
        }


        if (!empty($this->param['shop_id']))
        {
            $shop_id= $this->param['shop_id'];

            $where['shop_id'] =  $shop_id;
            $where[DATA_STATUS_NAME] = ['neq',DATA_DELETE];
            $inventory_ids = $this->logicInventory->getInventoryColumn($where, 'id');//查询机构已盘点单据

            $barcodes = $this->logicInventoryLog->getLogColumn(['inventory_id' => ['in', $inventory_ids]], "barcode");//查询已经盘点的条码商品

            $join  = [
                [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0'],
                [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id  ', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile pgf', 'pgf.goods_id = g.id and gf.kind=1', 'LEFT'],
                [SYS_DB_PREFIX.'goods_price pgp', 'pgp.profile_id = gf.id ', 'LEFT'],
                [SYS_DB_PREFIX.'category c', 'g.category_id = c.id', 'LEFT'],
                [SYS_DB_PREFIX.'goods_barcode gb', 'gb.profile_id = gf.id and gb.status=1', 'LEFT'],
                [SYS_DB_PREFIX.'goods_barcode_stock gbs', 'gbs.barcode = gb.barcode and gbs.shop_id='.$shop_id, 'RIGHT'],

            ];
            $field = "c.name category_name,gb.barcode barcode,gb.barcode barcode1,g.id goods_id,gf.name,gf.specification,gf.unit,
            gp.profile_id,gp.cost_price ,gp.retail_price ,gp.wholesale_price ,gp.delivery_price,gp.member_price1 ,gbs.stock_actual,gbs.id
            ";
            $list  = $this->logicGoods->getSimpleGoodsList(['gbs.barcode' => ['not in', $barcodes,'gb.shop_id'=>$shop_id]], $field, 'g.update_time desc', false, $join);
        }
        else
        {
            $list=[];
        }


        //echo model("Goods")->getLastSql();
        $this->assign('list', $list);

        return $this->fetch('uninventory_list');
    }


    /**
     * 删除未审核的采购入库单
     */
    public function inventoryDel()
    {
        $this->jump($this->logicInventory->inventoryDel($this->param));
    }


    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        if ($this->param["status"] == -1)
        {

            //$this->jump($this->logicAdminBase->setStatus('Inventory', $this->param));
        }
        else
        {
            $this->jump([RESULT_ERROR, "请在单据详情里面执行审核操作"]);
        }
    }

    /**
     * 导出
     */
    public function exportinventoryList()
    {

        $where = $this->logicInventory->getWhere($this->param);

        $this->logicInventory->exportInventoryList($where);
    }

}
