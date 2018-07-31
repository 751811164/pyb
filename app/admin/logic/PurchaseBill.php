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
class PurchaseBill extends AdminBase
{

    /**
     * 信息
     */
    public function getBillInfo($where = [], $field = true)
    {
        return $this->modelPurchaseBill->getInfo($where, $field);
    }

    /**
     * 信息
     */
    public function getBillJoinInfo($where = [], $field = "*",$join=[])
    {
        $this->modelPurchaseBill->alias("pb");
        if (empty($where["pb.".DATA_STATUS_NAME])&&empty($where[DATA_STATUS_NAME]))
        {
            $where["pb.".DATA_STATUS_NAME]=["neq",DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
//                                [SYS_DB_PREFIX.'shop s', 's.id = pb.shop_id', 'LEFT'],
                [SYS_DB_PREFIX.'admin add', 'add.id = pb.admin_add_id', 'LEFT'],
                [SYS_DB_PREFIX.'admin check', 'check.id = pb.admin_check_id', 'LEFT'],
            ];
        }

        return $this->modelPurchaseBill->getInfo($where, $field,$join);
    }

    /**
     * 列表
     */
    public function getBillList($where = [], $field = "pb.*,s.name shop_name,su.name supplier_name,add.username admin_add_name,ck.username admin_check_name", $order = 'pb.id desc', $paginate = 0)
    {
        $this->modelPurchaseBill->alias("pb");

        $join = [
            [SYS_DB_PREFIX.'shop s', 's.id = pb.shop_id', 'LEFT'],
            [SYS_DB_PREFIX.'supplier su', 'su.id = pb.supplier_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin add', 'add.id = pb.admin_add_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin ck', 'ck.id = pb.admin_check_id', 'LEFT'],
        ];
        return $this->modelPurchaseBill->getList($where, $field, $order, $paginate,$join);
    }


    /**
     * 导出单据列表
     */
    public function exportBillList($where = [], $field = 'pb.*,s.name shop_name,su.name supplier_name,add.username admin_add_name,ck.username admin_check_name', $order = 'pb.id desc')
    {

        $list = $this->getBillList($where, $field, $order, false);

        $titles = "单据编号,状态,机构,供应商,金额,操作员,操作日期,审核人,使用状态,备注";
        $keys   = "sn,status_text,shop_name,supplier_name,amount,admin_add_name,create_time,admin_check_name,use_status_text,bill_remark";

        action_log('导出', '导出采购订单列表');

        export_excel($titles, $keys, $list, '采购订单表'.date("Y-m-d"));
    }


    /**
     * 查询条件
     */
    public function getWhere($data=[]){

        $where=function($query)use($data){


            if (!empty($data['start_time'])||!empty($data['end_time']))
            {
                $date = getdate();
                $end_time=  empty($data['end_time'])? mktime(0,0, 0,$date['mon'],$date['mday']+1,$date['year']):$data['end_time']." 23:59:59";
                $start_time= empty($data['start_time'])?0:$data['start_time'];
                $query->whereTime('pb.create_time', 'between', [$start_time, $end_time]);
            }
            elseif (!empty($data['create_time']))
            {
                $query->whereTime('pb.create_time',$data['create_time'] );
            }
            $where=[];
            !empty($data["shop_id"])&&$where["s.id"] =$data["shop_id"];
            !empty($data["supplier_id"])&&$where["su.id"] =$data["supplier_id"];
            isset($data["use_status"])&&$where["pb.use_status"] =$data["use_status"];

            !empty($data["search_data"])&&$where["pb.sn"] =["like","%".$data["search_data"]."%"];
            $where["pb.".DATA_STATUS_NAME]= isset($data[DATA_STATUS_NAME])?$data[DATA_STATUS_NAME]:["neq",DATA_DELETE];

            $query->where($where);
        };
        return $where;
    }


    /**
     * 终止采购订单
     */
    public function setBillAbort($data){
        $ids=[];
        foreach (explode(',', $data["ids"]) as $key=>$value){
            array_push($ids,$value);
        }
        $where["id"]= ["in",$ids];
        $where["use_status"]= [['=',0],['=',2],"or"];//未使用和部分使用可终止

        $result = $this->modelPurchaseBill->setInfo(['use_status'=>DATA_DELETE],$where);

        $result && action_log('终止采购订单',  '终止采购订单，采购订单id：' .implode(',',$ids));

        return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $this->modelPurchaseBill->getError()?:'未做更改'];
    }


    /**
     * 删除未审核的采购订单
     */
    public function billDel($data = [])
    {

        $where['id'] = ["in",$data["ids"]];
        $where[DATA_STATUS_NAME] = DATA_DISABLE;
        $result = $this->modelPurchaseBill->deleteInfo($where);

        $result && action_log('删除采购订单', '删除采购订单' . '，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelPurchaseBill->getError()?:"未做更改"];
    }
}
