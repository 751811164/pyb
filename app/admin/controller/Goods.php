<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Regoodsitory | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\controller;

/**
 * 商品控制器
 */
class Goods extends AdminBase
{
    #region/*------------------------------------单品-------------------------------------------*/


    /**
     *  商品列表(单个商品)
     */
    public function goodsList()
    {
        //非总部人员,生成所在门店价格
        if (!IS_ROOT &&!session('viewed_goods_list') )
        {
//            if (!session('auth_shop_permission'))
//            {
//                return [RESULT_REDIRECT,'未获取您所在门店信息'];
//            }
//            else
                if (session('auth_shop_permission')&&session('auth_shop_permission')['type_id'] != HEAD_OFFICE)
            {
                $shop_id=session('auth_shop_permission')['shop_id'];
                //非总部查询,先生成门店商品价格
                $count = $this->logicGoodsPrice->getPriceStat(['shop_id' => SHOP_ID]);//商品价格总数
                for ($i = 0; $i < ceil($count / 10000); $i++)
                {

                    $arr1         = $this->logicGoodsPrice->getPriceColumn(['shop_id' => SHOP_ID], 'profile_id', '', $i.',10000');//商品价格列
                    $arr2         = $this->logicGoodsPrice->getPriceColumn(['shop_id' => $shop_id,'profile_id'=>['in',$arr1]], 'profile_id', '', $i.',10000');//店铺价格列
                    $profiles_ids = array_diff($arr1, $arr2);
                    if (count($profiles_ids)!=0)
                    {
                        $field='profile_id,cost_price,retail_price,gross_rate,wholesale_price,delivery_price,member_price,member_price1';
                        model('GoodsPrice')->field($field.",{$shop_id} shop_id")->where(['profile_id' => ['in', $profiles_ids],'shop_id'=>SHOP_ID])->selectInsert($field.',shop_id', SYS_DB_PREFIX.'goods_price');
                    }
                }
            }

            session('viewed_goods_list',1);
        }

        //左侧分类
        $categoryList = ($this->logicCategory->getCategoryList([], 'id,name,pid', '', false));
        $categoryList = $this->logicCategory->makeTree($categoryList, ['children_key' => 'children']);
        $this->assign('categoryList', $categoryList);
        //右侧商品列表
        $where = $this->logicGoods->getWhere($this->param);
        $list  = $this->logicGoods->getGoodsList($where, 'gf.*,gp.*,g.*,g.id,c.name category_name,b.name brand_name,gt.name as type_name,s.name supplier_name,username');
        //        foreach ($list as $key => $item)
        //        {
        //            $member_price                = json_decode($item["member_price"], true);
        //            $list[$key]["member_price1"] = $member_price[0]['price'];
        //        }
        //        echo model("Goods")->getLastSql();
        $this->assign('list', $list);
        return $this->fetch('goods_list');
    }


    /**
     * 商品添加与编辑通用方法
     */
    public function goodsCommon()
    {


        $where[DATA_STATUS_NAME] = DATA_NORMAL;
        //商品类型
        $this->assign('typeList', $typeList = $this->logicGoodsType->getTypeList($where, 'id,name', '', false));


        //品牌
        $this->assign('brandList', $this->logicBrand->getBrandList($where, 'id,name', '', false));
        //        //供应商
        //        $this->assign('supplierList', $supplierList = $this->logicSupplier->getSupplierList(['s.'.DATA_STATUS_NAME => DATA_NORMAL], 'id,name', '', false));

        //批发等级
        $this->assign('wholesaleGradeList', $wholesaleGradeList = $this->logicWholesaleGrade->getWholesaleGradeList($where, 'id,name', '', false));

        //会员等级
        $this->assign('memberGradeList', $memberGradeList = $this->logicMemberGrade->getSimpleMemberGradeList($where, 'id,name', '', false));
        //分类
        //        $pids        = $this->logicCategory->getCategoryColumn([DATA_STATUS_NAME => DATA_NORMAL], "pid");
        //        $where["id"] = ["not in", array_unique($pids)];
        $where_category["level"] = config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));

    }


    /**
     * 商品添加
     */
    public function goodsAdd()
    {
        IS_POST && $this->jump($this->logicGoods->goodsEdit($this->param));
        $this->goodsCommon();
        $this->assign('info', []);
        return $this->fetch('edit_modal');
    }

    /**
     * 商品编辑
     */
    public function goodsEdit()
    {

        IS_POST && $this->jump($this->logicGoods->goodsEdit($this->param));
        $this->goodsCommon();
        $single = [];
        $pack   = [];

        $where["g.id"]                = [trim($this->param["condition"]), $this->param['id']];//上一条,当前,下一条
        $where["g.".DATA_STATUS_NAME] = ["neq", DATA_DELETE];
        //        $info = $this->logicGoods->getGoodsInfo($where);
        $info = $this->logicGoods->getGoodsJoinInfo($where, "g.*");
        $category= $this->logicCategory->getCategoryInfo(['id'=>$info['category_id']],true);
        $ids =explode(',',$category['path']) ;
        $info['firstcate_id'] = $ids[count($ids)-2];
        $info['secondcate_id'] = $ids[count($ids)-1];

        if (!$info)
        {
            return $this->jump([RESULT_ERROR, "商品信息不存在"]);
        }
        $supplier              = $this->logicSupplier->getSupplierInfo(['s.id' => $info['supplier_id']], 's.id,s.name', false);
        $info['supplier_name'] = $supplier['name'];

        !empty($info) && $info['img_ids_array'] = str2arr($info['img_ids']);
        $add_admin               = $this->logicAdmin->getAdminInfo(['id' => $info['admin_add_id']], 'username');
        $edit_admin              = $this->logicAdmin->getAdminInfo(['id' => $info['admin_edit_id']], 'username');
        $info["admin_add_name"]  = empty($add_admin) ? "": $add_admin["username"];
        $info["admin_edit_name"] = empty($edit_admin) ? "": $edit_admin["username"];


        //查询单品和多包装信息

        $single = $this->logicGoodsProfile->getProfileJoinInfo(["gf.goods_id" => $info['id']], "*,gf.id profile_id,gg.id group_id,gp.id price_id");
        $pack   = $this->logicGoodsProfile->getProfileJoinInfo(["gf.goods_id" => $info['id'], "gf.kind" => 1], "*,gf.id profile_id,gg.id group_id,gp.id price_id");


        $single["goodsBarcodes"] = $this->logicGoodsBarcode->getBarcodeList([DATA_STATUS_NAME => 1, 'profile_id' => $single['profile_id']], true, '', false);
        $pack["goodsBarcodes"]   = $this->logicGoodsBarcode->getBarcodeList([DATA_STATUS_NAME => 1, 'profile_id' => $pack['profile_id']], true, '', false);


        //        $info= $info->toArray();

        $this->assign('info', $info->toArray());
        $this->assign('single', $single->toArray());
        $this->assign('pack', $pack->toArray());

        return $this->fetch('edit_modal');
    }

    /**
     * 选择供应商
     */
    public function chooseSupplier()
    {
        $this->view->engine->layout(false);
        $where = $this->logicSupplier->getWhere($this->param);
        $this->assign('list', $list = $this->logicSupplier->getSupplierList($where));
        return $this->fetch('choose_supplier_list');
    }

    /**
     * 单个批量修改
     * @throws \Exception
     */
    public function batchSingleEdit()
    {
        IS_POST && $this->jump($this->logicGoods->batchSingleEdit($this->param));
        $this->GoodsCommon();
        $this->assign('info', []);
        return $this->fetch('batch_single_edit');
    }


    /**
     * 商品删除
     */
    public function goodsDel($id = 0)
    {

        $this->jump($this->logicGoods->goodsDel(['id' => $id]));
    }

    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('Goods', $this->param));
    }

    #endregion


    #region/*------------------------------------多包装商品-------------------------------------------*/


    /**
     *  商品列表
     */
    public function packGoodsList()
    {
        //左侧分类
        $categoryList = $this->logicCategory->getCategoryList([], 'id,name,pid', '', false);
        $categoryList = collection($this->logicCategory->makeTree($categoryList, ['children_key' => 'children']))->toArray();
        $this->assign('categoryList', $categoryList);
        //多包装商品
        $where            = $this->logicGoods->getWhere($this->param);
        $where["gf.kind"] = 1;//
        $list             = $this->logicGoods->getGoodsList($where, 'gf.*,gp.*,g.*,gf.id,c.name category_name,gt.name as type_name,s.name supplier_name,username');
        //        foreach ($list as $key => $item)
        //        {
        //            $member_price                = json_decode($item["member_price"], true);
        //            $list[$key]["member_price1"] = $member_price[0]['price'];
        //        }
        $this->assign('list', $list);
        return $this->fetch('pack_list');
    }


    /**
     * 多包装批量修改
     * @throws \Exception
     */
    public function batchPackEdit()
    {
        IS_POST && $this->jump($this->logicGoods->batchPackEdit($this->param));
        $this->GoodsCommon();
        $this->assign('info', []);
        return $this->fetch('batch_pack_edit');
    }


    #endregion


    #region/*------------------------------------组合商品-------------------------------------------*/

    /**
     * 组合商品列表
     * @return mixed
     */
    public function groupGoodsList()
    {

        //左侧分类
        $categoryList = $this->logicCategory->getCategoryList([], 'id,name,pid', '', false);
        $categoryList = collection($this->logicCategory->makeTree($categoryList, ['children_key' => 'children']))->toArray();
        $this->assign('categoryList', $categoryList);
        //组合商品
        $where            = $this->logicGoods->getGroupWhere($this->param);
        $where["gf.kind"] = 2;//
        $list             = $this->logicGoods->getGroupGoodsList($where, 'gf.*,gp.*,g.*,g.id,username');
        //        foreach ($list as $key => $item)
        //        {
        //            $member_price                = json_decode($item["member_price"], true);
        //            $list[$key]["member_price1"] = $member_price[0]['price'];
        //        }
        $this->assign('list', $list);

        return $this->fetch('group_list');
    }

    /**
     * 单击查看组合商品列表信息
     * @return mixed
     */
    public function viewGroupGoods()
    {
        $where_profile["goods_id"] = $this->param["id"];
        $where_profile["kind"]     = 2;
        //        $where["gg.".DATA_STATUS_NAME] = DATA_NORMAL;

        $profile = $this->logicGoodsProfile->getProfileInfo($where_profile, "id", '', false);

        $where["gg.profile_id"]        = $profile["id"];
        $where["gg.".DATA_STATUS_NAME] = DATA_NORMAL;


        $list = $this->logicGoodsGroup->getGroupList($where, "gg.num,gf.*,gp.*,g.*,c.name category_name,gt.name as type_name,username", '', false);
        //        foreach ($list as $key => $item)
        //        {
        //            $member_price                = json_decode($item["member_price"], true);
        //            $list[$key]["member_price1"] = $member_price[0]['price'];
        //        }
        $this->assign("list", $list);
        return $this->fetch("view_group_goods");
    }


    /**
     * 商品添加与编辑通用方法
     */
    public function groupGoodsCommon()
    {
        $where[DATA_STATUS_NAME] = DATA_NORMAL;
        //会员等级
        $this->assign('memberGradeList', $memberGradeList = $this->logicMemberGrade->getSimpleMemberGradeList($where, 'id,name', '', false));
    }


    /**
     * 组合商品添加
     */
    public function groupGoodsAdd()
    {
        IS_POST && $this->jump($this->logicGoods->groupGoodsEdit($this->param));
        $this->groupGoodsCommon();
        $this->assign('groupGoods', []);
        $this->assign('info', []);
        return $this->fetch('group_edit_modal');
    }

    /**
     * 组合商品编辑
     */
    public function groupGoodsEdit()
    {

        IS_POST && $this->jump($this->logicGoods->groupGoodsEdit($this->param));
        $this->groupGoodsCommon();


        $where["g.id"]                = [trim($this->param["condition"]), $this->param['id']];//上一条,当前,下一条
        $where["g.".DATA_STATUS_NAME] = ["neq", DATA_DELETE];
        $where["gf.kind"]             = 2;//连接组合品

        $info = $this->logicGoods->getGoodsJoinInfo($where, 'gf.*,gp.*,g.*,gf.id profile_id,gp.id price_id');

        if (!$info)
        {
            return $this->jump([RESULT_ERROR, "商品信息不存在"]);
        }

        !empty($info) && $info['img_ids_array'] = str2arr($info['img_ids']);
        $add_admin               = $this->logicAdmin->getAdminInfo(['id' => $info['admin_add_id']], 'username');
        $edit_admin              = $this->logicAdmin->getAdminInfo(['id' => $info['admin_edit_id']], 'username');
        $info["admin_add_name"]  = empty($add_admin) ? "": $add_admin["username"];
        $info["admin_edit_name"] = empty($edit_admin) ? "": $edit_admin["username"];
        //条码
        $info["goodsBarcodes"] = $this->logicGoodsBarcode->getBarcodeList([DATA_STATUS_NAME => DATA_NORMAL, 'profile_id' => $info['profile_id']], true, '', false);
        //组合商品
        $groupGoods = collection($this->logicGoodsGroup->getGroupList([
            'gg.profile_id' => $info['profile_id'],
            'gg.'.DATA_STATUS_NAME => DATA_NORMAL
        ], "gf.barcode1,g.id goods_id,gf.name,gf.specification,gf.unit,gg.num,gg.id,gp.*", '', false))->toArray();

        $this->assign('groupGoods', $groupGoods);
        $this->assign('info', $info->toArray());

        return $this->fetch('group_edit_modal');
    }


    /**
     * 组合选择商品列表
     */
    public function groupGoodsChoose()
    {

        //分类
        //        $pids                 = $this->logicCategory->getCategoryColumn([DATA_STATUS_NAME => DATA_NORMAL], "pid");
        //        $where_category["id"] = ["not in", array_unique($pids)];
        $where_category["level"] = config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));


        $this->view->engine->layout(false);
        $where = $this->logicGoods->getWhere($this->param);
        $list  = $this->logicGoods->getGoodsList($where, 'g.id goods_id,gf.barcode1,gf.name,gf.specification,gf.unit,gg.num,gp.*,c.name category_name');

        $this->assign('list', $list);
        return $this->fetch('goods_choose');
    }


    /**
     * 组合数据状态设置
     */
    public function groupSetStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('Goods', $this->param));
    }

    /**
     * 组合的单品删除
     */
    public function groupGoodsDel($id = 0)
    {

        $this->jump($this->logicGoodsGroup->groupDel(['id' => $id]));
    }
    #endregion 组合商品


    #region/*------------------------------------异常查询-------------------------------------------*/

    public function exceptionList()
    {

        $searchItem = [
            ["id" => "sgf.barcode", "name" => "无条码商品"],
            ["id" => "g.category", "name" => "无分类"],
            ["id" => "g.brand", "name" => "无品牌"],
            ["id" => "sgf.unit", "name" => "无单位"],
            ["id" => "sgf.specification", "name" => "无规格"],
            ["id" => "g.origin", "name" => "无产地"],
            ["id" => "g.supplier", "name" => "无供应商"],
            //            ["id" => "sgp.cost_price", "name" => "无进价"],
            //            ["id" => "sgp.retail_price", "name" => "无零售价"],
            //            ["id" => "sgp.member_price1", "name" => "无会员价"],
            //            ["id" => "retailltcost", "name" => "零售价低于进价"],
            //            ["id" => "memberlltcost", "name" => "会员价低于进价"],
            //            ["id" => "membergtretail", "name" => "会员价高于零售价"],
            ["id" => "expiry_day", "name" => "未设置有效期"],
            //            ["id" => "stock_ceil", "name" => "未设置库存上线"],
            //            ["id" => "stock_floor", "name" => "未设置库存下线"],
            ["id" => "is_discount", "name" => "未开启前台打折"],
            ["id" => "is_bargain", "name" => "未开启前台议价"],
            ["id" => "is_presentation", "name" => "未开启前台赠送"],
            ["id" => "is_stored", "name" => "未开启项目储值"],
            ["id" => "is_point", "name" => "未开启商品积分"],
            ["id" => "is_control_stock", "name" => "未开始管理库存"],
            ["id" => "pgf.barcode", "name" => "未设多包装条码"],
            ["id" => "pgg.num", "name" => "未设多包装内装数"],
            ["id" => "pgf.specification", "name" => "未设多包装规格"]
        ];
        $this->assign('searchItem', $searchItem);

        $where[DATA_STATUS_NAME] = DATA_NORMAL;
        //商品类型
        $this->assign('typeList', $typeList = $this->logicGoodsType->getTypeList([], 'id,name', '', false));
        //品牌
        $this->assign('brandList', $this->logicBrand->getBrandList([], 'id,name', '', false));
        //机构
        $this->assign('shopList', $this->logicShop->getShopList([], 's.id,s.name', '', false));
        //分类
        //        $pids        = $this->logicCategory->getCategoryColumn([DATA_STATUS_NAME => DATA_NORMAL], "pid");
        //        $where["id"] = ["not in", array_unique($pids)];
        $where_category["level"] = config('logic_config.category_level') ;
        $this->assign('categoryList', $categoryList = $this->logicCategory->getCategoryList($where_category, true, '', false));


        //商品
        $where_goods = $this->logicGoods->getExceptionWhere($this->param);

        $join = [
            [SYS_DB_PREFIX.'goods_profile sgf', 'sgf.goods_id = g.id and sgf.kind=0 ', 'LEFT'],//单品
            [SYS_DB_PREFIX.'goods_profile pgf', 'pgf.goods_id = g.id  and pgf.kind=1', 'LEFT'],//多包装
            [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = sgf.id ', 'LEFT'],//单品价格
            [SYS_DB_PREFIX.'goods_group pgg', 'pgg.profile_id = sgf.id ', 'LEFT'],//多包装数量
            [SYS_DB_PREFIX.'goods_type gt', 'g.goods_type = gt.id and gt.status=1', 'LEFT'],//类型
            [SYS_DB_PREFIX.'brand b', 'g.brand_id = b.id and b.status=1', 'LEFT'],//品牌
            [SYS_DB_PREFIX.'category c', 'g.category_id = c.id and c.status=1', 'LEFT'],//分类
            [SYS_DB_PREFIX.'supplier s', 'g.supplier_id = s.id and s.status=1', 'LEFT'],//供应商
            [SYS_DB_PREFIX.'admin a', 'g.admin_add_id = a.id', 'LEFT'],//操作者
        ];

        $list = $this->logicGoods->getNormalGoodsList($where_goods, 'sgf.*,gp.*,g.*,g.id, c.name as category_name, gt.name  as type_name,s.name supplier_name,username', 'g.id desc', DB_LIST_ROWS, $join);
         //echo model("goods")->getLastSql();
        //        foreach ($list as $key => $item)
        //        {
        //            $member_price                = json_decode($item["member_price"], true);
        //            $list[$key]["member_price1"] = $member_price[0]['price'];
        //        }

        $this->assign("search_item", empty($this->param["search_item"]) ? []: $this->param["search_item"]);

        $this->assign('list', $list);
        return $this->fetch('exception_list');


    }

    #endregion 异常查询

    #region/*------------------------------------门店价格-------------------------------------------*/
    /**
     * 门店价格
     */
    public function shopPricelist()
    {

        //会员等级
        $this->assign('memberGradeList', $memberGradeList = $this->logicMemberGrade->getSimpleMemberGradeList([DATA_STATUS_NAME => DATA_NORMAL], 'id,name', '', false));
        $this->assign('shopTree', $shopTree = $this->logicShop->getShopTree());


        $list = [];
        if (!empty($this->param['shop_id']))
        {
            $shop_id = $this->param['shop_id'];
            $where   = ['gp.shop_id'=>$shop_id];

            $join  = [
                [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0'],
                [SYS_DB_PREFIX.'goods_price gp', 'gp.profile_id = gf.id and gp.shop_id='.$shop_id],
                [SYS_DB_PREFIX.'goods_profile pgf', 'pgf.goods_id = g.id and gf.kind=1', 'LEFT'],
                [SYS_DB_PREFIX.'goods_price pgp', 'pgp.profile_id = gf.id and pgp.shop_id='.$shop_id, 'LEFT'],
                [SYS_DB_PREFIX.'category c', 'g.category_id = c.id', 'LEFT'],
            ];
            $field = "c.name category_name,gf.barcode1,g.id goods_id,gf.name,gf.specification,gf.unit,gp.*,
            gp.cost_price single_cost_price,gp.retail_price single_retail_price,gp.wholesale_price single_wholesale_price,gp.delivery_price single_delivery_price,gp.member_price single_member_price,
            pgp.cost_price pack_cost_price,pgp.retail_price pack_retail_price,pgp.wholesale_price pack_wholesale_price,pgp.delivery_price  pack_delivery_price,pgp.member_price pack_member_price
            ";

            $list = $this->logicGoods->getSimpleGoodsList($where, $field, 'g.update_time desc', 0, $join);
        }

        //echo model("Goods")->getLastSql();
        $this->assign('list', $list);
        return $this->fetch('goods/shop_price_list');
    }
    #endregion
}
