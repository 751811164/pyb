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
class GoodsStockSetting extends AdminBase
{


    /**
     * 获取信息
     */
    public function getStockInfo($where = [], $field = true)
    {

        return $this->modelGoodsStockSetting->getInfo($where, $field);
    }


    /**
     * 获取列表
     */
    public function getStockList($where = [], $field = 'gf.*,g.*,gs.*,IFNULL(sum(gbs.stock_actual),0) stock_actual', $order = 'gs.id desc', $pagenate = DB_LIST_ROWS, $join = [],$group='')
    {
        if (!array_key_exists('gs.'.DATA_STATUS_NAME, $where))
        {
            $where['gs.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }


        $this->modelGoodsStockSetting->alias('gs');
        if (empty($join))
        {

            $join = [
                [SYS_DB_PREFIX.'shop sh', 'gs.shop_id = sh.id ', 'LEFT'],
                [SYS_DB_PREFIX.'goods g', 'gs.goods_id = g.id ', 'LEFT'],
                [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id and gf.kind=0', 'LEFT'],
                [SYS_DB_PREFIX.'goods_type gt', 'g.goods_type = gt.id and gt.status=1', 'LEFT'],
                [SYS_DB_PREFIX.'brand b', 'g.brand_id = b.id and b.status=1', 'LEFT'],
                [SYS_DB_PREFIX.'category c', 'g.category_id = c.id and c.status=1', 'LEFT'],
                [SYS_DB_PREFIX.'supplier s', 'g.supplier_id = s.id and s.status=1', 'LEFT'],
                [SYS_DB_PREFIX.'goods_barcode_stock gbs', 'gs.shop_id=gbs.shop_id and gs.goods_id=gbs.goods_id', 'LEFT'],
            ];
        }
        if (empty($group))
        {
            $group ='g.id';
        }

        return $this->modelGoodsStockSetting->getList($where, $field, $order, $pagenate, $join,$group);
    }


    /**
     * 获取列表
     */
    public function getSimpleStockList($where = [], $field = true, $order = 'gs.id desc', $pagenate = DB_LIST_ROWS, $join = [])
    {
        $this->modelGoodsStockSetting->alias('gs');
        if (!array_key_exists('gs.'.DATA_STATUS_NAME, $where))
        {
            $where['gs.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        }


        return $this->modelGoodsStockSetting->getList($where, $field, $order, $pagenate, $join);
    }


    /**
     * 获取某个列的数组
     */
    public function getStockColumn($where = [], $field = '', $key = '')
    {
        return $this->modelGoodsStockSetting->getColumn($where, $field, $key);
    }

    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getWhere($data = [])
    {
        $where = [];

        //        !empty($data['search_data']) && $where['gf.name|b.name|c.name|gf.barcode1'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['sid']) && $where['gs.shop_id'] = $data['sid'];


        return $where;
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
    public function stockEdit($data = [])
    {
                $validate_result = $this->validateGoodsStockSetting->scene('edit')->check($data);
                if (!$validate_result) : return [RESULT_ERROR, $this->validateGoodsStockSetting->getError()]; endif;

        $list = [];
        foreach ($data as $name => $value)
        {
            if (is_array($value))
            {
                foreach ($value as $k => $v)
                {
                    $list[$k][$name] = ( $name=="id" && empty($v))?null:$v;
                }
            }
        }

        $result      = $this->modelGoodsStockSetting->setList($list, true);
        $handle_text = empty($data['id']) ? '新增': '编辑';

        $result && action_log($handle_text, ''.$handle_text.'，商品指标管理：机构id'.$data['shop_id'][0]);

        return $result ? [RESULT_SUCCESS, '操作成功', ]: [RESULT_ERROR, $this->modelGoodsStockSetting->getError()];
    }



    /**
     * 删除
     */
    public function stockDel($where = [])
    {

        $result = $this->modelGoodsStockSetting->deleteInfo($where);

        $result && action_log('删除', '删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功']: [RESULT_ERROR, $this->modelGoodsStockSetting->getError()];
    }



}
