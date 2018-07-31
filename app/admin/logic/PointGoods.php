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
 * 积分商品逻辑
 */
class PointGoods extends AdminBase
{

    /**
     * 获取积分商品信息
     */
    public function getPointGoodsInfo($where = [], $field = true)
    {

        return $this->modelPointGoods->getInfo($where, $field);
    }

    /**
     * 获取积分商品列表
     */
    public function getPointGoodsList($where = [], $field = '*', $order = '', $paginate = 0, $join = [], $group = '', $limit = null)
    {
        $this->modelPointGoods->alias('pg');

        $join = [
            [SYS_DB_PREFIX.'goods_barcode gb', 'gb.id=pg.barcode_id'],
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.id=gb.profile_id'],
            [SYS_DB_PREFIX.'goods_price gp', 'gf.id=gp.profile_id and gp.shop_id='.SHOP_ID],
            [SYS_DB_PREFIX.'goods g', 'g.id=gf.goods_id'],
            [SYS_DB_PREFIX.'category c', 'c.id=g.category_id'],
            [SYS_DB_PREFIX.'brand b', 'b.id=g.brand_id'],
            [SYS_DB_PREFIX.'admin add', 'add.id = pg.admin_add_id'],
        ];
        if (empty($where['pg.'.DATA_STATUS_NAME]))
        {
            $where['pg.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }
        return $this->modelPointGoods->getList($where, $field, $order, $paginate, $join, $group, $limit);
    }


    public function getWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['gf.name|gb.barcode'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data[DATA_STATUS_NAME]) && $where['pg.'.DATA_STATUS_NAME] = $data[DATA_STATUS_NAME];
        if (!empty($data['start_time']) || !empty($data['end_time']))
        {
            $date                    = getdate();
            $end_time                = empty($data['end_time']) ? mktime(0, 0, 0, $date['mon'], $date['mday'] + 1, $date['year']): $data['end_time']." 23:59:59";
            $start_time              = empty($data['start_time']) ? 0: $data['start_time'];
            $where['pg.create_time'] = ['between time', [$start_time, $end_time]];
        }
        return $where;
    }


    public function getGoodsChooseWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['gf.name|gf.barcode1'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['cid']) && $where['c.id'] = $data['cid'];
        !empty($data['barcodes']) && $where["gb.barcode"] = ["not in", $data['barcodes']];
        !empty($data['shop_id']) && $where['gp.shop_id'] = $where['pgp.shop_id'] = SHOP_ID;
        return $where;
    }

    /**
     * 设置积分兑换数量
     */
    public function setPointNum($data=[]){
        $result = $this->modelPointGoods->setInfo(['point_num'=>$data['value']], ['id'=>$data['id']]);

        $handle_text = '修改兑换数量';

        $result && action_log($handle_text, '积分商品:'.$handle_text.'，values：'.json_encode($data));

        return $result ? [RESULT_SUCCESS, '操作成功','']: [RESULT_ERROR, $this->modelPointGoods->getError()];
    }


    /**
     * 积分商品信息编辑
     */
    public function pointGoodsEdit($data = [])
    {

        //        $validate_result = $this->validatePointGoods->scene('edit')->check($data);
        //
        //        if (!$validate_result) {
        //
        //            return [RESULT_ERROR, $this->validatePointGoods->getError()];
        //        }


        $list = [];


        foreach ($data['barcode_id'] as $key => $item)
        {

            $list[$key]['admin_add_id'] = ADMIN_ID;
            $list[$key]['barcode_id'] = $item;
            $list[$key]['point_num'] = $data['point_num'][$key];
        }
        $url = url('pointGoodsList');

        $result = $this->modelPointGoods->setList($list, true);

        $handle_text = '新增';

        $result && action_log($handle_text, '积分商品'.$handle_text.'，values：'.json_encode($data));

        return $result ? [RESULT_SUCCESS, '操作成功', $url]: [RESULT_ERROR, $this->modelPointGoods->getError()];
    }




    /**
     * 删除
     */
    public function pointGoodsDel($where = [])
    {


        $result = $this->modelpointGoods->deleteInfo(['id'=>['in',$where['ids']]]);

        $result && action_log('删除', '删除' . '，where：' . json_encode($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelPurchaseBillLog->getError()];
    }



}
