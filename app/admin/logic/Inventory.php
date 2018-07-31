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
 * 采购退货逻辑
 */
class Inventory extends AdminBase
{

    /**
     * 信息
     */
    public function getInventoryInfo($where = [], $field = true)
    {
        return $this->modelInventory->getInfo($where, $field);
    }

    /**
     * 信息
     */
    public function getInventoryJoinInfo($where = [], $field = "*",$join=[])
    {
        $this->modelInventory->alias("i");
        if (empty($where["i.".DATA_STATUS_NAME])&&empty($where[DATA_STATUS_NAME]))
        {
            $where["i.".DATA_STATUS_NAME]=["neq",DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'admin add', 'add.id = i.admin_add_id', 'LEFT'],
                [SYS_DB_PREFIX.'admin check', 'check.id = i.admin_check_id', 'LEFT'],
            ];
        }

        return $this->modelInventory->getInfo($where, $field,$join);
    }

    /**
     * 列表
     */
    public function getInventoryList($where = [], $field = "i.*,s.name shop_name,add.username admin_add_name,ck.username admin_check_name", $order = 'i.id desc', $paginate = 0)
    {
        $this->modelInventory->alias("i");

        $join = [
            [SYS_DB_PREFIX.'shop s', 's.id = i.shop_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin add', 'add.id = i.admin_add_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin ck', 'ck.id = i.admin_check_id', 'LEFT'],
        ];
        return $this->modelInventory->getList($where, $field, $order, $paginate,$join);
    }


    public function getInventoryColumn($where = [], $field = '', $key = '',$limit=null)
    {
        return $this->modelInventory->where($where)->limit($limit)->column($field, $key);
    }


    /**
     * 导出单据列表
     */
    public function exportInventoryList($where = [], $field = 'i.*,s.name shop_name,add.username admin_add_name,ck.username admin_check_name', $order = 'i.id desc')
    {

        $list = $this->getInventoryList($where, $field, $order, false);

        $titles = "单据编号,审核状态,机构名称,操作员,操作日期,审核人,备注";
        $keys   = "sn,status_text,shop_name,admin_add_name,create_time,admin_check_name,remark";

        action_log('导出', '导出存货盘点单列表');

        export_excel($titles, $keys, $list, '存货盘点单'.date("Y-m-d"));
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
                $query->whereTime('i.create_time', 'between', [$start_time, $end_time]);
            }
            elseif (!empty($data['create_time']))
            {
                $query->whereTime('i.create_time',$data['create_time'] );
            }

            $where=[];
            !empty($data["shop_id"])&&$where["s.id"] =$data["shop_id"];
            isset($data["use_status"])&&$where["i.use_status"] =$data["use_status"];

            !empty($data["search_data"])&&$where["i.sn"] =["like","%".$data["search_data"]."%"];
            $where["i.".DATA_STATUS_NAME]= isset($data[DATA_STATUS_NAME])?$data[DATA_STATUS_NAME]:["neq",DATA_DELETE];

            $query->where($where);
        };
        return $where;
    }
    


    /**
     * 删除未审核的盘点单
     */
    public function InventoryDel($data = [])
    {
        $where['id'] = ["in",$data["ids"]];
        $where[DATA_STATUS_NAME] = DATA_DISABLE;
        $result = $this->modelInventory->deleteInfo($where);

        $result && action_log('删除盘点单', '删除盘点单' . '，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelInventory->getError()?:"未做更改"];
    }
}
