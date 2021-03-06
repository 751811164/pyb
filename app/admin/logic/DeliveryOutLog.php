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
class DeliveryOutLog extends AdminBase
{

    /**
     * 信息
     */
    public function getLogInfo($where = [], $field = true)
    {

        return $this->modelDeliveryOutLog->getInfo($where, $field);
    }

    /**
     * 列表
     */
    public function getLogList($where = [], $field = "*", $order = 'dol.id', $paginate = 0, $join = [])
    {
        $this->modelDeliveryOutLog->alias("dol");
        if (empty($where["dol.".DATA_STATUS_NAME]) && empty($where[DATA_STATUS_NAME]))
        {
            $where["dol.".DATA_STATUS_NAME] = ["neq", DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'delivery_out do', 'do.id = dol.out_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods g', 'g.id = dol.goods_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],

            ];
        }

        return $this->modelDeliveryOutLog->getList($where, $field, $order, $paginate, $join);
    }


    /**
     * 导出单据列表
     */
    public function exportLogList($where = [], $field = 'dol.*,gf.name,gf.unit,gf.specification,g.size,g.color,delivery_price*num total_delivery_price', $order = 'dol.id')
    {

        $list = $this->getLogList($where, $field, $order, false);

        $titles = "条码,商品名称,单位,规格,数量,配送价,金额,备注,花色,尺码,当前库存,目标库存";
        $keys   = "barcode,name,unit,specification,num,delivery_price,total_delivery_price,remark,color,size,current_stock,target_stock";

        action_log('导出', '导出配送出库明细表');

        export_excel($titles, $keys, $list, '配送出库明细表'.date("Y-m-d"));
    }


    /**
     * 查询条件
     */
    public function getWhere($data = [])
    {
        $where           = [];
        $where['out_id'] = !empty($data["out_id"]) ? $data["out_id"]: 0;
        return $where;


    }


    public function getGoodsChooseWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['gf.name|gf.barcode1'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['cid']) && $where['c.id'] = $data['cid'];
        !empty($data['barcodes']) && $where["gb.barcode"] = ["not in", $data['barcodes']];
        $where['gp.shop_id'] = $where['pgp.shop_id'] = $data['out_shop_id'];
        return $where;
    }


    /**
     * 信息编辑
     */
    public function logEdit($data = [])
    {

        $validate_result = $this->validateDeliveryOutLog->scene('edit')->check($data);

        if (!$validate_result)
        {
            return [RESULT_ERROR, $this->validateDeliveryOutLog->getError()];
        }


        //        1:审核过的单据不能再次编辑
        if (!empty($data["out_id"]))
        {
            $deliveryOut = $this->modelDeliveryOut->getInfo(["id" => $data["out_id"]]);
            if (!empty($deliveryOut) && $deliveryOut["status"] == 1)
            {
                return [RESULT_ERROR, "已审核过的单据不能再次修改"];
            }
        }


        $fn     = function() use ($data) {

            if (empty($data["out_id"]))
            {
                $data["sn"]           = "DO".date("YmdHis");
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
            //保存出库单(判断新增或编辑)
            $data["id"] = empty($data["out_id"]) ? null: $data["out_id"];
            $this->modelDeliveryOut->setInfo($data);

            //保存出库记录明细
            empty($data["out_id"]) && $data["out_id"] = $this->modelDeliveryOut->getLastInsID();
            foreach ($logs as $key => $log)
            {
                $logs[$key]["out_id"]         = $data["out_id"];
                $logs[$key][DATA_STATUS_NAME] = DATA_DISABLE;
            }

            $this->modelDeliveryOutLog->setInfo([DATA_STATUS_NAME => DATA_DELETE], ["out_id" => $data["out_id"]]);

            $this->modelDeliveryOutLog->setList($logs, true);
            return intval($data['out_id']);
        };
        $result = closure_list_exe([$fn]);

        $handle_text = empty($data['out_id']) ? '新增': '编辑';
        //查询是否直接审核
        $shopPermission = $this->logicShopPermission->getPermissionInfo(['shop_id' => $data['out_shop_id']]);
        if ($shopPermission && $shopPermission['save_bills_check'] == 1)
        {
            $out_id = $result[0];
        }
        else
        {
            $out_id = '';
        }

        $url = url('DeliveryOut/outlist');
        $result && action_log($handle_text, $handle_text.'，配送出库单：'.$result[0]);

        return $result ? [RESULT_SUCCESS, '操作成功', $url, $out_id]: [RESULT_ERROR, $this->modelDeliveryOutLog->getError()];
    }


    /**
     * 删除
     */
    public function logDel($where = [])
    {
        $deliveryOut = $this->modelDeliveryOut->getInfo(["id" => $where["out_id"]]);
        if (!empty($deliveryOut) && $deliveryOut["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次修改"];
        }

        $result = $this->modelDeliveryOutLog->deleteInfo($where);
        //判断是否删除引用的申请单
        $info   = $this->modelDeliveryOutLog->getInfo([DATA_STATUS_NAME => ['neq', DATA_DELETE], 'out_id' => $where['out_id']]);
        if (empty($info))
        {
            $this->modelDeliveryOut->setInfo(['apply_id' => ''], ["id" => $where["out_id"]]);
        }

        $result && action_log('删除', '删除配送出库单已选商品'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功', '']: [RESULT_ERROR, $this->modelDeliveryOutLog->getError()];
    }

    /**
     * 审核
     */
    public function checking($data = [])
    {
        //获取出库单据信息
        $deliveryOut = $this->modelDeliveryOut->getInfo(["id" => $data["out_id"]]);
        if (empty($deliveryOut))
        {
            return [RESULT_ERROR, "请先保存单据后再审核"];
        }
        elseif (!empty($deliveryOut) && $deliveryOut["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次审核"];
        }


        //判断出库单是否使用过
        if (!empty($deliveryOut['apply_id']))
        {
            $applyInfo = $this->modelDeliveryApply->getInfo(['id' => $deliveryOut['apply_id']]);
            if ($applyInfo)
            {
                if ($applyInfo[DATA_STATUS_NAME] != 1)
                {
                    return [RESULT_ERROR, "配送申请单未审核"];
                }
                elseif ($applyInfo['use_status'] != 0)
                {
                    return [RESULT_ERROR, "配送申请单已被使用或终止"];
                }
            }
        }

        $count    = $use_status = 0;
        $barcodes = [];
        //获取商品明细
        $logList = $this->getLogList(["dol.".DATA_STATUS_NAME => DATA_DISABLE, "out_id" => $deliveryOut["id"]], "dol.id,dol.barcode", '', false);
        if (empty($logList))
        {
            return [RESULT_ERROR, "请先保存已选择的商品"];
        }

        if (!empty($deliveryOut['apply_id']))
        {
            foreach ($logList as $key => $item)
            {

                array_push($barcodes, $item['barcode']);

            }
        }


        //获取申请单商品明细信息
        $billLogList = $this->modelDeliveryApplyLog->getList([DATA_STATUS_NAME => DATA_NORMAL, "apply_id" => $deliveryOut["apply_id"]], true, '', false);
        //判断申请单使用状态
        foreach ($billLogList as $key => $bill)
        {
            if (in_array($bill["barcode"], $barcodes))
            {
                $count++;
            }
        }
        if ($count > 0)
        {
            if ($count == count($billLogList))
            {
                $use_status = 1;
            }
            elseif ($count < count($billLogList))
            {
                $use_status = 2;
            }
        }


        $fn = function() use ($data, $deliveryOut, $use_status) {
            //保存单据
            $this->modelDeliveryOut->setInfo([DATA_STATUS_NAME => DATA_NORMAL, "check_time" => date('Y-m-d H:i:s', time()), "admin_check_id" => ADMIN_ID], ["id" => $data["out_id"]]);
            //更新商品明细
            $this->modelDeliveryOutLog->setInfo([DATA_STATUS_NAME => DATA_NORMAL], ["out_id" => $data["out_id"], DATA_STATUS_NAME => DATA_DISABLE]);
            //更新申请单使用状态
            if (!empty($deliveryOut['apply_id']) && $use_status > 0)
            {
                $this->modelDeliveryApply->setInfo(['use_status'=>$use_status], ["id" => $deliveryOut['apply_id']]);
            }
        };

        $result = closure_list_exe([$fn]);
        //        $url    = url('DeliveryOut/billlist');

        $result && action_log('审核', '配送出库单单据，单据编号：'.$deliveryOut['sn']);

        return $result ? [RESULT_SUCCESS, '审核成功']: [RESULT_ERROR, $this->modelDeliveryOutLog->getError()];
    }


}
