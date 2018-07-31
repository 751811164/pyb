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
class ShopPriceLog extends AdminBase
{

    /**
     * 信息
     */
    public function getLogInfo($where = [], $field = true)
    {

        return $this->modelShopPriceLog->getInfo($where, $field);
    }

    /**
     * 列表
     */
    public function getLogList($where = [], $field = "*", $order = 'pl.id asc', $paginate = 0, $join = [])
    {
        $this->modelShopPriceLog->alias("pl");
        if (empty($where["pl.".DATA_STATUS_NAME]) && empty($where[DATA_STATUS_NAME]))
        {
            $where["pl.".DATA_STATUS_NAME] = ["neq", DATA_DELETE];
        }
        if (empty($join))
        {
            $join = [
                //            [SYS_DB_PREFIX.'shop s', 's.id = pl.shop_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods g', 'g.id = pl.goods_id', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],
            ];
        }

        return $this->modelShopPriceLog->getList($where, $field, $order, $paginate, $join);
    }


    /**
     * 获取某个列的数组
     */
    public function getLogColumn($where = [], $field = '', $key = '')
    {
        return $this->modelShopPriceLog->getColumn($where, $field, $key);
    }

    /**
     * 查询条件
     */
    public function getWhere($data = [])
    {


    }


    public function getGoodsChooseWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['gf.name|gf.barcode1'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['cid']) && $where['c.id'] = $data['cid'];
        !empty($data['ids']) && $where["g.id"] = ["not in",$data['ids']];

        return $where;
    }


    /**
     * 信息编辑
     */
    public function logEdit($data = [])
    {

                $validate_result = $this->validateShopPriceLog->scene('edit')->check($data);

                if (!$validate_result) {

                    return [RESULT_ERROR, $this->validateShopPriceLog->getError()];
                }
        //        1:审核过的单据不能再次编辑
        if (!empty($data["bill_id"]))
        {
            $bill = $this->modelShopPriceBill->getInfo(["id" => $data["bill_id"]]);
            if (!empty($bill) && $bill["status"] == 1)
            {
                return [RESULT_ERROR, "已审核过的单据不能再次修改"];
            }
        }


        $fn          = function() use ($data) {

            if (empty($data["bill_id"]))
            {
                $data["sn"]           = "sp".date("YmdHis");
                $data["admin_add_id"] = ADMIN_ID;
            }

            $logs  = [];
            $count = 1;
            foreach ($data as $name => $item)
            {
                if (is_array($item))
                {

                    foreach ($item as $k => $val)
                    {
                        if (is_array($val))
                        {
                            //会员价处理
                            if (strrchr($name, "_id") == "_id")
                            {
                                $grade_id = $name;
                                $price    = str_replace("_grade_id", "_price", $name);
                                $index    = intval(count($item) / $count);

                                //会员价[grade_id=>id,price=>price]
                                $arr                                 = ["grade_id" => $val[0], "price" => $data[$price][$k][0]];
                                $logs[intval($k / $index)][$price][] = $arr;
                            }
                        }
                        else
                        {
                            if ($name == "id" && $val == "")
                            {
                                $logs[$k][$name] = null;
                            }
                            else
                            {
                                $logs[$k][$name] = $val;
                            }
                            $count = count($item) > 1 ? count($item): $count;
                        }
                    }
                }
            }

            //保存订单(判断新增或编辑)
            $data["id"] = empty($data["bill_id"]) ? null: $data["bill_id"];
            $this->modelShopPriceBill->setInfo($data);

            //保存记录
            empty($data["bill_id"]) && $data["bill_id"] = $this->modelShopPriceBill->getLastInsID();
            foreach ($logs as $key => $log)
            {
                $logs[$key]["bill_id"] = $data["bill_id"];
            }

            $result = $this->modelShopPriceLog->setList($logs, true);
        };
        $result      = closure_list_exe([$fn]);
        $handle_text = empty($data['bill_id']) ? '新增': '编辑';
        $url         = url('shopPriceBill/billlist');
        $result && action_log($handle_text, ''.$handle_text.'，调价单据：'.$data['bill_id']."机构：".$data["shop_names"]);

        return $result ? [RESULT_SUCCESS, '操作成功', $url]: [RESULT_ERROR, $this->modelShopPriceLog->getError()];
    }


    /**
     * 删除
     */
    public function logDel($where = [])
    {
        $bill = $this->modelShopPriceBill->getInfo(["id" => $where["bill_id"]]);
        if (!empty($bill) && $bill["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次修改"];
        }

        $result = $this->modelShopPriceLog->deleteInfo($where);

        $result && action_log('删除', '删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功', '']: [RESULT_ERROR, $this->modelShopPriceLog->getError()];
    }

    /**
     * 审核
     */
    public function checking($data = [])
    {
        $bill = $this->modelShopPriceBill->getInfo(["id" => $data["bill_id"]]);
        if (!empty($bill) && $bill["status"] == 1)
        {
            return [RESULT_ERROR, "已审核过的单据不能再次审核"];
        }
        $profile_ids = [];
        $shop_ids    = explode(",", $bill["shop_ids"]);
        $shops       = $this->logicShop->getSimpleShopList(['id' => ['in', $bill["shop_ids"]]], 's.id,s.type_id');
        $list        = [];
        //此处 连两次profile表 获取单品和多包装profile_id,且计算多包装的进货价
        $join = [
            //                [SYS_DB_PREFIX.'shop s', 's.id = pl.shop_id', 'LEFT'],
            [SYS_DB_PREFIX.'goods g', 'g.id = pl.goods_id', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile gf', 'g.id = gf.goods_id and gf.kind=0', 'LEFT'],//单品
            [SYS_DB_PREFIX.'goods_profile pgf', 'g.id = pgf.goods_id and pgf.kind=1', 'LEFT'],//多包装
            [SYS_DB_PREFIX.'goods_group pgg', 'pgf.id = pgg.profile_id', 'LEFT'],//多包装数量
        ];

        $logList = $this->getLogList(["pl.".DATA_STATUS_NAME => DATA_NORMAL, "bill_id" => $data["bill_id"]], "pl.*, gf.id single_profile_id, pgf.id pack_profile_id, pgg.num", '', false, $join);
        foreach ($shops as $key => $shop)
        {
            foreach ($logList as $index => $log)
            {
                //首次记录profile_ids,避免重复,以备后续删除
                if ($key == 0)
                {
                    array_push($profile_ids, $log["single_profile_id"]);
                    array_push($profile_ids, $log["pack_profile_id"]);
                }

                //如果是总部门店,直接改商品原价,否则更改门店价
                if ($shop['type_id'] == HEAD_OFFICE)
                {
                    $shop['id'] = SHOP_ID;
                }

                $i = count($shop_ids) * count($logList) * $key + $index * 2;
                //计算成本价,分单品和多包装
                //单品
                $list[$i]["shop_id"]         = $shop['id'];
                $list[$i]["profile_id"]      = $log["single_profile_id"];
                $list[$i]["cost_price"]      = $log["alert_single_cost_price"] ?: $log["single_cost_price"];
                $list[$i]["retail_price"]    = $log["alert_single_retail_price"] ?: $log["single_retail_price"];
                $list[$i]["wholesale_price"] = $log["alert_single_wholesale_price"] ?: $log["single_wholesale_price"];
                $list[$i]["delivery_price"]  = $log["alert_single_delivery_price"] ?: $log["single_delivery_pric"];
                $list[$i]["member_price"]    = $log["alert_single_member_price"] ?: $log["single_member_price"];
                $list[$i]["member_price1"]   = $log["alert_single_member_price"][0]["price"] ?: $log["single_member_price"][0]["price"];


                //多品
                if (!empty($log["pack_profile_id"]))
                {
                    $list[$i + 1]["shop_id"]         = $shop['id'];
                    $list[$i + 1]["profile_id"]      = $log["pack_profile_id"];
                    $list[$i + 1]["cost_price"]      = $log["alert_single_cost_price"] ? $log["alert_single_cost_price"] * $log["num"]: $log["single_cost_price"] * $log["num"];
                    $list[$i + 1]["retail_price"]    = $log["alert_pack_retail_price"] ?: $log["pack_retail_price"];
                    $list[$i + 1]["wholesale_price"] = $log["alert_pack_wholesale_price"] ?: $log["pack_wholesale_price"];
                    $list[$i + 1]["delivery_price"]  = $log["alert_pack_delivery_price"] ?: $log["pack_delivery_price"];
                    $list[$i + 1]["member_price"]    = $log["alert_pack_member_price"] ?: $log["pack_member_price"];
                    $list[$i + 1]["member_price1"]   = $log["alert_pack_member_price"][0]["price"] ?: $log["pack_member_price"][0]["price"];
                }

            }
        }
        $fn = function() use ($data, $list, $profile_ids) {
            //保存单据
            $this->modelShopPriceBill->setInfo([DATA_STATUS_NAME => DATA_NORMAL, "check_time" => date('Y-m-s H:i:s', time()), "admin_check_id" => ADMIN_ID], ["id" => $data["bill_id"]]);

            //删除原有商品的店铺价格
            $this->modelGoodsPrice->deleteInfo(["profile_id" => ["in", $profile_ids]], true);
            //保存调整后的价格
            $this->modelGoodsPrice->setList($list);

        };

        $result = closure_list_exe([$fn]);

        $result && action_log('审核', '审核单据，单据编号：'.$bill['sn']);

        return $result ? [RESULT_SUCCESS, '审核成功']: [RESULT_ERROR, $this->modelShopPriceLog->getError()];
    }


}
