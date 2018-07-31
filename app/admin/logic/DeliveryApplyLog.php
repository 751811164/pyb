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
class DeliveryApplyLog extends AdminBase
{

    //    /**
    //     * 信息
    //     */
    //    public function getLogInfo($where = [], $field = true)
    //    {
    //
    //        return $this->modelDeliveryApplyLog->getInfo($where, $field);
    //    }
    /**
     * 列表
     */
    public function getLogList($where = [], $field = "*", $order = '', $paginate = 0, $join = [])
    {
        $this->modelDeliveryApplyLog->alias("dal");
        if (empty($where["dal.".DATA_STATUS_NAME]) && empty($where[DATA_STATUS_NAME]))
        {
            $where["dal.".DATA_STATUS_NAME] = ["neq", DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'delivery_apply da', 'da.id = dal.apply_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods g', 'g.id = dal.goods_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],

            ];
        }

        return $this->modelDeliveryApplyLog->getList($where, $field, $order, $paginate, $join);
    }


        /**
         * 导出单据列表
         */
        public function exportLogList($where = [], $field = 'dal.*,gf.name,gf.unit,gf.specification,g.size,g.color,delivery_price*num total_delivery_price', $order = 'dal.id')
        {

            $list = $this->getLogList($where, $field, $order, false);

            $titles = "条码,商品名称,单位,规格,数量,配送价,金额,备注,花色,尺码,当前库存,目标库存";
            $keys   = "barcode,name,unit,specification,num,delivery_price,total_delivery_price,remark,color,size,current_stock,target_stock";

            action_log('导出', '导出要货申请明细表');

            export_excel($titles, $keys, $list, '要货申请明细表'.date("Y-m-d"));
        }


    /**
     * 查询条件
     */
    public function getWhere($data = [])
    {
        $where             = [];
        $where['apply_id'] = !empty($data["apply_id"]) ? $data["apply_id"]: 0;
        return $where;


    }


    public function getGoodsChooseWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['gf.name|gf.barcode1'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['cid']) && $where['c.id'] = $data['cid'];
        !empty($data['barcodes']) && $where["gb.barcode"] = ["not in", $data['barcodes']];

        return $where;
    }


    /**
     * 信息编辑
     */
    public function logEdit($data = [])
    {

        $validate_result = $this->validateDeliveryApplyLog->scene('edit')->check($data);

        if (!$validate_result)
        {
            return [RESULT_ERROR, $this->validateDeliveryApplyLog->getError()];
        }


        //        1:审核过的单据不能再次编辑
        if (!empty($data["apply_id"]))
        {
            $deliveryApply = $this->modelDeliveryApply->getInfo(["id" => $data["apply_id"]]);
            if (!empty($deliveryApply) && $deliveryApply["status"] == 1)
            {
                return [RESULT_ERROR, "已审核过的单据不能再次修改"];
            }
        }


        $fn     = function() use ($data) {

            if (empty($data["apply_id"]))
            {
                $data["sn"]           = "DA".date("YmdHis");
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
            foreach ($data['delivery_price'] as $key=>$value){
                $data['amount']+=$value*$data['num'][$key];
            }
            //保存入库订单(判断新增或编辑)
            $data["id"] = empty($data["apply_id"]) ? null: $data["apply_id"];
            $this->modelDeliveryApply->setInfo($data);

            //保存入库记录明细
            empty($data["apply_id"]) && $data["apply_id"] = $this->modelDeliveryApply->getLastInsID();
            foreach ($logs as $key => $log)
            {
                $logs[$key]["apply_id"]       = $data["apply_id"];
                $logs[$key][DATA_STATUS_NAME] = DATA_DISABLE;
            }

            $this->modelDeliveryApplyLog->setInfo([DATA_STATUS_NAME => DATA_DELETE], ["apply_id" => $data["apply_id"]]);

            $this->modelDeliveryApplyLog->setList($logs, true);
            return intval($data['apply_id']);
        };
        $result = closure_list_exe([$fn]);

        $handle_text = empty($data['apply_id']) ? '新增': '编辑';
        //查询是否直接审核
        $shopPermission = $this->logicShopPermission->getPermissionInfo(['shop_id' => $data['apply_shop_id']]);
        if ($shopPermission && $shopPermission['save_bills_check'] == 1)
        {
            $apply_id = $result[0];
        }
        else
        {
            $apply_id = '';
        }

        $url = url('DeliveryApply/applylist');
        $result && action_log($handle_text, $handle_text.'，要货申请单：'.$result[0]);

        return $result ? [RESULT_SUCCESS, '操作成功', $url, $apply_id]: [RESULT_ERROR, $this->modelDeliveryApplyLog->getError()];
    }


    /**
     * 删除
     */
    public function logDel($where = [])
    {
        $deliveryApply = $this->modelDeliveryApply->getInfo(["id" => $where["apply_id"]]);
        if (!empty($deliveryApply) && $deliveryApply["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次修改"];
        }

        $result = $this->modelDeliveryApplyLog->deleteInfo($where);

        $result && action_log('删除', '删除要货申请单已选商品'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功', '']: [RESULT_ERROR, $this->modelDeliveryApplyLog->getError()];
    }

    /**
     * 审核
     */
    public function checking($data = [])
    {
        //获取入库单据信息
        $deliveryApply = $this->modelDeliveryApply->getInfo(["id" => $data["apply_id"]]);
        if (empty($deliveryApply))
        {
            return [RESULT_ERROR, "请先保存单据后再审核"];
        }
        elseif (!empty($deliveryApply) && $deliveryApply["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次审核"];
        }

        //获取商品明细
        $logList = $this->getLogList(["dal.".DATA_STATUS_NAME => DATA_DISABLE, "apply_id" => $deliveryApply["id"]], "dal.id", '', false);
        if (empty($logList))
        {
            return [RESULT_ERROR, "请先保存已选择的商品"];
        }


        $fn = function() use ($data, $deliveryApply) {
            //保存单据
            $this->modelDeliveryApply->setInfo([DATA_STATUS_NAME => DATA_NORMAL, "check_time" => date('Y-m-d H:i:s', time()), "admin_check_id" => ADMIN_ID], ["id" => $data["apply_id"]]);
            //更新商品明细
            $this->modelDeliveryApplyLog->setInfo([DATA_STATUS_NAME => DATA_NORMAL], ["apply_id" => $data["apply_id"], DATA_STATUS_NAME => DATA_DISABLE]);
        };

        $result = closure_list_exe([$fn]);
        //        $url    = url('DeliveryApply/billlist');

        $result && action_log('审核', '要货申请单单据，单据编号：'.$deliveryApply['sn']);

        return $result ? [RESULT_SUCCESS, '审核成功']: [RESULT_ERROR, $this->modelDeliveryApplyLog->getError()];
    }


}
