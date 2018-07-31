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

namespace app\admin\logic;

/**
 * 逻辑
 */
class GoodsBarcodeStock extends AdminBase
{

    /**
     *  获取信息
     */
    public function getStockInfo($where = [], $field = true)
    {

        return $this->modelGoodsBarcodeStock->getInfo($where, $field);
    }


    //    /**
    //     *  获取信息
    //     */
    //    public function getStockJoinInfo($where = [], $field = '*',$join=[])
    //    {
    //        $this->modelGoodsBarcodeStock->alias('gb');
    //        if (empty($join))
    //        {
    //            $join = [
    //                [SYS_DB_PREFIX.'goods_profile gf', 'gb.profile_id = gf.id', 'LEFT'],
    //                [SYS_DB_PREFIX.'goods g', 'g.id = gf.goods_id ', 'LEFT'],
    //            ];
    //        }
    //        return $this->modelGoodsBarcodeStock->getInfo($where, $field,$join);
    //    }
    /**
     * 获取列表
     */
    public function getStockList($where = [], $field = '*', $order = '', $pagenate = 0, $join = false,$group='gbs.barcode')
    {
        $this->modelGoodsBarcodeStock->alias('gbs');
        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'shop s', 's.id = gbs.shop_id ', 'LEFT'],
                [SYS_DB_PREFIX.'goods g', 'g.id = gbs.goods_id ', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0', 'LEFT'],
                [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id=gf.id and gp.shop_id=s.id ', 'LEFT'],
                [SYS_DB_PREFIX.'storage st', 'st.barcode=gbs.barcode ', 'LEFT'],
            ];
        }
        return $this->modelGoodsBarcodeStock->getList($where, $field, $order, $pagenate, $join,$group);
    }


//    /**
//     * 导出列表
//     */
//    public function exportInventoryList($where = [], $field = '*', $order = 'i.id desc')
//    {
//
//        $list = $this->getStockList($where, $field, $order, false);
//
//        $titles = "机构,条码,商品名称,规格,单位,数量,进价,金额,售价,售价金额";
//        $keys   = "";
//
//        action_log('导出', '导出采购退货单列表');
//
//        export_excel($titles, $keys, $list, '采购退货表'.date("Y-m-d"));
//    }




    public function getWhere($data = [])
    {
        $where = [];
        !empty($data['shop_id'])&&$where['gbs.shop_id']=$data['shop_id'];
        !empty($data['category_id'])&&$where['g.category_id']=$data['category_id'];
        !empty($data['supplier_id'])&&$where['g.supplier_id']=$data['supplier_id'];
        !empty($data['search_data'])&&$where['gbs.barcode|gf.name']=['like','%'.$data['search_data'].'%'];
        !empty($data['valid_stock'])&&$where['gbs.stock_actual']=['>',0];
        return $where;
    }


}
