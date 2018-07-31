<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | ReshopDisburseitory | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\logic;

/**
 * 店铺支出逻辑
 */
class ShopDisburse extends AdminBase
{

    /**
     * 获取店铺支出列表
     */
    public function getShopDisburseList($where = [], $field = 'sd.*', $order = '')
    {

        $this->modelShopDisburse->alias('sd');
        $join = [
            [SYS_DB_PREFIX . 'shop s', 'sd.shop_id = s.id'],
            [SYS_DB_PREFIX . 'disburse_type dt', 'sd.disburse_type_id = dt.id'],
        ];
        $where['sd.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        return $this->modelShopDisburse->getList($where, $field, $order, DB_LIST_ROWS, $join);
    }

    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getWhere($data=[]){
        $where = [];

        !empty($data['search_data']) && $where['p.name|s.name'] = ['like', '%'.$data['search_data'].'%'];

        return $where;
    }


    /**
     * 店铺支出信息编辑
     */
    public function shopDisburseEdit($data = [])
    {

        $validate_result = $this->validateShopDisburse->scene('edit')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $this->validateShopDisburse->getError()]; endif;

        $url = url('shopDisburseList');

        $result = $this->modelShopDisburse->setInfo($data);

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '店铺支出' . $handle_text . '，describe：' . $data['describe']);

        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelShopDisburse->getError()];
    }

    /**
     * 获取店铺支出信息
     */
    public function getShopDisburseInfo($where = [], $field = true)
    {

        return $this->modelShopDisburse->getInfo($where, $field);
    }

    /**
     * 店铺支出删除
     */
    public function shopDisburseDel($where = [])
    {

        $result = $this->modelShopDisburse->deleteInfo($where);

        $result && action_log('删除', '店铺支出删除' . '，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelShopDisburse->getError()];
    }

}
