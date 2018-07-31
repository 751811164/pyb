<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | ReCouponitory | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\logic;

/**
 * 逻辑
 */
class Coupon extends AdminBase
{

    /**
     * 获取列表
     */
    public function getCouponList($where = [], $field = 'c.*', $order = '',$pagenate=0,$join=[])
    {

        $this->modelCoupon->alias('c');
        if (empty($join))
        {
            $join=[
                [SYS_DB_PREFIX . 'coupon_type t','c.type=t.id','LEFT']
            ];
        }

        $where['c.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        return $this->modelCoupon->getList($where, $field, $order, $pagenate, $join);
    }

    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getWhere($data=[]){
        $where = [];

        !empty($data['search_data']) && $where['c.name'] = ['like', '%'.$data['search_data'].'%'];

        return $where;
    }


    /**
     * 编辑
     */
    public function CouponEdit($data = [])
    {

        $validate_result = $this->validateCoupon->scene('edit')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $this->validateCoupon->getError()]; endif;

        $url = url('CouponList');

        $result = $this->modelCoupon->setInfo($data);

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '礼券' . $handle_text . '，name：' . $data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelCoupon->getError()];
    }

    /**
     * 获取信息
     */
    public function getCouponInfo($where = [], $field = true)
    {

        return $this->modelCoupon->getInfo($where, $field);
    }

    /**
     * 删除
     */
    public function CouponDel($where = [])
    {

        $result = $this->modelCoupon->deleteInfo($where);

        $result && action_log('删除', '礼券删除' . '，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelCoupon->getError()];
    }

}
