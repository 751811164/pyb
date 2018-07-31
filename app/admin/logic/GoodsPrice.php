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
class GoodsPrice extends AdminBase
{

    /**
     * 获取信息
     */
    public function getPriceInfo($where = [], $field = true)
    {

        return $this->modelGoodsPrice->getInfo($where, $field);
    }


    /**
     * 获取列表
     */
    public function getPriceList($where = [], $field = true, $order = '', $paginate = 0,$join=[])
    {
        return $this->modelGoodsPrice->getList($where, $field, $order, $paginate,$join );
    }

    /**
     * 获取列表
     */
    public function getSimplePriceList($where = [], $field = true, $order = '', $paginate = 0,$join = [], $group = '', $limit = null)
    {

        return $this->modelGoodsPrice->getList($where, $field, $order, $paginate,$join ,$group, $limit);
    }


    /**
     * 统计数据
     */
    public function getPriceStat($where = [], $stat_type = 'count', $field = 'id')
    {
      return  $this->modelGoodsPrice->stat($where, $stat_type, $field );

    }


    /**
     * 获取某个列的数组
     */
    public function getPriceColumn($where = [], $field = '', $key = '',$limit=null)
    {

      return  $this->modelGoodsPrice->getColumn($where, $field, $key ,$limit);
    }


    //    /**
//     * 查找店铺价格,替换商品原价
//     */
//    public function shopGoodsPrice($shop_id,&$list=[]){
//        //region 查找店铺价格,替换商品原价
//        $profile_ids=[];
//        foreach ($list as $key=>$item){
//            array_push($profile_ids,$item['profile_id']) ;
//        }
//        $profile_ids= array_unique($profile_ids);
//
//
//        //店铺价格
//        $field="profile_id, cost_price ,retail_price ,wholesale_price ,delivery_price,member_price1,member_price";
//        $where_shop=['profile_id'=>['in',$profile_ids],'shop_id'=>$shop_id,DATA_STATUS_NAME=>['neq',DATA_DELETE]];
//        $priceList  = $this->logicGoodsPrice->getSimplePriceList($where_shop,$field, '',false);
//
//        foreach ($list as $key=>$item){
//            foreach ($priceList as $k=>$price){
//                $price= $price->toArray();
//                if ($item['profile_id']==$price['profile_id'])
//                {
//                    foreach ($price as $name=>$val){
//                        $list[$key][$name] = $val;
//                    }
//                }
//
//            }
//        }
//        //endregion
//    }


}
