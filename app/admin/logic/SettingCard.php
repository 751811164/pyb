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
class SettingCard extends AdminBase
{

    /**
     * 信息
     */
    public function getSettingInfo($where = [], $field = true)
    {
        $info = $this->modelSettingCard->getInfo($where, $field);
        return $info;
    }

    /**获取列表
     */
    public function getSettingList($where = [], $field = 's.name shop_name,g.name group_name,ss.*', $order = 'ss.status desc,ss.id desc', $paginate = DB_LIST_ROWS)
    {

        $this->modelSettingCard->alias('ss');

        $join = [
            [SYS_DB_PREFIX.'auth_group g', 'g.id = ss.group_id', 'LEFT'],
            [SYS_DB_PREFIX.'shop s', 's.id = g.shop_id', 'LEFT'],
        ];

        $where['ss.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
//        $where['g.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];

         $info= $this->modelSettingCard->getList($where, $field, $order, $paginate, $join);
        return $info;
    }

    public function getSimpleSettingList($where=[],$field=true,$order,$paginate = DB_LIST_ROWS){
        return $this->modelSettingCard->getList($where, $field, $order, $paginate);
    }


    /**
     * 新增编辑
     * @return array
     */
    public function SettingEdit($data=[]){

        $validate_result = $this->validateSettingCard->scene($data['type']==0?'storedEdit':'edit')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validateSettingCard->getError()]; endif;

        foreach ($data as $key=> $item)
        {
            if (is_array($item))
            {
                foreach ($item as $k=>$v)
                {
                    $rules[$k][$key]=(int)$v;
                }

            }
        }
        $data['rules'] = $rules;


        $result = $this->modelSettingCard->setInfo($data);

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '会员卡提成规则' . $handle_text . '，group：' . $data['group_id']);

        return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $this->modelSettingCard->getError()];

    }






}

