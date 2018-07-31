<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Recarditory | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\logic;

/**
 * 逻辑
 */
class Card extends AdminBase
{

    /**
     * 获取会员卡信息
     */
    public function getCardInfo($where = [], $field = true)
    {
        return $this->modelCard->getInfo($where, $field);
    }

    /**
     * 获取会员卡信息
     */
    public function getCardJoinInfo($where = [], $field = 'c.*,add.username admin_add_name,edit.username admin_edit_name')
    {
        $this->modelCard->alias('c');
        $join                         = [
            //            [SYS_DB_PREFIX.'card_type ct', 'ct.id = c.type_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin add', 'add.id = c.admin_add_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin edit', 'edit.id = c.admin_edit_id', 'LEFT'],
        ];
        $where['c.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        return $this->modelCard->getInfo($where, $field, $join);
    }

    /**
     * 获取列表
     */
    public function getCardList($where = [], $field = 'c.*,ct.name type_name,ct.number type_number,add.username admin_add_name,edit.username admin_edit_name', $order = '', $pagenate = 0)
    {

        $this->modelCard->alias('c');
        $join                         = [
            [SYS_DB_PREFIX.'card_type ct', 'ct.id = c.type_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin add', 'add.id = c.admin_add_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin edit', 'edit.id = c.admin_edit_id', 'LEFT'],
        ];
        $where['c.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        return $this->modelCard->getList($where, $field, $order, $pagenate, $join);
    }

    /**
     * 获取列表
     */
    public function getSimpleCardList($where = [], $field =true, $order = '', $pagenate = 0,$join=[])
    {

        $this->modelCard->alias('c');
        $where['c.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        return $this->modelCard->getList($where, $field, $order, $pagenate, $join);
    }

    /**
     * 获取搜索条件
     * @param array $data
     * @return array
     */
    public function getWhere($data = [])
    {
        $where = [];

        !empty($data['search_data']) && $where['c.name'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['type_id']) && $where['c.type_id'] = $data['type_id'];

        if (!empty($data['start_time']) || !empty($data['end_time']))
        {
            $date       = getdate();
            $end_time   = empty($data['end_time']) ? mktime(0, 0, 0, $date['mon'], $date['mday'] + 1, $date['year']): $data['end_time']." 23:59:59";
            $start_time = empty($data['start_time']) ? 0: $data['start_time'];
            //            $query->whereTime('do.create_time', 'between', [$start_time, $end_time]);
            $where['c.create_time'] = ['between time', [$start_time, $end_time]];
        }


        return $where;
    }


    /**
     * 会员卡信息编辑
     */
    public function cardEdit($data = [])
    {
        $validate_result = $this->validateCard->scene('edit')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validateCard->getError()]; endif;

        if (empty($data['id']))
        {
            $data['number']       = $this->createCode($data);
            $data['admin_add_id'] = ADMIN_ID;
            $handle_text          = '新增';
        }
        else
        {
            $data['admin_edit_id'] = ADMIN_ID;
            $handle_text           = '编辑';
        }
        $result = $this->modelCard->setInfo($data);

        $result && action_log($handle_text, '会员卡'.$handle_text.'，name：'.$data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $this->modelCard->getError()];
    }


    /**
     * 会员卡删除
     */
    public function cardDel($where = [])
    {

        $result = $this->modelCard->deleteInfo($where);

        $result && action_log('删除', '会员卡删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功']: [RESULT_ERROR, $this->modelCard->getError()];
    }

    /**
     *生成编号
     */
    public function createCode($data = [], $len = 2)
    {
        $where_type['id']             = $data['type_id'];
        $where_type[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $type                         = $this->modelCardType->getInfo($where_type, true);
        if (empty($type))
        {
            return null;
        }

        $where['type_id']        = $data['type_id'];
        $where[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $number                  = $this->modelCard->stat($where, 'max', 'number');

        $number = substr($number, -$len);
        $number = sprintf("%0{$len}s", intval($number) + 1);

        return $type['number'].$number;
    }

    /**
     * 树形结构
     */
    public function getCardTree()
    {
        $where[DATA_STATUS_NAME] = DATA_NORMAL;
        $typeList                = collection($this->modelCardType->getList($where, 'id,name,number', 'id', false))->toArray();
        $cardList                = collection($this->modelCard->getList($where, 'id,name,number,type_id', 'id', false))->toArray();
        foreach ($typeList as $key => $type)
        {
            $typeList[$key]['children'] = [];
            foreach ($cardList as $k => $card)
            {
                if ($card['type_id'] == $type['id'])
                {
                    $typeList[$key]['children'][] = $card;
                }
            }

        }
        return $typeList;
    }
}
