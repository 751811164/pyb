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
 * 店铺逻辑
 */
class Shop extends AdminBase
{

    /**
     * 获取商品信息
     */
    public function getShopInfo($where = [], $field = true)
    {

        return $this->modelShop->getInfo($where, $field);
    }

    /**
     * 获取店铺列表
     */
    public function getShopList($where = [], $field = "s.*", $order = '', $pagenate = DB_LIST_ROWS)
    {
        $this->modelShop->alias('s');
        $join                         = [
            //[SYS_DB_PREFIX.'admin a', 'a.shop_id = s.id ', "LEFT"],
            [SYS_DB_PREFIX.'shop_type st', 'st.id = s.type_id', 'LEFT'],
        ];
        $where['s.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $res                          = $this->modelShop->getList($where, $field, $order, $pagenate, $join);

        return $res;

    }

    public function getSimpleShopList($where = [], $field = "s.*", $order = '', $paginate = 0, $join = [])
    {
        $this->modelShop->alias('s');
        if (empty($where['s.'.DATA_STATUS_NAME]))
        {
            $where['s.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }
        return $this->modelShop->getList($where, $field, $order, $paginate, $join);
    }

    /**
     * 获取搜索条件
     */
    public function getWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['s.name|s.number|mobile|legalname'] = ['like', '%'.$data['search_data'].'%'];

        return $where;
    }


    /**
     * 模态框编辑
     */
    public function getEditModal($where = [], $field = true)
    {

        $res = $this->modelShop->getInfo($where, $field);
        return $res;
    }


    /**
     * 添加店铺信息
     */
    public function shopSave($data = [])
    {


        //验证店铺信息
        $validate_result = $this->validateShop->scene('all')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validateShop->getError()]; endif;
        //计算编号规则
        if (empty($data['id']) )
        {
            $data['number'] = $this->createCode($data);
        }
        else
        {
            unset($data['number']);
        }

        $func1 = function() use ($data) {
            $r = $this->modelShop->setInfo($data);

            //总部首次建立,记入配置表
            if (empty($data['id']) && !SHOP_ID)
            {
                $data['id']  = $this->modelShop->getLastInsID();
                $config_data = [
                    'name' => 'shop_id',
                    'title' => '总部id',
                    'describe' => '总部id',
                    'group' => 3,
                    'value' => $this->modelShop->getLastInsID(),
                ];
                $this->modelConfig->setInfo($config_data);
            }
        };


        //执行事务闭包操作
        $result = closure_list_exe([$func1]);

        $url         = url('shopList');
        $handle_text = empty($data['id']) ? '新增': '编辑';
        $id          = empty($data['id']) ? $this->modelShop->getLastInsID(): $data['id'];

        $result && action_log($handle_text, '商户信息'.$handle_text.'，name：'.$data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功', $url, ['id' => $id]]: [RESULT_ERROR, $this->modelShop->getError()];
    }


    public function getShopTree()
    {
        //店铺列表
        $where_type[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where_shop[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $typeList                     = collection($this->logicShopType->getTypeList($where_type, 'id,name', "", false, [], ''))->toArray();
        $shopList                     = $this->getSimpleShopList($where_shop, 'id,name,type_id', "", false, [], '');

        foreach ($typeList as $key => $type)
        {
            $typeList[$key]["children"] = [];
            foreach ($shopList as $k => $shop)
            {

                if ($shop['type_id'] == $type['id'])
                {
                    $typeList[$key]["children"][] = $shopList[$k];
                }
            }
        }
        return $typeList;
    }

    public function createCode($data = [], $len = 4)
    {

        if (empty($data['id']) && !SHOP_ID)
        {
            //总部
            $number = sprintf("%0{$len}s", 0);
        }
        else
        {
            $where[DATA_STATUS_NAME]=['neq',DATA_DELETE];
            //非总部
            $number = $this->modelShop->stat($where, 'max', 'number');
            if (empty($number))
            {
                $number = sprintf("%0{$len}s", 1);
            }
            else
            {
//                $number = sprintf("%0{$len}s", $number * 1 + 1);
                $number= substr($number,-$len);
                $number = sprintf("%0{$len}s",  intval($number) + 1);
            }
        }
        return $number;
    }

}
