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
class SupplierPrice extends AdminBase
{

    /**
     * 信息编辑
     */
    public function priceEdit($data = [])
    {

        //        $validate_result = $this->validateSupplierPrice->scene('edit')->check($data);
        //
        //        if (!$validate_result) {
        //
        //            return [RESULT_ERROR, $this->validateSupplierPrice->getError()];
        //        }

        if (empty($data["id"]))
        {
            $data["id"]=null;
            //拆分单品/多包装 成两条信息
            foreach ($data as $key => $value)
            {
                if (strpos($key, "single_") !== false)
                {
                    $single[$key] = $value;

                }
                elseif (strpos($key, "pack_") !== false)
                {
                    $pack[$key] = $value;

                }
            }

            //会员价组合
            foreach ($single['single_member_grade_id'] as $key => $value)
            {

                $memberPrice[$key]['grade_id']  =$value;
                $memberPrice[$key]['price'] = $data['single_member_price'][$key]?:$data['single_retail_price'];

            }
            $data["single_member_price"]= $memberPrice;//单个会员价组合


            foreach ($pack['pack_member_grade_id'] as $key => $value)
            {
                $memberPrice1[$key]['grade_id']  =$value;
                $memberPrice1[$key]['price'] = $pack['pack_member_price'][$key]?:$pack['pack_retail_price'];
            }
            $data["pack_member_price"]= $memberPrice1;//整装会员价组合
        }

        $fn= function()use($data){
        //1设置其余非默认
        $this->modelSupplierPrice->setInfo(['checked'=>0],["goods_id"=>$data["goods_id"]]);

        //2.保存价格信息
        $data["checked"] =1;
        $result = $this->modelSupplierPrice->setList([$data],true);

        $id = empty($data["id"])?$this->modelSupplierPrice->getLastInsID():$data["id"];

        $info =$this->getPriceInfo(["id"=>$id],true);
        $info=$info?$info->toArray():[];
        //修改商品价格
        //拆分单品/多包装 成两条信息
        foreach ($info as $key => $value)
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
            $single['member_price1'] = $single["member_price"][0]["price"];
            $pack['member_price1'] = $pack["member_price"][0]["price"];


        //通过goods_id获取 价格对应的profile信息,作保存
//        $profileList= $this->modelGoodsProfile->getColumn(["goods_id"=>$data["goods_id"],DATA_STATUS_NAME=>DATA_NORMAL],"id,kind,goods_id");
        $profileList= $this->logicGoodsProfile->getProfileList(["gf.goods_id"=>$data["goods_id"],"gf.kind"=>["neq",2],"gf.".DATA_STATUS_NAME=>DATA_NORMAL],"gf.id,gf.kind,gf.goods_id,gg.num");




        foreach ($profileList as $key=>$item){
            if ($item['kind']==0)
            {
                $where_single["profile_id"]=$item["id"];
                $where_single["shop_id"]=SHOP_ID;
            }
            elseif ($item['kind']==1)
            {
                $pack["cost_price"] = $single["cost_price"]*$item["num"];
                $where_pack["profile_id"]=$item["id"];
                $where_pack["shop_id"]=SHOP_ID;
            }
        }
        //3:保存单品和多包装价格
        $res=  $this->modelGoodsPrice->setInfo($single,$where_single);
        $res= $this->modelGoodsPrice->setInfo($pack,$where_pack);


        //4:保存商品对应的供应商
            if (!empty($data["supplier_id"]))
            {

            $this->modelGoods->setInfo(["supplier_id"=>$data["supplier_id"]],["id"=>$data["goods_id"]]);
            }
        };
        $result= closure_list_exe([$fn]);

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '' . $handle_text ."启用供应商价格". '，data：' . http_build_query($data));

        return $result ? [RESULT_SUCCESS, '操作成功', null] : [RESULT_ERROR, $this->modelSupplierPrice->getError()];
    }

    /**
     * 获取信息
     */
    public function getPriceInfo($where = [], $field = true)
    {

        return $this->modelSupplierPrice->getInfo($where, $field);
    }

    /**
     * 获取信息
     */
    public function getPriceJoinInfo($where = [], $field = "*")
    {


    }

    /**
     * 获取列表
     */
    public function getPriceList($where = [], $field = "*,sp.id", $order = '', $paginate = 0)
    {
        $this->modelSupplierPrice->alias('sp');
        if (!array_key_exists('sp.'.DATA_STATUS_NAME,$where))
        {
            $where['sp.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }

        $join = [
            [SYS_DB_PREFIX.'supplier s', 'sp.supplier_id = s.id', 'LEFT'],

        ];



        return $this->modelSupplierPrice->getList($where, $field, $order, $paginate,$join );
    }

    /**
     * 获取列表
     */
    public function getSimplePriceList($where = [], $field = true, $order = '', $paginate = 0,$join = [], $group = '', $limit = null)
    {

        return $this->modelSupplierPrice->getList($where, $field, $order, $paginate,$join ,$group, $limit);
    }


    
    /**
     * 删除
     */
    public function priceDel($where = [])
    {
        
        $result = $this->modelSupplierPrice->deleteInfo($where);
        
        $result && action_log('删除', '删除' . '，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '删除成功','',null] : [RESULT_ERROR, $this->modelSupplierPrice->getError()];
    }
}
