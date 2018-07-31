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
class PurchaseBackLog extends AdminBase
{

    //    /**
    //     * 信息
    //     */
    //    public function getLogInfo($where = [], $field = true)
    //    {
    //
    //        return $this->modelPurchaseBackLog->getInfo($where, $field);
    //    }
    /**
     * 列表
     */
    public function getLogList($where = [], $field = "*", $order = '', $paginate = 0, $join = [])
    {
        $this->modelPurchaseBackLog->alias("pl");
        if (empty($where["pl.".DATA_STATUS_NAME]) && empty($where[DATA_STATUS_NAME]))
        {
            $where["pl.".DATA_STATUS_NAME] = ["neq", DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'purchase_back pb', 'pb.id = pl.back_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods g', 'g.id = pl.goods_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],
            ];
        }

        return $this->modelPurchaseBackLog->getList($where, $field, $order, $paginate, $join);
    }


    /**
     * 导出单据列表
     */
    public function exportLogList($where = [], $field = 'barcode,name,unit,specification,num,cost_price,num*cost_price total_cost_price,retail_price,num*retail_price total_retail_price,produce_date,remark', $order = 'pl.id ')
    {

        $list = $this->getLogList($where, $field, $order, false);

        $titles = "条码,商品名称,单位,规格,数量,进价,金额,售价,售价金额.生产日期,备注";
        $keys   = "barcode,name,unit,specification,num,cost_price,total_cost_price,retail_price,total_retail_price,produce_date,remark";

        action_log('导出', '导出采购退货明细表');

        export_excel($titles, $keys, $list, '采购退货明细表'.date("Y-m-d"));
    }



    /**
     * 查询条件
     */
    public function getWhere($data = [])
    {

        $where=[];
        $where['back_id']=!empty($data["back_id"])?$data["back_id"]:0;
        return $where;
    }


    public function getGoodsChooseWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['gf.name|gf.barcode1'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['cid']) && $where['c.id'] = $data['cid'];
        !empty($data['supplier_id']) && $where['g.supplier_id'] = $data['supplier_id'];
        !empty($data['barcodes']) && $where["gb.barcode"] = ["not in", $data['barcodes']];
        !empty($data['shop_id'])&&$where['gp.shop_id'] =$where['pgp.shop_id']= $data['shop_id'];
        return $where;
    }

    /**
     * 验证条码是否存在
     */
    public function existBarcode($data=[]){
        $where['gb.barcode']=$data['barcode'];
        $where['gf.goods_id']=$data['gid'];
      $barcodeInfo= $this->logicGoodsBarcode->getBarcodeJoinInfo($where,'gb.id');
        return $barcodeInfo ? [RESULT_SUCCESS, '操作成功','']: [RESULT_ERROR, '条码不存在'];
    }



    /**
     * 信息编辑
     */
    public function logEdit($data = [])
    {

        $validate_result = $this->validatePurchaseBackLog->scene('edit')->check($data);

        if (!$validate_result)
        {

            return [RESULT_ERROR, $this->validatePurchaseBackLog->getError()];
        }


        //        1:审核过的单据不能再次编辑
        if (!empty($data["back_id"]))
        {
            $billIn = $this->modelPurchaseBack->getInfo(["id" => $data["back_id"]]);
            if (!empty($billIn) && $billIn["status"] == 1)
            {
                return [RESULT_ERROR, "已审核过的单据不能再次修改"];
            }
        }


        $fn     = function() use ($data) {

            if (empty($data["back_id"]))
            {
                $data["sn"]           = "PR".date("YmdHis");
                $data["admin_add_id"] = ADMIN_ID;
            }

            $logs = [];
            foreach ($data as $name => $item)
            {
                if (is_array($item))
                {

                    foreach ($item as $k => $val)
                    {


                        if ($name == "id" && trim($val) == "")
                        {
                            $logs[$k][$name] = null;
                        }
                        else
                        {
                            $logs[$k][$name] = $val;
                        }


                    }
                }
            }
            $data['amount'] =0;
            foreach ($data['cost_price'] as $key=>$value){
                $data['amount']+=$value*$data['num'][$key];
            }

            //保存退货订单(判断新增或编辑)
            $data["id"] = empty($data["back_id"]) ? null: $data["back_id"];
            $this->modelPurchaseBack->setInfo($data);

            //保存退货记录明细
            empty($data["back_id"]) && $data["back_id"] = $this->modelPurchaseBack->getLastInsID();
            foreach ($logs as $key => $log)
            {
                $logs[$key]["back_id"]          = $data["back_id"];
                $logs[$key][DATA_STATUS_NAME] = DATA_DISABLE;
            }

            $this->modelPurchaseBackLog->setInfo([DATA_STATUS_NAME => DATA_DELETE], ["back_id" => $data["back_id"]]);

            $this->modelPurchaseBackLog->setList($logs, true);
            return intval($data['back_id']);
        };
        $result = closure_list_exe([$fn]);

        //查询是否直接审核
        $shopPermission= $this->logicShopPermission->getPermissionInfo(['shop_id'=> $data['shop_id']]);
        if ($shopPermission&&$shopPermission['save_bills_check']==1)
        {
            $data['back_id']=$result[0];
        }

        $handle_text = empty($data['back_id']) ? '新增': '编辑';
        $url         = url('PurchaseBack/billlist');
        $result && action_log($handle_text, $handle_text.'，退货订单：'.$result[0]);

        return $result ? [RESULT_SUCCESS, '操作成功', $url,$data['back_id']]: [RESULT_ERROR, $this->modelPurchaseBackLog->getError()];
    }


    /**
     * 删除
     */
    public function logDel($where = [])
    {
        $bill = $this->modelPurchaseBack->getInfo(["id" => $where["back_id"]]);
        if (!empty($bill) && $bill["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次修改"];
        }

        $result = $this->modelPurchaseBackLog->deleteInfo($where);

        $result && action_log('删除', '删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功', '']: [RESULT_ERROR, $this->modelPurchaseBackLog->getError()];
    }

    /**
     * 审核
     * 判断退货单状态,
     * 判断采购订单状态,
     * 通过退货商品明细判断采购订单是否完全使用,
     * 更新退货单审核状态
     * 更新退货商品明细
     * 更新采购订单使用状态
     * 更新价格
     * 更新门店库存
     */
    public function checking($data = [])
    {
        $backInfo = $this->modelPurchaseBack->getInfo(["id" => $data["back_id"]]);
        if (empty($backInfo))
        {
            return [RESULT_ERROR, "请先保存单据后再审核"];
        }
        elseif (!empty($backInfo) && $backInfo["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次审核"];
        }


        $sqls =['updateStockSql'=>''];

        if (!empty($backInfo['in_id']))
        {
            $billInfo = $this->modelPurchaseIn->getInfo(['id' => $backInfo['in_id']]);
            if ($billInfo)
            {
                if ($billInfo[DATA_STATUS_NAME] != 1)
                {
                    return [RESULT_ERROR, "采购收货单未审核"];
                }
                elseif ($billInfo['use_status'] != 0)
                {
                    return [RESULT_ERROR, "采购收货单已被使用"];
                }


                $barcodes = [];
                $sqls['updateStockSql']  .=' INSERT INTO `ob_goods_barcode_stock` ( `shop_id`,`goods_id`,`supplier_id`,`barcode`,`stock_actual`) VALUES ';

                //获取退货商品明细
                $inLogList      = $this->getLogList(["pl.".DATA_STATUS_NAME => DATA_DISABLE, "back_id" => $backInfo["id"]], "pl.*,gf.id profile_id,pb.shop_id,supplier_id", '', false);

                foreach ($inLogList as $item)
                {
                   array_push($barcodes, $item['barcode']);

                    //拼接 更新商品库存sql
                    $sqls['updateStockSql'].=" ({$item['shop_id']},{$item['goods_id']},{$item['supplier_id']},{$item['barcode']},-{$item['num']} ), ";
                }

                $sqls['updateStockSql'] = trim($sqls['updateStockSql'],' ,'). ' ON DUPLICATE KEY UPDATE `stock_actual` = `stock_actual` + VALUES(stock_actual)';

            }
        }


        $fn = function() use ($data, $backInfo,  $sqls) {
            //保存退货单据
            $this->modelPurchaseBack->setInfo([DATA_STATUS_NAME => DATA_NORMAL, "check_time" => date('Y-m-d H:i:s', time()), "admin_check_id" => ADMIN_ID], ["id" => $data["back_id"]]);

            //更新退货商品明细
            $this->modelPurchaseBackLog->setInfo([DATA_STATUS_NAME => DATA_NORMAL], ["back_id" => $data["back_id"], DATA_STATUS_NAME => DATA_DISABLE]);

            //更改采购订单使用状态
            if (!empty($backInfo['in_id']) )
            {
                $this->modelPurchaseIn->setInfo(["use_status" => 1], ["id" => $backInfo["in_id"]]);

            }
            if (!empty($sqls['updateStockSql']))
            {
                //更改每种条码库存数量
                $this->execute($sqls['updateStockSql']);
            }

        };

        $result = closure_list_exe([$fn]);
        //        $url    = url('PurchaseBack/billlist');

        $result && action_log('审核', '审核单据，单据编号：'.$backInfo['sn']);

        return $result ? [RESULT_SUCCESS, '审核成功']: [RESULT_ERROR, $this->modelPurchaseBackLog->getError()];
    }



}
