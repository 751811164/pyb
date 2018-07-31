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
class ShopPriceBill extends AdminBase
{

    /**
     * 信息
     */
    public function getBillInfo($where = [], $field = true)
    {
        return $this->modelShopPriceBill->getInfo($where, $field);
    }

    /**
     * 信息
     */
    public function getBillJoinInfo($where = [], $field = "*",$join=[])
    {
        $this->modelShopPriceBill->alias("pb");
        if (empty($where["pb.".DATA_STATUS_NAME])&&empty($where[DATA_STATUS_NAME]))
        {
            $where["pb.".DATA_STATUS_NAME]=["neq",DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
//                [SYS_DB_PREFIX.'shop s', 's.id = pb.shop_id', 'LEFT'],
                [SYS_DB_PREFIX.'admin add', 'add.id = pb.admin_add_id', 'LEFT'],
                [SYS_DB_PREFIX.'admin check', 'check.id = pb.admin_check_id', 'LEFT'],
            ];
        }

        return $this->modelShopPriceBill->getInfo($where, $field,$join);
    }

    /**
     * 列表
     */
    public function getBillList($where = [], $field = "pb.*,a.username", $order = 'id desc', $paginate = 0)
    {
        $this->modelShopPriceBill->alias("pb");

        $join = [
//            [SYS_DB_PREFIX.'shop s', 's.id = pb.shop_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin a', 'a.id = pb.admin_add_id', 'LEFT'],
        ];
        return $this->modelShopPriceBill->getList($where, $field, $order, $paginate,$join);
    }

    /**
     * 查询条件
     */
    public function getWhere($data=[]){

        $where=function($query)use($data){
            if (!empty($data["create_time"]))
            {
                $query->whereTime('pb.create_time', $data["create_time"]);
            }
            else if (!empty($data['start_time'])||!empty($data['end_time']))
            {
                $date = getdate();
                $end_time=  empty($data['end_time'])? mktime(0,0, 0,$date['mon'],$date['mday']+1,$date['year']):$data['end_time']." 23:59:59";
                $start_time= empty($data['start_time'])?0:$data['start_time'];
                $query->whereTime('pb.create_time', 'between', [$start_time, $end_time]);
            }

            $where=[];
           // !empty($data["shop_id"])&&$where["s.id"] =$data["shop_id"];
            !empty($data["shop_id"])&&$where[] = ["exp","find_in_set(".$data["shop_id"].",shop_ids)"];
            !empty($data["search_data"])&&$where["sn"] =["like","%".trim($data["search_data"])."%"];
            $where["pb.".DATA_STATUS_NAME]= isset($data[DATA_STATUS_NAME])?$data[DATA_STATUS_NAME]:["neq",DATA_DELETE];;

            $query->where($where);
        };
        return $where;
    }
    
//    /**
//     * 信息编辑
//     */
//    public function billEdit($data = [])
//    {
//
//        $validate_result = $this->validateShopPriceBill->scene('edit')->check($data);
//
//        if (!$validate_result) {
//
//            return [RESULT_ERROR, $this->validateShopPriceBill->getError()];
//        }
//        $data["sn"] = "SP".date("YmdHis");
////        $data["check_time"] = time();
//        empty($data["id"])&&$data["admin_add_id"] = ADMIN_ID;
//
//
//        $result = $this->modelShopPriceBill->setInfo($data);
//
//
//        $handle_text = empty($data['id']) ? '新增' : '编辑';
//
//        $result && action_log($handle_text, '' . $handle_text . '，单号：' . $data['sn']."机构id：".$data["shop_id"]);
//
//        return $result ? [RESULT_SUCCESS, '操作成功',] : [RESULT_ERROR, $this->modelShopPriceBill->getError()];
//    }

    
    /**
     * 删除
     */
    public function billDel($where = [])
    {
        
        $result = $this->modelShopPriceBill->deleteInfo($where);
        
        $result && action_log('删除', '删除' . '，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelShopPriceBill->getError()];
    }
}
