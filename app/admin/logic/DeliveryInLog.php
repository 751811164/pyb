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
class DeliveryInLog extends AdminBase
{

    /**
     * 信息
     */
    public function getLogInfo($where = [], $field = true)
    {

        return $this->modelDeliveryInLog->getInfo($where, $field);
    }

    /**
     * 列表
     */
    public function getLogList($where = [], $field = "*", $order = 'dil.id', $paginate = 0, $join = [])
    {
        $this->modelDeliveryInLog->alias("dil");
        if (empty($where["dil.".DATA_STATUS_NAME]) && empty($where[DATA_STATUS_NAME]))
        {
            $where["dil.".DATA_STATUS_NAME] = ["neq", DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'delivery_in di', 'di.id = dil.in_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods g', 'g.id = dil.goods_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],

            ];
        }

        return $this->modelDeliveryInLog->getList($where, $field, $order, $paginate, $join);
    }


    /**
     * 导出单据列表
     */
    public function exportLogList($where = [], $field = 'dil.*,gf.name,gf.unit,gf.specification,g.size,g.color,delivery_price*num total_delivery_price', $order = 'dil.id')
    {

        $list = $this->getLogList($where, $field, $order, false);

        $titles = "条码,商品名称,单位,规格,数量,配送价,金额,备注,花色,尺码,当前库存,目标库存";
        $keys   = "barcode,name,unit,specification,num,delivery_price,total_delivery_price,remark,color,size,current_stock,target_stock";

        action_log('导出', '导出收货入库明细表');

        export_excel($titles, $keys, $list, '收货入库明细表'.date("Y-m-d"));
    }


    /**
     * 查询条件
     */
    public function getWhere($data = [])
    {
        $where           = [];
        $where['in_id'] = !empty($data["in_id"]) ? $data["in_id"]: 0;
        return $where;


    }


    public function getGoodsChooseWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['gf.name|gf.barcode1'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['cid']) && $where['c.id'] = $data['cid'];
        !empty($data['barcodes']) && $where["gb.barcode"] = ["not in", $data['barcodes']];
        $where['gp.shop_id'] = $where['pgp.shop_id'] = $data['in_shop_id'];
        return $where;
    }


    /**
     * 信息编辑
     */
    public function logEdit($data = [])
    {

        $validate_result = $this->validateDeliveryInLog->scene('edit')->check($data);

        if (!$validate_result)
        {
            return [RESULT_ERROR, $this->validateDeliveryInLog->getError()];
        }


        //        1:审核过的单据不能再次编辑
        if (!empty($data["in_id"]))
        {
            $deliveryIn = $this->modelDeliveryIn->getInfo(["id" => $data["in_id"]]);
            if (!empty($deliveryIn) && $deliveryIn["status"] == 1)
            {
                return [RESULT_ERROR, "已审核过的单据不能再次修改"];
            }
        }


        $fn     = function() use ($data) {

            if (empty($data["in_id"]))
            {
                $data["sn"]           = "DI".date("YmdHis");
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
            $data['amount'] = 0;
            foreach ($data['delivery_price'] as $key => $value)
            {
                $data['amount'] += $value * $data['num'][$key];
            }
            //保存入库单(判断新增或编辑)
            $data["id"] = empty($data["in_id"]) ? null: $data["in_id"];
            $this->modelDeliveryIn->setInfo($data);

            //保存入库记录明细
            empty($data["in_id"]) && $data["in_id"] = $this->modelDeliveryIn->getLastInsID();
            foreach ($logs as $key => $log)
            {
                $logs[$key]["in_id"]         = $data["in_id"];
                $logs[$key][DATA_STATUS_NAME] = DATA_DISABLE;
            }

            $this->modelDeliveryInLog->setInfo([DATA_STATUS_NAME => DATA_DELETE], ["in_id" => $data["in_id"]]);

            $this->modelDeliveryInLog->setList($logs, true);
            return intval($data['in_id']);
        };
        $result = closure_list_exe([$fn]);

        $handle_text = empty($data['in_id']) ? '新增': '编辑';
        //查询是否直接审核
        $shopPermission = $this->logicShopPermission->getPermissionInfo(['shop_id' => $data['in_shop_id']]);
        if ($shopPermission && $shopPermission['save_bills_check'] == 1)
        {
            $in_id = $result[0];
        }
        else
        {
            $in_id = '';
        }

        $url = url('DeliveryIn/inlist');
        $result && action_log($handle_text, $handle_text.'，配送收货入库单：'.$result[0]);

        return $result ? [RESULT_SUCCESS, '操作成功', $url, $in_id]: [RESULT_ERROR, $this->modelDeliveryInLog->getError()];
    }


    /**
     * 删除
     */
    public function logDel($where = [])
    {
        $deliveryIn = $this->modelDeliveryIn->getInfo(["id" => $where["in_id"]]);
        if (!empty($deliveryIn) && $deliveryIn["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次修改"];
        }
        $result = $this->modelDeliveryInLog->deleteInfo($where);

        //判断是否删除引用的出库单
        $info   = $this->modelDeliveryInLog->getInfo([DATA_STATUS_NAME => ['neq', DATA_DELETE], 'in_id' => $where['in_id']]);
        if (empty($info))
        {
            $this->modelDeliveryIn->setInfo(['out_id' => ''], ["id" => $where["in_id"]]);
        }

        $result && action_log('删除', '删除配送收货入库单已选商品'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功', '']: [RESULT_ERROR, $this->modelDeliveryInLog->getError()];
    }

    /**
     * 审核
     */
    public function checking($data = [])
    {
        //获取入库单据信息
        $deliveryIn = $this->modelDeliveryIn->getInfo(["id" => $data["in_id"]]);
        if (empty($deliveryIn))
        {
            return [RESULT_ERROR, "请先保存单据后再审核"];
        }
        elseif (!empty($deliveryIn) && $deliveryIn["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次审核"];
        }


        //判断出库单是否使用过
        if (!empty($deliveryIn['out_id']))
        {
            $outInfo = $this->modelDeliveryApply->getInfo(['id' => $deliveryIn['out_id']]);
            if ($outInfo)
            {
                if ($outInfo[DATA_STATUS_NAME] != 1)
                {
                    return [RESULT_ERROR, "配送出库单未审核"];
                }
                elseif ($outInfo['use_status'] != 0)
                {
                    return [RESULT_ERROR, "配送出库单已被使用或终止"];
                }
            }
        }

        $count    = $use_status = 0;
        $barcodes = [];
        $sqls       = ['updateInStockSql' => '', 'updateOutStockSql' => ''];
        $sqls['updateInStockSql'] .= ' INSERT INTO `ob_goods_barcode_stock` ( `shop_id`,`barcode`,`stock_actual`,`update_time`) VALUES ';
        $sqls['updateOutStockSql'] .= ' INSERT INTO `ob_goods_barcode_stock` ( `shop_id`,`barcode`,`stock_actual`,`update_time`) VALUES ';

        $time      = time();
        //获取商品明细
        $logList = $this->getLogList(["dil.".DATA_STATUS_NAME => DATA_DISABLE, "in_id" => $deliveryIn["id"]], "di.in_shop_id,di.out_shop_id,dil.id,dil.barcode,num", '', false);
        if (empty($logList))
        {
            return [RESULT_ERROR, "请先保存已选择的商品"];
        }

        if (!empty($deliveryIn['out_id']))
        {
            foreach ($logList as $key => $item)
            {

                array_push($barcodes, $item['barcode']);

                //拼接 更新商品库存sql

                $sqls['updateInStockSql'] .= " ({$item['in_shop_id']},{$item['barcode']},{$item['num']},{$time}), ";
                $sqls['updateOutStockSql'] .= " ({$item['out_shop_id']},{$item['barcode']},{$item['num']},{$time}), ";

            }
            $sqls['updateInStockSql'] = trim($sqls['updateInStockSql'], ' ,').' ON DUPLICATE KEY UPDATE `stock_actual` = `stock_actual` + VALUES(`stock_actual`)';
            $sqls['updateOutStockSql'] = trim($sqls['updateOutStockSql'], ' ,').' ON DUPLICATE KEY UPDATE `stock_actual` = `stock_actual` - VALUES(`stock_actual`)';
        }


        //获取出库单商品明细信息
        $outLogList = $this->modelDeliveryOutLog->getList([DATA_STATUS_NAME => DATA_NORMAL, "out_id" => $deliveryIn["out_id"]], true, '', false);
        //判断出库单使用状态
        foreach ($outLogList as $key => $out)
        {
            if (in_array($out["barcode"], $barcodes))
            {
                $count++;
            }
        }
        if ($count > 0)
        {
            if ($count == count($outLogList))
            {
                $use_status = 1;
            }
            elseif ($count < count($outLogList))
            {
                $use_status = 2;
            }
        }


        $fn = function() use ($data, $deliveryIn, $use_status,$sqls) {
            //保存单据
            $this->modelDeliveryIn->setInfo([DATA_STATUS_NAME => DATA_NORMAL, "check_time" => date('Y-m-d H:i:s', time()), "admin_check_id" => ADMIN_ID], ["id" => $data["in_id"]]);
            //更新商品明细
            $this->modelDeliveryInLog->setInfo([DATA_STATUS_NAME => DATA_NORMAL], ["in_id" => $data["in_id"], DATA_STATUS_NAME => DATA_DISABLE]);
            //更新出库单使用状态
            if (!empty($deliveryIn['out_id']) && $use_status > 0)
            {
                $this->modelDeliveryApply->setInfo(['use_status'=>$use_status], ["id" => $deliveryIn['out_id']]);
            }
            //更新收货门店库存
            $this->execute($sqls['updateInStockSql']);

            //更新配送门店库存
            $this->execute($sqls['updateOutStockSql']);

        };

        $result = closure_list_exe([$fn]);
        //        $url    = url('DeliveryIn/billlist');

        $result && action_log('审核', '审核配送入库单据，单据编号：'.$deliveryIn['sn']);

        return $result ? [RESULT_SUCCESS, '审核成功']: [RESULT_ERROR, $this->modelDeliveryInLog->getError()];
    }


}
