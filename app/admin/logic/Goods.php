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
 * 商品逻辑
 */
class Goods extends AdminBase
{

    #region/*------------------------------------单个商品-------------------------------------------*/
    /**
     * 获取商品信息
     */
    public function getGoodsInfo($where = [], $field = true)
    {

        return $this->modelGoods->getInfo($where, $field);
    }

    /**
     * 获取商品信息
     */
    public function getGoodsJoinInfo($where = [], $field = "*", $join = null)
    {

        if (!array_key_exists('g.'.DATA_STATUS_NAME, $where))
        {
            $where['g.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }

        if (!array_key_exists("gp.shop_id", $where))
        {
            $where["gp.shop_id"] = SHOP_ID;//默认价
        }

        if (!array_key_exists("gf.kind", $where))
        {
            $where["gf.kind"] = 0;//连接单品
        }

        $this->modelGoods->alias('g');
        if (!$join)
        {
            $join = [
                [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id ', 'LEFT'],
                [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id', 'LEFT'],
                [SYS_DB_PREFIX.'goods_group gg', 'gg.profile_id = gf.id ', 'LEFT'],

            ];
        }

        return $this->modelGoods->getInfo($where, $field, $join);
    }

    /**
     * 获取商品列表
     */
    public function getGoodsList($where = [], $field = 'gf.*,gp.*,g.*', $order = 'g.id desc', $pagenate = DB_LIST_ROWS, $join = [])
    {
        $this->modelGoods->alias('g');

        if (!array_key_exists('g.'.DATA_STATUS_NAME, $where))
        {
            $where['g.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }


        if (!array_key_exists("gf.kind", $where))
        {
            $where["gf.kind"] = 0;//连接单品
        }

        if (IS_ROOT)
        {
            //看商品档案价格
            if (!array_key_exists("gp.shop_id", $where))
            {
                $where["gp.shop_id"] = SHOP_ID;//默认价
            }
        }
        else
        {
            $shopPermission = $this->authShopPermission();
            //总部
            if ($shopPermission['type_id'] == HEAD_OFFICE)
            {
                //看商品档案价格
                if (!array_key_exists("gp.shop_id", $where))
                {
                    $where["gp.shop_id"] = SHOP_ID;//默认价
                }
            }
            elseif ($shopPermission['category_ids'])
            {
                //按设定的分类查看
                $category = json_decode($shopPermission['category_ids'], true);
                if (array_key_exists("c.id", $where))
                {
                    $where["c.id"] = [["eq", $where["c.id"]], ["in", $category]];
                }
                else
                {
                    $where["c.id"] = ["in", $category];
                }
                $where["gp.shop_id"] = $shopPermission['shop_id'];
            }
            else
            {
                $where["c.id"] = 0;
            }

        }


        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id ', 'LEFT'],
                [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id ', 'LEFT'],
                [SYS_DB_PREFIX.'goods_group gg', 'gg.profile_id = gf.id ', 'LEFT'],
                [SYS_DB_PREFIX.'goods_type gt', 'g.goods_type = gt.id and gt.status=1', 'LEFT'],
                [SYS_DB_PREFIX.'brand b', 'g.brand_id = b.id and b.status=1', 'LEFT'],
                [SYS_DB_PREFIX.'category c', 'g.category_id = c.id and c.status=1', 'LEFT'],
                [SYS_DB_PREFIX.'supplier s', 'g.supplier_id = s.id and s.status=1', 'LEFT'],
                [SYS_DB_PREFIX.'admin a', 'g.admin_add_id = a.id', 'LEFT'],
            ];
        }

        return $this->modelGoods->getList($where, $field, $order, $pagenate, $join);
        //        return $this->modelGoods->getList($where, $field, $order, DB_LIST_ROWS, $join);
    }


    /**
     * 获取商品列表
     */
    public function getSimpleGoodsList($where = [], $field = true, $order = 'g.id desc', $pagenate = DB_LIST_ROWS, $join = [])
    {
        $this->modelGoods->alias('g');
        if (!array_key_exists('g.'.DATA_STATUS_NAME, $where))
        {
            $where['g.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }


        if (IS_ROOT)
        {
            //看商品档案价格
            if (!array_key_exists("gp.shop_id", $where))
            {
                $where["gp.shop_id"] = SHOP_ID;//默认价
                $where["pgp.shop_id"] = SHOP_ID;//默认价
            }
        }
        else
        {
            $shopPermission = $this->authShopPermission();
            //总部
            if ($shopPermission['type_id'] == HEAD_OFFICE)
            {
                //                看商品档案价格
                if (!array_key_exists("gp.shop_id", $where))
                {
                    $where["gp.shop_id"] = SHOP_ID;//默认价
                    $where["pgp.shop_id"] = SHOP_ID;//默认价
                }
            }
            elseif ($shopPermission['category_ids'])
            {
                //按设定的分类查看
                $category = json_decode($shopPermission['category_ids'], true);
                if (array_key_exists("c.id", $where))
                {
                    $where["c.id"] = [["eq", $where["c.id"]], ["in", $category]];
                }
                else
                {
                    $where["c.id"] = ["in", $category];
                }
                $where["gp.shop_id"] = $shopPermission['shop_id'];//
                $where["pgp.shop_id"] = $shopPermission['shop_id'];//
            }
            else
            {
                $where["c.id"] = 0;
            }
        }
        return $this->modelGoods->getList($where, $field, $order, $pagenate, $join);
        //        return $this->modelGoods->getList($where, $field, $order, DB_LIST_ROWS, $join);
    }


    public function getNormalGoodsList($where = [], $field = true, $order = 'g.id desc', $pagenate = DB_LIST_ROWS, $join = []){
        $this->modelGoods->alias('g');
        if (!array_key_exists('g.'.DATA_STATUS_NAME, $where))
        {
            $where['g.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }
        return $this->modelGoods->getList($where, $field, $order, $pagenate, $join);
    }

    /**
     * 获取某个列的数组
     */
    public function getGoodsColumn($where = [], $field = '', $key = '')
    {
        return $this->modelGoods->getColumn($where, $field, $key);
    }


    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getWhere($data = [])
    {
        $where = [];

        !empty($data['search_data']) && $where['gf.name|b.name|c.name|gf.barcode1'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['cid']) && $where['c.id'] = $data['cid'];
        !empty($data['ids']) && $where['g.id'] = ["in", $data["ids"]];
        array_key_exists('status', $data) && $where['g.status'] = $data['status'];


        return $where;
    }


    /**
     * 商品信息编辑
     */
    public function goodsEdit($data = [])
    {
        $validate_result = $this->validateGoods->scene(empty($data["id"]) ? 'add': 'edit')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validateGoods->getError()]; endif;

        //        $data['content'] = html_entity_decode($data['content']);


        $single = ["kind" => 0];
        $pack   = ["kind" => 1];

        //拆分单品/多包装 成两条信息
        foreach ($data as $key => $value)
        {
            if (strpos($key, "single_") !== false)
            {

                if ($key == "single_id")
                {

                    if (!empty($value))
                    {
                        $single["id"] = $value;
                    }
                }
                else
                {
                    $single[str_replace("single_", "", $key)] = $value;
                }
            }
            elseif (strpos($key, "pack_") !== false)
            {

                if ($key == "pack_id")
                {
                    if (!empty($value))
                    {
                        $pack["id"] = $value;
                    }
                }
                else
                {
                    $pack[str_replace("pack_", "", $key)] = $value;
                }

            }
        }
        if (empty($data['id']))
        {
            //新增时计算价格

            //计算多包装成本价
            $pack["cost_price"] = $single["cost_price"] * $pack["num"];

            //会员价组合
            foreach ($single['member_grade_id'] as $key => $value)
            {

                $memberPrice[$key]['grade_id'] = $value;
                $memberPrice[$key]['price']    = $single['member_price'][$key] ?: $single['retail_price'];
                //            if ($key==0)
                //            {
                //                $data["member_price1"]=$memberPrice[$key]['price'];
                //            }
            }
            $data["single_member_price"] = $single["member_price"] = $memberPrice;//单个会员价组合


            foreach ($pack['member_grade_id'] as $key => $value)
            {
                $memberPrice1[$key]['grade_id'] = $value;
                $memberPrice1[$key]['price']    = $pack['member_price'][$key] ?: $pack['retail_price'];
            }
            $data["pack_member_price"] = $pack["member_price"] = $memberPrice1;//整装会员价组合

            $data['admin_add_id'] = ADMIN_ID;
        }
        else
        {
            $data['admin_edit_id'] = ADMIN_ID;
        }

        $goodsFn = function() use ($data) {
            return $result = $this->modelGoods->setInfo($data);
        };

        $profileList = [0 => $single, 1 => $pack];
        //保存profile信息
        $profileFn = function() use ($data, $profileList) {

            //新增时,关联goods_id
            if (empty($data["id"]))
            {
                $data["id"] = $this->modelGoods->getLastInsID();
            }
            foreach ($profileList as $key => $item)
            {
                $profileList[$key]["goods_id"] = $data["id"];
                $profileList[$key]["barcode1"] = $item["barcode"][0];
            }

            $result                 = $this->modelGoodsProfile->setList($profileList, true);
            $GLOBALS["profile_ids"] = $result;
            $GLOBALS["goods_id"]    = $data["id"];
            return $result;

        };

        //保存条形码
        $barcodesFn = function() use ($profileList) {

            //添加条形码
            $barcodeList = [];

            foreach ($profileList as $index => $profile)
            {
                foreach ($profile['barcode'] as $key => $value)
                {

                    $arr['barcode']    = $value;
                    $arr['profile_id'] = empty($profile['id']) ? $GLOBALS["profile_ids"][$index]['id']: $profile['id'];
                    //                    $arr['id']      =empty($profile['barcode_id'][$key])?null:$profile['barcode_id'][$key] ;

                    array_push($barcodeList, $arr);
                    unset($arr);
                }
                $profile_ids[] = empty($profile['id']) ? $GLOBALS["profile_ids"][$index]['id']: $profile['id'];
            }
            //删除原有条码信息
            $this->modelGoodsBarcode->deleteInfo(["profile_id" => ["in", $profile_ids]], false);

            return $result = $this->modelGoodsBarcode->setList($barcodeList, false);
        };


        //保存组合数量
        $groupFn = function() use ($profileList) {

            //数量
            $groupList = [];

            foreach ($profileList as $index => $profile)
            {
                $profile['num']        = array_key_exists("num", $profile) ? $profile["num"]: 1;
                $profile['profile_id'] = empty($profile['id']) ? $GLOBALS["profile_ids"][$index]['id']: $profile['id'];
                $profile['id']         = empty($profile['group_id']) ? null: $profile['group_id'];
                array_push($groupList, $profile);
            }

            return $result = $this->modelGoodsGroup->setList($groupList, true);
        };


        //保存价格
        $priceFn = function() use ($data, $profileList) {
            //新增时做价格保存,编辑时不变动
            if (empty($data['id']))
            {
                //价格
                $priceList = [];

                foreach ($profileList as $index => $profile)
                {
                    $profile['shop_id']       = SHOP_ID;
                    $profile['profile_id']    = empty($profile['id']) ? $GLOBALS["profile_ids"][$index]['id']: $profile['id'];
                    $profile['id']            = empty($profile['price_id']) ? null: $profile['price_id'];
                    $profile['member_price1'] = $profile["member_price"][0]["price"];
                    array_push($priceList, $profile);
                }

                return $result = $this->modelGoodsPrice->setList($priceList, true);
            }

        };

        //首次保存商品--供应商的价格
        $supplierPrice = function() use ($data) {
            if (!empty($data["id"]))
            {
                return false;
            }
            $data["goods_id"] = $GLOBALS["goods_id"];
            return $this->modelSupplierPrice->setInfo($data);
        };


        $result = closure_list_exe([$goodsFn, $profileFn, $barcodesFn, $groupFn, $priceFn, $supplierPrice]);

        $handle_text = empty($data['id']) ? '新增': '编辑';

        $result && action_log($handle_text, '商品'.$handle_text.'，单品：'.$data['single_name'].'，多包装：'.$data['pack_name']);

        return $result ? [RESULT_SUCCESS, '操作成功', '', $GLOBALS["goods_id"]]: [RESULT_ERROR, $this->modelGoods->getError()];
    }


    /**
     * 商品删除
     */
    public function goodsDel($where = [])
    {

        $result = $this->modelGoods->deleteInfo($where);

        $result && action_log('删除', '商品删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功']: [RESULT_ERROR, $this->modelGoods->getError()];
    }


    /**
     * 单个批量修改
     * @param array $data
     */
    public function batchSingleEdit($data = [])
    {

        $where["id"]           = ["in", explode(',', $data["ids"])];
        $data["admin_edit_id"] = ADMIN_ID;

        //拆分单品/多包装 成两条信息
        foreach ($data as $key => $value)
        {
            if (strpos($key, "single_") !== false)
            {
                $single[str_replace("single_", "", $key)] = $value;

            }
            elseif (strpos($key, "pack_") !== false)
            {
                $pack[str_replace("pack_", "", $key)] = $value;
            }
        }
        //保存商品基本信息
        $result = $this->modelGoods->setInfo($data, $where);


        //保存单品简述信息
        if (!empty($single))
        {
            $where_sinlge["goods_id"] = ["in", explode(',', $data["ids"])];
            $where_sinlge["kind"]     = 0;
            $result                   = $this->modelGoodsProfile->setInfo($single, $where_sinlge);
        }


        //保存多包装简述信息
        if (!empty($pack))
        {
            $where_pack["goods_id"] = ["in", explode(',', $data["ids"])];
            $where_pack["kind"]     = 1;
            $result                 = $this->modelGoodsProfile->setInfo($pack, $where_pack);
        }

        $result && action_log('批量修改', '商品批量修改'.'，where：'.$data["ids"]);


        return $result ? [RESULT_SUCCESS, '修改成功']: [RESULT_ERROR, $this->modelGoods->getError()];
    }

    #endregion


    #region/*------------------------------------多包装-------------------------------------------*/

    /**
     * 整包批量修改
     * @param array $data
     */
    public function batchPackEdit($data = [])
    {

        $where["id"] = ["in", explode(',', $data["ids"])];


        //        //会员价
        //        foreach ($data['member_grade_id'] as $key => $value)
        //        {
        //            $memberPrice[$key]['grade_id']  =$value;
        //            $memberPrice[$key]['price'] = $data['member_price'][$key]?:$data['retail_price'];
        //        }
        //        $data["member_price"] = $memberPrice;
        //
        //
        //        //批发价组合
        //        foreach ($data['wholesale_grade_id'] as $key => $value)
        //        {
        //            $wholesalePrice[$key]['grade_id']  =$value;
        //            $wholesalePrice[$key]['price'] = $data['wholesale_price'][$key]?:$data['retail_price'];
        //        }
        //        $data["wholesale_price"] = $wholesalePrice;

        $result = $this->modelGoodsProfile->setInfo($data, $where);
        $result && action_log('批量修改', '多包装批量修改'.'，where：profile_id '.$data["ids"]);

        return $result ? [RESULT_SUCCESS, '修改成功']: [RESULT_ERROR, $this->modelGoods->getError()];
    }

    #endregion


    #region/*------------------------------------组合商品-------------------------------------------*/

    /**
     * 获取商品列表
     */
    public function getGroupGoodsList($where = [], $field = 'gf.*,gp.*,g.*', $order = 'g.id desc', $pagenate = DB_LIST_ROWS, $join = [])
    {
        if (!array_key_exists('g.'.DATA_STATUS_NAME, $where))
        {
            $where['g.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }

        if (!array_key_exists("gp.shop_id", $where))
        {
            $where["gp.shop_id"] = SHOP_ID;//默认价
        }

        if (!array_key_exists("gf.kind", $where))
        {
            $where["gf.kind"] = 2;//连接组合品
        }

        $this->modelGoods->alias('g');
        if (empty($join))
        {
            $join = [
                [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id ', 'LEFT'],
                [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id ', 'LEFT'],
                [SYS_DB_PREFIX.'admin a', 'g.admin_add_id = a.id', 'LEFT'],
            ];
        }

        return $this->modelGoods->getList($where, $field, $order, $pagenate, $join);
    }


    /**
     * 获取组合商品搜索条件
     * @param array $data
     * @return array
     */
    public function getGroupWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['gf.name|gf.barcode1'] = ['like', '%'.$data['search_data'].'%'];

        return $where;
    }


    /**
     * 组合商品编辑保存
     */
    public function groupGoodsEdit($data = [])
    {
        //        $validate_result = $this->validateGoods->scene('edit')->check($data);
        ////        if (!$validate_result) : return [RESULT_ERROR, $this->validateGoods->getError()]; endif;
        ///
        $data['kind'] = 2;
        if (empty($data['id']))
        {
            $data['admin_add_id'] = ADMIN_ID;
        }
        else
        {
            $data['admin_edit_id'] = ADMIN_ID;
        }

        $fn = function() use ($data) {
            $rawData = $data;
            //保存商品基本信息
            $goodData = $data;
            $goodsRes = $this->modelGoods->setInfo($goodData);
            if (empty($data["id"]))
            {
                $data["id"] = $this->modelGoods->getLastInsID();
            }


            //保存商品profile信息
            $profileData = $data;
            //关联goods_id
            $profileData["id"]       = $data["profile_id"];
            $profileData["goods_id"] = $data["id"];
            $profileData["barcode1"] = $data['barcode'][0];
            $profileRes              = $this->modelGoodsProfile->setInfo($profileData);
            if (empty($profileData["id"]))
            {
                $profileData["id"] = $this->modelGoodsProfile->getLastInsID();
            }

            //新增时保存价格
            if (empty($rawData["id"]))
            {
                $priceData                 = $data;
                $priceData["shop_id"]      = SHOP_ID;
                $priceData["id"]           = $data["price_id"];
                $priceData["profile_id"]   = $profileData["id"];
                $priceData['member_price'] = [];

                foreach ($data['member_price'] as $key => $value)
                {
                    $priceData['member_price'][$key]['price']    = $value ?: $priceData["retail_price"];
                    $priceData['member_price'][$key]['grade_id'] = $data['member_grade_id'][$key];
                }
                $priceData["member_price1"] = $priceData['member_price'][0]['price'];
                $priceRes                   = $this->modelGoodsPrice->setInfo($priceData);

            }

            //保存条形码
            //            $barcodeData = $data;
            $barcodeList = [];
            foreach ($data['barcode'] as $key => $value)
            {
                $arr['barcode']    = $value;
                $arr['profile_id'] = $profileData["id"];
                //                    $arr['id']      =empty($data['barcode_id'][$key])?null:$data['barcode_id'][$key] ;
                array_push($barcodeList, $arr);
                unset($arr);
            }
            //删除原有条码信息
            $this->modelGoodsBarcode->deleteInfo(["profile_id" => $profileData["id"]], false);

            $barcodeRes = $this->modelGoodsBarcode->setList($barcodeList, false);

            //保存组合商品
            $groupList = [];
            foreach ($data['num'] as $key => $value)
            {
                $arr['num']        = $value;
                $arr['goods_id']   = $data['goods_id'][$key];
                $arr['profile_id'] = $profileData["id"];
                //                $arr['id']      =empty($data['group_id'][$key])?null:$data['group_id'][$key] ;
                array_push($groupList, $arr);
                unset($arr);
            }
            //删除原有组合商品
            $this->modelGoodsGroup->deleteInfo(["profile_id" => $profileData["id"]], false);
            $groupRes = $this->modelGoodsGroup->setList($groupList, false);

        };


        $result = closure_list_exe([$fn]);

        $handle_text = empty($data['id']) ? '新增': '编辑';

        $result && action_log($handle_text, '商品'.$handle_text.'，组合商品：'.$data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功', '', $data['id']]: [RESULT_ERROR, $this->modelGoods->getError()];
    }


    #endregion


    #region/*------------------------------------异常商品-------------------------------------------*/

    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getExceptionWhere($data = [])
    {


        !empty($data['search_data']) && $where['sgf.name|sgf.barcode1'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['goods_type']) && $where['g.goods_type'] = $data['goods_type'];
        !empty($data['category_id']) && $where['g.category_id'] = $data['category_id'];
        !empty($data['brand_id']) && $where['g.brand_id'] = $data['brand_id'];
        !empty($data['shop_id'])&& $where["gp.shop_id"] = $data['shop_id'];
        $where["sgf.kind"]    = ["neq", 2];

        if (!empty($data['start_time']) || empty($data['end_time']))
        {
            $date                   = getdate();
            $end_time               = empty($data['end_time']) ? mktime(0, 0, 0, $date['mon'], $date['mday'] + 1, $date['year']): $data['end_time']." 23:59:59";
            $start_time             = empty($data['start_time']) ? 0: $data['start_time'];
            $where["g.create_time"] = ["between time", [$start_time, $end_time]];
        }


            $item = [
                "sgf.barcode" => "sgf.barcode1=''",
                "g.category" => "g.category_id=0 or c.status=-1",
                "g.brand" => "g.brand_id=0 or b.status=-1",
                "sgf.unit" => "sgf.unit=''",
                "sgf.specification" => "sgf.specification=''",
                "g.origin" => "g.origin=''",
                "g.supplier" => "g.supplier_id=0 or s.status=-1",
                //                "sgp.cost_price" => "sgp.cost_price=0",
                //                "sgp.retail_price" => "sgp.retail_price=0",
                //                "sgp.member_price1" => "sgp.member_price1=0",
                //                "retailltcost" => "sgp.retail_price < sgp.cost_price",
                //                "memberlltcost" => "sgp.member_price1 < sgp.cost_price",
                //                "membergtretail" => "sgp.member_price1 > sgp.retail_price",
                "expiry_day" => "expiry_day=0",
                //                "stock_ceil" => "stock_ceil=0",
                //                "stock_floor" => "stock_floor=0",
                "is_discount" => "is_discount=0",
                "is_bargain" => "is_bargain=0",
                "is_presentation" => "is_presentation=0",
                "is_stored" => "is_stored=0",
                "is_point" => "is_point=0",
                "is_control_stock" => "is_control_stock=0",
                "pgf.barcode" => "pgf.barcode1=''",
                "pgg.num" => "pgg.num=0",
                "pgf.specification" => "pgf.specification=''"
            ];

        if (empty($data["search_item"]))
        {
            $data["search_item"]= array_keys($item) ;
        }
            $str  = "";
            foreach ($data["search_item"] as $key => $value)
            {
                //                $where[] = ["exp", $item[$value]];

                $str .= $key == 0 ? $item[$value]: " or ".$item[$value];
            }
            $where[] = ["exp", $str];



        return $where;
    }

    #endregion

}
