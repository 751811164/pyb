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
class GoodsBarcode extends AdminBase
{

    /**
     *  获取信息
     */
    public function getBarcodeInfo($where = [], $field = true)
    {

        return $this->modelGoodsBarcode->getInfo($where, $field);
    }


    /**
     *  获取信息
     */
    public function getBarcodeJoinInfo($where = [], $field = "*",$join=[])
    {
        $this->modelGoodsBarcode->alias('gb');
        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'goods_profile gf', 'gb.profile_id = gf.id', 'LEFT'],
                [SYS_DB_PREFIX.'goods g', 'g.id = gf.goods_id ', 'LEFT'],
            ];
        }
        return $this->modelGoodsBarcode->getInfo($where, $field,$join);
    }
    /**
     * 获取商品列表
     */
    public function getBarcodeList($where = [], $field = true, $order = '', $pagenate = DB_LIST_ROWS,$join=false)
    {



        return $this->modelGoodsBarcode->getList($where, $field, $order, $pagenate,$join );
        //        return $this->modelGoods->getList($where, $field, $order, DB_LIST_ROWS, $join);
    }


    /**
     *生成最大id+1的编号
     * @param $data
     * @param int $len 编号长度
     * @return string 编号
     */
    public function createCode($data = [] ,$len = 7)
    {
        if (empty($data["category_id"]))
        {
            $category['number']=20000;
        }
        else
        {
            $category =$this->logicCategory->getCategoryInfo(["id"=>$data["category_id"]]);
        }

        $model_str = LAYER_MODEL_NAME.$this->name;
        $obj       = $this->$model_str;
        $table = config('database.prefix').humpToLine(lcfirst($this->name));
        $res = $obj->query("show table status where `name`='".$table."'");
        $maxId = (!empty($res)) ? $res[0]['Auto_increment']: 1;
        $code  = $category['number'].$data["kind"].sprintf("%0{$len}s", $maxId);
        $maxId++;
        $obj->execute("alter table {$table} AUTO_INCREMENT={$maxId};");
        return $code;
    }
}
