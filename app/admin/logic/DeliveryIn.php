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
class DeliveryIn extends AdminBase
{

    /**
     * 信息
     */
    public function getInInfo($where = [], $field = true)
    {
        return $this->modelDeliveryIn->getInfo($where, $field);
    }

    /**
     * 信息
     */
    public function getInJoinInfo($where = [], $field = "*",$join=[])
    {
        $this->modelDeliveryIn->alias("di");
        if (empty($where["di.".DATA_STATUS_NAME])&&empty($where[DATA_STATUS_NAME]))
        {
            $where["di.".DATA_STATUS_NAME]=["neq",DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'delivery_out do', 'do.id = di.out_id', 'LEFT'],
                [SYS_DB_PREFIX.'admin add', 'add.id = di.admin_add_id', 'LEFT'],
                [SYS_DB_PREFIX.'admin check', 'check.id = di.admin_check_id', 'LEFT'],
            ];
        }

        return $this->modelDeliveryIn->getInfo($where, $field,$join);
    }

    /**
     * 列表
     */
    public function getInList($where = [], $field = "di.*,os.name out_shop_name,is.name in_shop_name,add.username admin_add_name,ck.username admin_check_name", $order = 'di.id desc', $paginate = 0)
    {
        $this->modelDeliveryIn->alias("di");

        $join = [
            [SYS_DB_PREFIX.'shop is', 'is.id = di.in_shop_id', 'LEFT'],
            [SYS_DB_PREFIX.'shop os', 'os.id = di.out_shop_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin add', 'add.id = di.admin_add_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin ck', 'ck.id = di.admin_check_id', 'LEFT'],
        ];
        return $this->modelDeliveryIn->getList($where, $field, $order, $paginate,$join);
    }


    /**
     * 导出单据列表
     */
    public function exportInList($where = [], $field = 'di.*,os.name out_shop_name,is.name in_shop_name,add.username admin_add_name,ck.username admin_check_name', $order = 'di.id desc')
    {

        $list = $this->getInList($where, $field, $order, false);

        $titles = "单据编号,审核状态,终止状态,调出机构,收货机构,金额,操作员,操作日期,审核人,备注";
        $keys   = "sn,status_text,use_status_text,out_shop_name,in_shop_name,amount,admin_add_name,create_time,admin_check_name,in_remark";

        action_log('导出', '导出收货入库单据表');

        export_excel($titles, $keys, $list, '收货入库单据表'.date("Y-m-d"));
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
                $query->whereTime('di.create_time', 'between', [$start_time, $end_time]);
            }
            elseif (!empty($data['create_time']))
            {
                $query->whereTime('di.create_time',$data['create_time'] );
            }

            $where=[];
            !empty($data["in_shop_id"])&&$where["di.in_shop_id"] =$data["in_shop_id"];
            isset($data["use_status"])&&$where["di.use_status"] =$data["use_status"];

            !empty($data["search_data"])&&$where["di.sn"] =["like","%".$data["search_data"]."%"];
            $where["di.".DATA_STATUS_NAME]= isset($data[DATA_STATUS_NAME])?$data[DATA_STATUS_NAME]:["neq",DATA_DELETE];

            $query->where($where);
        };
        return $where;
    }


    /**
     * 终止单据
     */
    public function setInAbort($data){
        $ids=[];
        foreach (explode(',', $data["ids"]) as $key=>$value){
            array_push($ids,$value);
        }
        $where["id"]= ["in",$ids];
        $where["use_status"]= [['=',0],['=',2],"or"];//未使用和部分使用可终止

        $result = $this->modelDeliveryIn->setInfo(['use_status'=>DATA_DELETE],$where);

        $result && action_log('终止要货申请单据',  '终止要货申请单据，要货申请单据id：' .implode(',',$ids));

        return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $this->modelDeliveryIn->getError()?:'未做更改'];
    }


    /**
     * 删除未审核的单据
     */
    public function inDel($data = [])
    {

        $where['id'] = ["in",$data["ids"]];
        $where[DATA_STATUS_NAME] = DATA_DISABLE;
        $result = $this->modelDeliveryIn->deleteInfo($where);

        $result && action_log('删除要货申请单据', '删除要货申请单据' . '，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelDeliveryIn->getError()?:"未做更改"];
    }
}
