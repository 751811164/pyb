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
class settingExpense extends AdminBase
{

    /**
     * 信息
     */
    public function getSettingInfo($where = [], $field = true)
    {
        $info = $this->modelsettingExpense->getInfo($where, $field);
        return $info;
    }

    /**获取列表
     */
    public function getSettingList($where = [], $field = 'ss.*,username', $order = 'ss.status desc,ss.id desc', $paginate = DB_LIST_ROWS)
    {

        $this->modelsettingExpense->alias('ss');

        $join = [
            [SYS_DB_PREFIX.'auth_group_access ags', 'ss.admin_id = ags.admin_id'],
            [SYS_DB_PREFIX.'admin a', 'a.id = ss.admin_id'],
//            [SYS_DB_PREFIX.'auth_group g', 'g.id = ss.group_id', 'LEFT'],
        ];

        $where['a.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['ss.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['ags.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        $list= $this->modelsettingExpense->getList($where, $field, $order, $paginate, $join);
        return  $list;
    }

    public function getSimpleSettingList($where=[],$field=true,$order,$paginate = DB_LIST_ROWS){
        return $this->modelsettingExpense->getList($where, $field, $order, $paginate);
    }


    /**
     * 新增编辑
     * @return array
     */
    public function SettingEdit($data=[]){

        $validate_result = $this->validatesettingExpense->scene('edit')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validatesettingExpense->getError()]; endif;

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


        $result = $this->modelsettingExpense->setInfo($data);

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '其他奖励规则' . $handle_text . '，group：' . $data['group_id']);

        return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $this->modelsettingExpense->getError()];

    }




}

