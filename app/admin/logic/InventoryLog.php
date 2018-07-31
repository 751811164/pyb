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
class InventoryLog extends AdminBase
{

    //    /**
    //     * 信息
    //     */
    //    public function getLogInfo($where = [], $field = true)
    //    {
    //
    //        return $this->modelInventoryLog->getInfo($where, $field);
    //    }

    public function getLogColumn($where = [], $field = '', $key = '', $limit = null)
    {
        $where[DATA_STATUS_NAME] = ["neq", DATA_DELETE];
        return $this->modelInventoryLog->getColumn($where, $field, $key, $limit);
    }

    /**
     * 列表
     */
    public function getLogList($where = [], $field = "*", $order = '', $paginate = 0, $join = [])
    {
        $this->modelInventoryLog->alias("il");
        if (empty($where["il.".DATA_STATUS_NAME]) && empty($where[DATA_STATUS_NAME]))
        {
            $where["il.".DATA_STATUS_NAME] = ["neq", DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'inventory i', 'i.id = il.inventory_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods g', 'g.id = il.goods_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],
            ];
        }

        return $this->modelInventoryLog->getList($where, $field, $order, $paginate, $join);
    }


    /**
     * 导出单据列表
     */
    public function exportLogList($where = [], $field = 'barcode,name,unit,specification,stock_actual,num,retail_price,retail_price*num total_retail_price,color,size', $order = 'il.id')
    {

        $list = $this->getLogList($where, $field, $order, false);

        $titles = "条码,商品名称,单位,规格,库存数量,实盘数量,售价,售价金额,花色,尺码";
        $keys   = "barcode,name,unit,specification,stock_actual,num,retail_price,total_retail_price,color,size";

        action_log('导出', '导出存货盘点明细表');

        export_excel($titles, $keys, $list, '存货盘点明细表'.date("Y-m-d"));
    }


    /**
     * 查询条件
     */
    public function getWhere($data = [])
    {
        $where                 = [];
        $where['inventory_id'] = !empty($data["inventory_id"]) ? $data["inventory_id"]: 0;
        return $where;


    }


    public function getGoodsChooseWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['gf.name|gf.barcode1'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['cid']) && $where['c.id'] = $data['cid'];
        //        !empty($data['supilier_id']) && $where['g.supilier_id'] = $data['supilier_id'];
        !empty($data['barcodes']) && $where["gb.barcode"] = ["not in", $data['barcodes']];
        !empty($data['shop_id']) && $where['gp.shop_id'] = $where['pgp.shop_id'] = $data['shop_id'];
        return $where;
    }

    //    /**
    //     * 验证条码是否存在
    //     */
    //    public function existBarcode($data = [])
    //    {
    //        $where['gb.barcode']  = $data['barcode'];
    //        $where['gf.goods_id'] = $data['gid'];
    //        $barcodeInfo          = $this->logicGoodsBarcode->getBarcodeJoinInfo($where, 'gb.id');
    //        return $barcodeInfo ? [RESULT_SUCCESS, '操作成功', '']: [RESULT_ERROR, '条码不存在'];
    //    }


    /**
     * 信息编辑
     */
    public function logEdit($data = [])
    {

        $validate_result = $this->validateInventoryLog->scene('edit')->check($data);

        if (!$validate_result)
        {

            return [RESULT_ERROR, $this->validateInventoryLog->getError()];
        }


        //        1:审核过的单据不能再次编辑
        if (!empty($data["inventory_id"]))
        {
            $inventory = $this->modelInventory->getInfo(["id" => $data["inventory_id"]]);
            if (!empty($inventory) && $inventory["status"] == 1)
            {
                return [RESULT_ERROR, "已审核过的单据不能再次修改"];
            }
        }

        if (empty($data["inventory_id"]))
        {
            $data["sn"]           = "IV".date("YmdHis");
            $data["admin_add_id"] = ADMIN_ID;
        }
        $fn     = function() use ($data) {


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

            //保存入库订单(判断新增或编辑)
            $data["id"] = empty($data["inventory_id"]) ? null: $data["inventory_id"];
            $this->modelInventory->setInfo($data);

            //保存入库记录明细
            empty($data["inventory_id"]) && $data["inventory_id"] = $this->modelInventory->getLastInsID();
            foreach ($logs as $key => $log)
            {
                $logs[$key]["inventory_id"]   = $data["inventory_id"];
                $logs[$key][DATA_STATUS_NAME] = DATA_DISABLE;
            }

            $this->modelInventoryLog->setInfo([DATA_STATUS_NAME => DATA_DELETE], ["inventory_id" => $data["inventory_id"]]);

            $this->modelInventoryLog->setList($logs, true);
            return intval($data['inventory_id']);
        };
        $result = closure_list_exe([$fn]);

        $handle_text = empty($data['inventory_id']) ? '新增': '编辑';
        //查询是否直接审核
        $shopPermission = $this->logicShopPermission->getPermissionInfo(['shop_id' => $data['shop_id']]);
        if ($shopPermission && $shopPermission['save_bills_check'] == 1)
        {
            $inventory_id = $result[0];
        }
        else
        {
            $inventory_id = '';
        }

        $url = url('Inventory/inventorylist');
        $result && action_log($handle_text, $handle_text.'，盘点单据：'.$result[0]);

        return $result ? [RESULT_SUCCESS, '操作成功', $url, $inventory_id]: [RESULT_ERROR, $this->modelInventoryLog->getError()];
    }

    /**
     * 审核
     */
    public function checking($data = [])
    {
        //获取入库单据信息
        $inventory = $this->modelInventory->getInfo(["id" => $data["inventory_id"]]);
        if (empty($inventory))
        {
            return [RESULT_ERROR, "请先保存单据后再审核"];
        }
        elseif (!empty($inventory) && $inventory["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次审核"];
        }


        $sqls                   = $barcodes = [];
        $sqls['updateStockSql'] = ' INSERT INTO `ob_goods_barcode_stock` ( `shop_id`,`goods_id`,`barcode`,`stock_actual`,`create_time`,`update_time`) VALUES ';

        //获取商品明细
        $logList = $this->getLogList(["il.".DATA_STATUS_NAME => DATA_DISABLE, "inventory_id" => $inventory["id"]], "il.*,gf.id profile_id,i.shop_id", '', false);
        if (empty($logList))
        {
            return [RESULT_ERROR, "请先保存已选择的商品"];
        }

        $time = time();
        foreach ($logList as $item)
        {
            array_push($barcodes, $item['barcode']);

            //拼接 更新商品库存sql
            $sqls['updateStockSql'] .= " ({$item['shop_id']},{$item['goods_id']},{$item['barcode']},{$item['num']},{$time},{$time}), ";
        }
        //        $sqls['updatePriceSql'] .= " end where profile_id in ( ".join(',', $profile_ids).") and shop_id=".$shop_id;
        $sqls['updateStockSql'] = trim($sqls['updateStockSql'], ' ,').' ON DUPLICATE KEY UPDATE `stock_actual` =  VALUES(`stock_actual`)';


        $fn = function() use ($data, $inventory, $sqls) {
            //保存单据
            $this->modelInventory->setInfo([DATA_STATUS_NAME => DATA_NORMAL, "check_time" => date('Y-m-d H:i:s', time()), "admin_check_id" => ADMIN_ID], ["id" => $data["inventory_id"]]);
            //更新商品明细
            $this->modelInventoryLog->setInfo([DATA_STATUS_NAME => DATA_NORMAL], ["inventory_id" => $data["inventory_id"], DATA_STATUS_NAME => DATA_DISABLE]);
            //更改每种条码库存数量
            $this->execute($sqls['updateStockSql']);
        };

        $result = closure_list_exe([$fn]);

        $result && action_log('审核', '审核单据，单据编号：'.$inventory['sn']);

        return $result ? [RESULT_SUCCESS, '审核成功']: [RESULT_ERROR, $this->modelInventoryLog->getError()];
    }


    /**
     * 批量审核
     */
    public function multichecking($data=[]){

        //获取所有单据信息
        $data["ids"] = $this->modelInventory->getColumn(["id" => ['in',$data["ids"]],DATA_STATUS_NAME=>DATA_DISABLE ],'id');
        if (empty($data["ids"]))
        {
            return [RESULT_ERROR, "暂无未审核单据"];
        }



        $sqls                   = $barcodes = [];
        $sqls['updateStockSql'] = ' INSERT INTO `ob_goods_barcode_stock` ( `shop_id`,`goods_id`,`barcode`,`stock_actual`,`create_time`,`update_time`) VALUES ';

        //获取商品明细
        $logList = $this->getLogList(["il.".DATA_STATUS_NAME => DATA_DISABLE, "inventory_id" =>['in',$data["ids"]] ], "il.*,gf.id profile_id,i.shop_id", '', false);
        if (empty($logList))
        {
            return [RESULT_ERROR, "无盘点的商品"];
        }

        $time = time();
        foreach ($logList as $item)
        {
            array_push($barcodes, $item['barcode']);

            //拼接 更新商品库存sql
            $sqls['updateStockSql'] .= " ({$item['shop_id']},{$item['goods_id']},{$item['barcode']},{$item['num']},{$time},{$time}), ";
        }
        //        $sqls['updatePriceSql'] .= " end where profile_id in ( ".join(',', $profile_ids).") and shop_id=".$shop_id;
        $sqls['updateStockSql'] = trim($sqls['updateStockSql'], ' ,').' ON DUPLICATE KEY UPDATE `stock_actual` =  VALUES(`stock_actual`)';


        $fn = function() use ($data, $sqls) {
            //保存单据
            $this->modelInventory->setInfo([DATA_STATUS_NAME => DATA_NORMAL, "check_time" => date('Y-m-d H:i:s', time()), "admin_check_id" => ADMIN_ID], ["id" => ['in',$data["ids"]]]);
            //更新商品明细
            $this->modelInventoryLog->setInfo([DATA_STATUS_NAME => DATA_NORMAL], ["inventory_id" =>  ['in',$data["ids"]], DATA_STATUS_NAME => DATA_DISABLE]);
            //更改每种条码库存数量
            $this->execute($sqls['updateStockSql']);
        };

        $result = closure_list_exe([$fn]);

        $result && action_log('审核', '审核单据，单据ids：'. join(',',$data['ids']));

        return $result ? [RESULT_SUCCESS, '审核成功']: [RESULT_ERROR, $this->modelInventoryLog->getError()];
    }





    /**
     * 删除
     */
    public function logDel($where = [])
    {
        $inventory = $this->modelInventory->getInfo(["id" => $where["inventory_id"]]);
        if (!empty($inventory) && $inventory["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次修改"];
        }

        $result = $this->modelInventoryLog->deleteInfo($where);

        $result && action_log('删除', '删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功', '']: [RESULT_ERROR, $this->modelInventoryLog->getError()];
    }

    /**
     * 复审核
     */
    public function rechecking($data=[])
    {
        $result = $this->logSave($data);
        $result2 = $this->logCheck(['inventory_id'=>$result[0]]);

        $handle_text='复盘';
        $result[0] && action_log($handle_text, $handle_text.'，盘点单据：'.$result[0]);
        $url =url('inventory/inventorylist');
        return $result ? [RESULT_SUCCESS, '操作成功',$url]: [RESULT_ERROR, $this->modelInventoryLog->getError()];
    }


    /**
     * 保存
     */
    protected function logSave($data = [])
    {
        //        1:审核过的单据不能再次编辑
        if (!empty($data["inventory_id"]))
        {
            $inventory = $this->modelInventory->getInfo(["id" => $data["inventory_id"]]);
            if (!empty($inventory) && $inventory["status"] == 1)
            {
                return [RESULT_ERROR, "已审核过的单据不能再次修改"];
            }
        }

        if (empty($data["inventory_id"]))
        {
            $data["sn"]           = "IV".date("YmdHis");
            $data["admin_add_id"] = ADMIN_ID;
        }
        $fn = function() use ($data) {


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

            //保存入库订单(判断新增或编辑)
            $data["id"] = empty($data["inventory_id"]) ? null: $data["inventory_id"];
            $this->modelInventory->setInfo($data);

            //保存入库记录明细
            empty($data["inventory_id"]) && $data["inventory_id"] = $this->modelInventory->getLastInsID();
            foreach ($logs as $key => $log)
            {
                $logs[$key]["inventory_id"]   = $data["inventory_id"];
                $logs[$key][DATA_STATUS_NAME] = DATA_DISABLE;
            }

            $this->modelInventoryLog->setInfo([DATA_STATUS_NAME => DATA_DELETE], ["inventory_id" => $data["inventory_id"]]);

            $this->modelInventoryLog->setList($logs, true);
            return intval($data['inventory_id']);
        };
        return $result = closure_list_exe([$fn]);
    }

    /**
     * 审核
     */
    protected function logCheck($data = [])
    {

        //获取入库单据信息
        $inventory = $this->modelInventory->getInfo(["id" => $data["inventory_id"]]);
        if (empty($inventory))
        {
            return [RESULT_ERROR, "请先保存单据后再审核"];
        }
        elseif (!empty($inventory) && $inventory["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次审核"];
        }

        $sqls                   = $barcodes = [];
        $sqls['updateStockSql'] = ' INSERT INTO `ob_goods_barcode_stock` ( `shop_id`,`goods_id`,`barcode`,`stock_actual`,`create_time`,`update_time`) VALUES ';

        //获取商品明细
        $logList = $this->getLogList(["il.".DATA_STATUS_NAME => DATA_DISABLE, "inventory_id" => $inventory["id"]], "il.*,gf.id profile_id,i.shop_id", '', false);
        if (empty($logList))
        {
            return [RESULT_ERROR, "请先保存已选择的商品"];
        }

        $time = time();
        foreach ($logList as $item)
        {
            array_push($barcodes, $item['barcode']);

            //拼接 更新商品库存sql
            $sqls['updateStockSql'] .= " ({$item['shop_id']},{$item['goods_id']},{$item['barcode']},{$item['num']},{$time},{$time}), ";
        }
        $sqls['updateStockSql'] = trim($sqls['updateStockSql'], ' ,').' ON DUPLICATE KEY UPDATE `stock_actual` =  VALUES(`stock_actual`)';


        $fn = function() use ($data, $inventory, $sqls) {
            //保存单据
            $this->modelInventory->setInfo([DATA_STATUS_NAME => DATA_NORMAL, "check_time" => date('Y-m-d H:i:s', time()), "admin_check_id" => ADMIN_ID], ["id" => $data["inventory_id"]]);
            //更新商品明细
            $this->modelInventoryLog->setInfo([DATA_STATUS_NAME => DATA_NORMAL], ["inventory_id" => $data["inventory_id"], DATA_STATUS_NAME => DATA_DISABLE]);
            //更改每种条码库存数量
            $this->execute($sqls['updateStockSql']);
        };

        return $result = closure_list_exe([$fn]);


    }



}
