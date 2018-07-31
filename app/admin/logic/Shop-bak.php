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
     * 获取店铺列表
     */
    public function getShopList($where = [], $field = true, $order = '', $paginate = 0)
    {

        return $this->modelShop->getList($where, $field, $order, $paginate);
    }

    /**
     * 获取搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];
        !empty($data['search_data']) && $where['name|number|representative|phone|region'] = ['like', '%'.$data['search_data'].'%'];

        return $where;
    }


    /**
     * 店铺信息编辑
     */
    public function shopEdit($data = [])
    {

        $validate_result = $this->validateShop->scene('edit')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $this->validateShop->getError()]; endif;

        $url = url('shopList');

        $result = $this->modelShop->setInfo($data);

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '店铺' . $handle_text . '，name：' . $data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelShop->getError()];
    }

    /**
     * 获取店铺信息
     */
    public function getShopInfo($where = [], $field = true)
    {

        return $this->modelShop->getInfo($where, $field);
    }

    /**
     * 店铺删除
     */
    public function shopDel($where = [])
    {

        $result = $this->modelShop->deleteInfo($where);

        $result && action_log('删除', '店铺删除' . '，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelShop->getError()];
    }
}
