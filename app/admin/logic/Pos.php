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
 * POS机逻辑
 */
class Pos extends AdminBase
{

    /**
     * 获取POS机列表
     */
    public function getPosList($where = [], $field = 'p.*', $order = '')
    {

        $this->modelPos->alias('p');
        $join = [
            [SYS_DB_PREFIX . 'admin a', 'p.admin_id = a.id',"LEFT"],
            [SYS_DB_PREFIX . 'shop s', 'p.shop_id = s.id','LEFT'],
        ];
        $where['p.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        return $this->modelPos->getList($where, $field, $order, DB_LIST_ROWS, $join);
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
     * POS机信息编辑
     */
    public function posEdit($data = [])
    {

        $validate_result = $this->validatePos->scene('edit')->check($data);

        if (!$validate_result) : return [RESULT_ERROR, $this->validatePos->getError()]; endif;

        $url = url('posList');

        $result = $this->modelPos->setInfo($data);

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, 'POS机' . $handle_text . '，name：' . $data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelPos->getError()];
    }

    /**
     * 获取POS机信息
     */
    public function getPosInfo($where = [], $field = true)
    {

        return $this->modelPos->getInfo($where, $field);
    }

    /**
     * POS机删除
     */
    public function posDel($where = [])
    {

        $result = $this->modelPos->deleteInfo($where);

        $result && action_log('删除', 'POS机删除' . '，where：' . http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, $this->modelPos->getError()];
    }

}
