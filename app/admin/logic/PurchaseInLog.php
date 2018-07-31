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
class PurchaseInLog extends AdminBase
{

    //    /**
    //     * 信息
    //     */
    //    public function getLogInfo($where = [], $field = true)
    //    {
    //
    //        return $this->modelPurchaseInLog->getInfo($where, $field);
    //    }
    /**
     * 列表
     */
    public function getLogList($where = [], $field = "*", $order = '', $paginate = 0, $join = [])
    {
        $this->modelPurchaseInLog->alias("pl");
        if (empty($where["pl.".DATA_STATUS_NAME]) && empty($where[DATA_STATUS_NAME]))
        {
            $where["pl.".DATA_STATUS_NAME] = ["neq", DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'purchase_in pi', 'pi.id = pl.in_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods g', 'g.id = pl.goods_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],
            ];
        }

        return $this->modelPurchaseInLog->getList($where, $field, $order, $paginate, $join);
    }


    /**
     * 导出单据列表
     */
    public function exportLogList($where = [], $field = 'barcode,name,unit,specification,num,cost_price,num*cost_price total_cost_price,retail_price,num*retail_price total_retail_price,produce_date,remark', $order = 'pl.id')
    {

        $list = $this->getLogList($where, $field, $order, false);

        $titles = "条码,商品名称,单位,规格,数量,进价,金额,售价,售价金额.生产日期,备注";
        $keys   = "barcode,name,unit,specification,num,cost_price,total_cost_price,retail_price,total_retail_price,produce_date,remark";

        action_log('导出', '导出采购入库明细表');

        export_excel($titles, $keys, $list, '采购订入库明细表'.date("Y-m-d"));
    }


    /**
     * 查询条件
     */
    public function getWhere($data = [])
    {
        $where          = [];
        $where['in_id'] = !empty($data["in_id"]) ? $data["in_id"]: 0;
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
    public function existBarcode($data = [])
    {
        $where['gb.barcode']  = $data['barcode'];
        $where['gf.goods_id'] = $data['gid'];
        $barcodeInfo          = $this->logicGoodsBarcode->getBarcodeJoinInfo($where, 'gb.id');
        return $barcodeInfo ? [RESULT_SUCCESS, '操作成功', '']: [RESULT_ERROR, '条码不存在'];
    }


    /**
     * 信息编辑
     */
    public function logEdit($data = [])
    {

        $validate_result = $this->validatePurchaseInLog->scene('edit')->check($data);

        if (!$validate_result)
        {

            return [RESULT_ERROR, $this->validatePurchaseInLog->getError()];
        }


        //        1:审核过的单据不能再次编辑
        if (!empty($data["in_id"]))
        {
            $billIn = $this->modelPurchaseIn->getInfo(["id" => $data["in_id"]]);
            if (!empty($billIn) && $billIn["status"] == 1)
            {
                return [RESULT_ERROR, "已审核过的单据不能再次修改"];
            }
        }


        $fn     = function() use ($data) {

            if (empty($data["in_id"]))
            {
                $data["sn"]           = "PI".date("YmdHis");
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
            foreach ($data['cost_price'] as $key => $value)
            {
                $data['amount'] += $value * $data['num'][$key];
            }


            //保存入库订单(判断新增或编辑)
            $data["id"] = empty($data["in_id"]) ? null: $data["in_id"];
            $this->modelPurchaseIn->setInfo($data);

            //保存入库记录明细
            empty($data["in_id"]) && $data["in_id"] = $this->modelPurchaseIn->getLastInsID();
            foreach ($logs as $key => $log)
            {
                $logs[$key]["in_id"]          = $data["in_id"];
                $logs[$key][DATA_STATUS_NAME] = DATA_DISABLE;
            }

            $this->modelPurchaseInLog->setInfo([DATA_STATUS_NAME => DATA_DELETE], ["in_id" => $data["in_id"]]);

            $this->modelPurchaseInLog->setList($logs, true);
            return intval($data['in_id']);
        };
        $result = closure_list_exe([$fn]);

        $handle_text = empty($data['in_id']) ? '新增': '编辑';
        //查询是否直接审核
        $shopPermission = $this->logicShopPermission->getPermissionInfo(['shop_id' => $data['shop_id']]);
        if ($shopPermission && $shopPermission['save_bills_check'] == 1)
        {
            $data['in_id'] = $result[0];
        }

        $url = url('PurchaseIn/billlist');
        $result && action_log($handle_text, $handle_text.'，采购订单：'.$result[0]);

        return $result ? [RESULT_SUCCESS, '操作成功', $url, $data['in_id']]: [RESULT_ERROR, $this->modelPurchaseInLog->getError()];
    }


    /**
     * 删除
     */
    public function logDel($where = [])
    {
        $bill = $this->modelPurchaseIn->getInfo(["id" => $where["in_id"]]);
        if (!empty($bill) && $bill["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次修改"];
        }

        $result = $this->modelPurchaseInLog->deleteInfo($where);

        $result && action_log('删除', '删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功', '']: [RESULT_ERROR, $this->modelPurchaseInLog->getError()];
    }

    /**
     * 审核
     * 判断入库单状态,
     * 判断采购订单状态,
     * 通过入库商品明细判断采购订单是否完全使用,
     * 更新入库单审核状态
     * 更新入库商品明细
     * 更新采购订单使用状态
     * 更新价格
     * 更新门店库存
     */
    public function checking($data = [])
    {
        //获取入库单据信息
        $inBillInfo = $this->modelPurchaseIn->getInfo(["id" => $data["in_id"]]);
        if (empty($inBillInfo))
        {
            return [RESULT_ERROR, "请先保存单据后再审核"];
        }
        elseif (!empty($inBillInfo) && $inBillInfo["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次审核"];
        }


        //判断采购订单是否使用过
        if (!empty($inBillInfo['bill_id']))
        {
            $billInfo = $this->modelPurchaseBill->getInfo(['id' => $inBillInfo['bill_id']]);
            if ($billInfo)
            {
                if ($billInfo[DATA_STATUS_NAME] != 1)
                {
                    return [RESULT_ERROR, "采购订单未审核"];
                }
                elseif ($billInfo['use_status'] != 0)
                {
                    return [RESULT_ERROR, "采购订单已被使用或终止"];
                }
            }
        }

        $use_status = 0;
        $count      = 0;
        $sqls       = ['updatePriceSql' => '', 'updateStockSql' => ''];


        //获取店铺类型
        $shop = $this->logicShop->getShopInfo(['id' => $inBillInfo['shop_id']]);
        if ($shop['type_id'] == HEAD_OFFICE)
        {
            $shop_id = SHOP_ID;
        }
        else
        {
            $shop_id = $shop['id'];
        }

        $profile_ids            = $barcodes = [];
        $sqls['updatePriceSql'] .= 'update ob_goods_price set cost_price = case profile_id ';
        $sqls['updateStockSql'] .= ' INSERT INTO `ob_goods_barcode_stock` ( `shop_id`,`goods_id`,`supplier_id`,`barcode`,`stock_actual`,`produce_date`,`create_time`,`update_time`) VALUES ';

        //获取入库商品明细
        $inLogList = $this->getLogList(["pl.".DATA_STATUS_NAME => DATA_DISABLE, "in_id" => $inBillInfo["id"]], "pl.*,gf.id profile_id,pi.shop_id,pi.supplier_id", '', false);
        if (empty($inLogList))
        {
            return [RESULT_ERROR, "请先保存已选择的商品"];
        }
        $time      = time();
        foreach ($inLogList as $item)
        {
            array_push($barcodes, $item['barcode']);

            //拼接 更改商品价格sql
            if (!in_array($item['profile_id'], $profile_ids))
            {
                $sqls['updatePriceSql'] .= ' when '.$item['profile_id'].' then '.$item['cost_price'];
                array_push($profile_ids, $item['profile_id']);
            }
            //拼接 更新商品库存sql
            $produce_date           = strtotime($item['produce_date']);
            $sqls['updateStockSql'] .= " ({$item['shop_id']},{$item['goods_id']},{$item['supplier_id']},{$item['barcode']},{$item['num']}, $produce_date,{$time},{$time}), ";
        }
        $sqls['updatePriceSql'] .= " end where profile_id in ( ".join(',', $profile_ids).") and shop_id=".$shop_id;
        $sqls['updateStockSql'] = trim($sqls['updateStockSql'], ' ,').' ON DUPLICATE KEY UPDATE `stock_actual` = `stock_actual` + VALUES(`stock_actual`),`supplier_id`=VALUES(`supplier_id`)';


        //获取采购订单商品明细信息
        $billLogList = $this->modelPurchaseBillLog->getList([DATA_STATUS_NAME => DATA_NORMAL, "bill_id" => $inBillInfo["bill_id"]], true, '', false);
        //判断采购订单使用状态
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


        //更新门店商品分类查看范围
        $category_ids=[];

        $join = [
            [SYS_DB_PREFIX.'goods g', 'g.id = gf.goods_id', 'LEFT'],
        ];
        $profiles= $this->logicGoodsProfile->getSimpleProfileList(['gf.id'=>['in',$profile_ids]],'category_id','',false,$join);
        foreach ($profiles as $key=>$profile)
        {
           array_push($category_ids,(string)$profile['category_id']);
        }

        $shop_permission = $this->modelShopPermission->getInfo(['shop_id'=>$shop['id']]);
        if ($shop_permission)
        {
            $shop_permission->category_ids= array_unique(array_merge($shop_permission->category_ids,$category_ids));
            $shop_permission->save();
        }


        $fn = function() use ($data, $inBillInfo, $use_status, $sqls) {
            //保存入库单据
            $this->modelPurchaseIn->setInfo([DATA_STATUS_NAME => DATA_NORMAL, "check_time" => date('Y-m-d H:i:s', time()), "admin_check_id" => ADMIN_ID], ["id" => $data["in_id"]]);

            //更新入库商品明细
            $this->modelPurchaseInLog->setInfo([DATA_STATUS_NAME => DATA_NORMAL], ["in_id" => $data["in_id"], DATA_STATUS_NAME => DATA_DISABLE]);

            //更改采购订单使用状态
            if (!empty($inBillInfo['bill_id']) && $use_status > 0)
            {
                $this->modelPurchaseBill->setInfo(["use_status" => $use_status], ["id" => $inBillInfo["bill_id"]]);

            }

            //更改商品价格
            $this->execute($sqls['updatePriceSql']);
            //更改每种条码库存数量
            $this->execute($sqls['updateStockSql']);


        };

        $result = closure_list_exe([$fn]);
        //        $url    = url('PurchaseIn/billlist');

        $result && action_log('审核', '审核单据，单据编号：'.$inBillInfo['sn']);

        return $result ? [RESULT_SUCCESS, '审核成功']: [RESULT_ERROR, $this->modelPurchaseInLog->getError()];
    }

    //    //反审核
    //    public function unchecking($data=[]){
    //        $bill=  $this->modelPurchaseIn->getInfo(["id"=>$data["in_id"]]);
    //        if (empty($bill))
    //        {
    //            return [RESULT_ERROR, "单据未保存"];
    //        }
    //
    //        $date = getdate();
    //        if (!empty($bill)&&$bill["status"]!=1)
    //        {
    //            return [RESULT_ERROR, "单据未审核"];
    //        }
    //        elseif (strtotime($bill["check_time"])<mktime(0,0,0,$date['mon'],$date['mday'],$date['year']))
    //        {
    //            return [RESULT_ERROR, "只能反审核当天审核的采购订单"];
    //        }
    //
    //
    //        $fn=function()use($data,$bill){
    //            //保存单据
    //            $this->modelPurchaseIn->setInfo([DATA_STATUS_NAME=>DATA_DISABLE,"check_time"=>'',"admin_check_id"=>''],["id"=>$data["bill_id"]]);
    //        };
    //
    //        $result=closure_list_exe([$fn]);
    //
    //        $result && action_log('审核',  '审核单据，单据编号：' . $bill['sn']);
    //
    //        return $result ? [RESULT_SUCCESS, '审核成功'] : [RESULT_ERROR, $this->modelPurchaseInLog->getError()];
    //    }


}
