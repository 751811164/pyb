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
 * 会员卡类型逻辑
 */
class CardType extends AdminBase
{

    /**
     * 获取会员卡类型信息
     */
    public function getCardTypeInfo($where = [], $field = true)
    {
        return $this->modelCardType->getInfo($where, $field);
    }

    /**
     * 获取会员卡类型信息
     */
    public function getCardTypeJoinInfo($where = [], $field = "ct.*,add.username admin_add_name,edit.username admin_edit_name")
    {
        $this->modelCardType->alias('ct');
        $join                          = [
            [SYS_DB_PREFIX.'admin add', 'add.id = ct.admin_add_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin edit', 'edit.id = ct.admin_edit_id', 'LEFT'],
        ];
        $where['ct.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        return $this->modelCardType->getInfo($where, $field, $join);
    }

    /**
     * 获取会员卡类型列表
     */
    public function getCardTypeList($where = [], $field = "ct.*,add.username admin_add_name,edit.username admin_edit_name", $order = '', $paginate = 0)
    {
        $this->modelCardType->alias('ct');
        $where['ct.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $join                          = [
            [SYS_DB_PREFIX.'admin add', 'add.id = ct.admin_add_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin edit', 'edit.id = ct.admin_edit_id', 'LEFT'],
        ];
        return $this->modelCardType->getList($where, $field, $order, $paginate, $join);
    }

    /**
     * 获取会员卡类型列表
     */
    public function getSimpleCardTypeList($where = [], $field = "ct.*", $order = '', $paginate = 0, $join = [])
    {
        $this->modelCardType->alias('ct');
        $where['ct.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        return $this->modelCardType->getList($where, $field, $order, $paginate, $join);
    }


    public function getWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['name'] = ['like', '%'.$data['search_data'].'%'];
        return $where;
    }

    /**
     * 会员卡类型信息编辑
     */
    public function cardTypeEdit($data = [])
    {

        $validate_result = $this->validateCardType->scene('edit')->check($data);

        if (!$validate_result)
        {

            return [RESULT_ERROR, $this->validateCardType->getError()];
        }

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
        $result = $this->modelCardType->setInfo($data);


        $result && action_log($handle_text, '会员卡类型'.$handle_text.'，name：'.$data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $this->modelCardType->getError()];
    }


    /**
     * 会员卡类型删除
     */
    public function cardTypeDel($where = [])
    {

        $result = $this->modelCardType->deleteInfo($where);

        $result && action_log('删除', '会员卡类型删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功']: [RESULT_ERROR, $this->modelCardType->getError()];
    }

    /**
     *生成编号
     */
    public function createCode($data = [], $len = 2)
    {
        $where[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $number                  = $this->modelCardType->stat($where, 'max', 'number');
        if (empty($number))
        {
            $number = sprintf("%0{$len}s", 1);
        }
        else
        {
            $number = substr($number, -$len) - 0;
            $max    = sprintf("1%0{$len}s", 0) - 1;
            if ($number == $max)//超过最大值找出不连续的数字
            {
                $items = $this->modelCardType->getColumn([DATA_STATUS_NAME => DATA_NORMAL], 'number', '',  pow(10,$len),'number');
                foreach ($items as $key => $value)
                {
                    if ($key == 0)
                    {
                        if ($value != 1)
                        {
                            $number = sprintf("%0{$len}s", 1);
                            break;
                        }
                    }

                    elseif (($key!=count($items)-1)&& ($value != $items[$key +1] - 1))
                    {
                        $number = sprintf("%0{$len}s", intval($value) + 1);
                        break;
                    }
                }
            }
            else
            {
                $number = sprintf("%0{$len}s", intval($number) + 1);
            }

        }
        return $number;
    }

}
