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
class PurchaseBillLog extends AdminBase
{

//    /**
//     * 信息
//     */
//    public function getLogInfo($where = [], $field = true)
//    {
//
//        return $this->modelPurchaseBillLog->getInfo($where, $field);
//    }
    /**
     * 列表
     */
    public function getLogList($where = [], $field = "*", $order = '', $paginate = 0,$join=[])
    {
        $this->modelPurchaseBillLog->alias("pl");
        if (empty($where["pl.".DATA_STATUS_NAME])&&empty($where[DATA_STATUS_NAME]))
        {
            $where["pl.".DATA_STATUS_NAME]=["neq",DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
                //            [SYS_DB_PREFIX.'shop s', 's.id = pl.shop_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods g', 'g.id = pl.goods_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],
            ];
        }

        return $this->modelPurchaseBillLog->getList($where, $field, $order, $paginate,$join);
    }



    /**
     * 导出单据列表
     */
    public function exportLogList($where = [], $field = 'barcode,name,unit,specification,num,cost_price,num*cost_price total_cost_price,retail_price,num*retail_price total_retail_price,remark', $order = 'pl.id')
    {

        $list = $this->getLogList($where, $field, $order, false);

        $titles = "条码,商品名称,单位,规格,数量,进价,金额,售价,售价金额,备注";
        $keys   = "barcode,name,unit,specification,num,cost_price,total_cost_price,retail_price,total_retail_price,remark";

        action_log('导出', '导出采购订单明细表');

        export_excel($titles, $keys, $list, '采购订单明细表'.date("Y-m-d"));
    }



    /**
     * 查询条件
     */
    public function getWhere($data=[]){
        $where=[];
        $where['bill_id']=!empty($data["bill_id"])?$data["bill_id"]:0;
        return $where;
    }


    public function getGoodsChooseWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['gf.name|gf.barcode1'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['cid']) && $where['c.id'] = $data['cid'];
        !empty($data['supplier_id']) && $where['g.supplier_id'] = $data['supplier_id'];
        !empty($data['barcodes'])&&$where["gb.barcode"] = ["not in", $data['barcodes']];

        return $where;
    }

    
    /**
     * 信息编辑
     */
    public function logEdit($data = [])
    {

        $validate_result = $this->validatePurchaseBillLog->scene('edit')->check($data);

        if (!$validate_result) {

            return [RESULT_ERROR, $this->validatePurchaseBillLog->getError()];
        }
        //        1:审核过的单据不能再次编辑
        if (!empty($data["bill_id"]))
        {
            $bill=  $this->modelPurchaseBill->getInfo(["id"=>$data["bill_id"]]);
            if (!empty($bill)&&$bill["status"]==1)
            {
                return [RESULT_ERROR, "已审核过的单据不能再次修改"];
            }
        }


        $fn=function()use ($data){

            if (empty($data["bill_id"]))
            {
                $data["sn"] = "PO".date("YmdHis");
                $data["admin_add_id"] = ADMIN_ID;
            }

            $logs=[];
            foreach ($data as $name=>$item){
                if (is_array($item))
                {

                    foreach ($item as $k=>$val)
                    {


                        if ($name=="id"&&trim($val)=="")
                        {
                            $logs[$k][$name]=null;
                        }
                        else
                        {
                            $logs[$k][$name]=$val;
                        }


                    }
                }
            }

            $data['amount'] =0;
            foreach ($data['cost_price'] as $key=>$value){
                $data['amount']+=$value*$data['num'][$key];
            }

            //保存订单(判断新增或编辑)
            $data["id"] =empty($data["bill_id"])?null:$data["bill_id"];
            $this->modelPurchaseBill->setInfo($data);

            //保存采购记录
            empty($data["bill_id"])&&$data["bill_id"]= $this->modelPurchaseBill->getLastInsID();
            foreach ($logs as $key=>$log){
                $logs[$key]["bill_id"]=$data["bill_id"];
            }

            $this->modelPurchaseBillLog->setList($logs,true);
            return intval($data['bill_id']);
        };
        $result=closure_list_exe([$fn]);
        session('billInfo',null);
        $handle_text = empty($data['bill_id']) ? '新增' : '编辑';

        //查询是否直接审核
        $shopPermission= $this->logicShopPermission->getPermissionInfo(['shop_id'=> $data['shop_id']]);
        if ($shopPermission&&$shopPermission['save_bills_check']==1)
        {
            $data['bill_id']=$result[0];
        }

        $url=url('PurchaseBill/billlist');
        $result && action_log($handle_text, $handle_text . '，采购订单：' . $result[0]);

        return $result ? [RESULT_SUCCESS, '操作成功',$url,$data['bill_id']] : [RESULT_ERROR, $this->modelPurchaseBillLog->getError()];
    }


    
    /**
     * 删除
     */
    public function logDel($where = [])
    {
        $bill=  $this->modelPurchaseBill->getInfo(["id"=>$where["bill_id"]]);
        if (!empty($bill)&&$bill["status"]==1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次修改"];
        }

        $result = $this->modelPurchaseBillLog->deleteInfo($where);

        $result && action_log('删除', '删除' . '，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功',''] : [RESULT_ERROR, $this->modelPurchaseBillLog->getError()];
    }

    /**
     * 审核
     */
    public function checking($data=[]){
        $bill=  $this->modelPurchaseBill->getInfo(["id"=>$data["bill_id"]]);
        if (empty($bill))
        {
            return [RESULT_ERROR, "请先保存单据后再审核"];
        }
        elseif ($bill["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次审核"];
        } elseif ( $bill["use_status"] != 0)
        {
            return [RESULT_ERROR, "只能审核未使用的订单"];
        }

        $fn=function()use($data,$bill){
            //保存单据
            $this->modelPurchaseBill->setInfo([DATA_STATUS_NAME=>DATA_NORMAL,"check_time"=>date('Y-m-d H:i:s',time()),"admin_check_id"=>ADMIN_ID],["id"=>$data["bill_id"]]);
            //更新订单商品明细
            $this->modelPurchaseBillLog->setInfo([DATA_STATUS_NAME=>DATA_NORMAL],["bill_id"=>$data["bill_id"],DATA_STATUS_NAME=>DATA_DISABLE]);

        };

        $result=closure_list_exe([$fn]);
//        $url=url('PurchaseBill/billlist');

        $result && action_log('审核',  '审核单据，单据编号：' . $bill['sn']);

        return $result ? [RESULT_SUCCESS, '审核成功'] : [RESULT_ERROR, $this->modelPurchaseBillLog->getError()];
    }

    //反审核
    public function unchecking($data=[]){
        $bill=  $this->modelPurchaseBill->getInfo(["id"=>$data["bill_id"]]);
        if (empty($bill))
        {
            return [RESULT_ERROR, "单据未保存"];
        }elseif ( $bill["use_status"] != 0)
        {
            return [RESULT_ERROR, "只能反审核未使用的订单"];
        }

        $date = getdate();
        if (!empty($bill)&&$bill["status"]!=1)
        {
            return [RESULT_ERROR, "单据未审核"];
        }
        elseif (strtotime($bill["check_time"])<mktime(0,0,0,$date['mon'],$date['mday'],$date['year']))
        {
            return [RESULT_ERROR, "只能反审核当天审核的采购订单"];
        }
        
        
        $fn=function()use($data,$bill){
            //保存单据
            $this->modelPurchaseBill->setInfo([DATA_STATUS_NAME=>DATA_DISABLE,"check_time"=>'',"admin_check_id"=>''],["id"=>$data["bill_id"]]);
            //更新订单商品明细
            $this->modelPurchaseBillLog->setInfo([DATA_STATUS_NAME=>DATA_DISABLE],["bill_id"=>$data["bill_id"],DATA_STATUS_NAME=>DATA_NORMAL]);
        };

        $result=closure_list_exe([$fn]);

        $result && action_log('审核',  '审核单据，单据编号：' . $bill['sn']);

        return $result ? [RESULT_SUCCESS, '审核成功'] : [RESULT_ERROR, $this->modelPurchaseBillLog->getError()];
    }



}
