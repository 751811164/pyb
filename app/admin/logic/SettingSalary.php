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
class SettingSalary extends AdminBase
{

    /**
     * 信息
     */
    public function getSettingInfo($where = [], $field = true)
    {
        $info = $this->modelSettingSalary->getInfo($where, $field);
        return $info;
    }

    /**获取列表
     */
    public function getSettingList($where = [], $field = 's.name shop_name,g.name group_name,ss.*', $order = 'ss.status desc,ss.id desc', $paginate = DB_LIST_ROWS)
    {

        $this->modelSettingSalary->alias('ss');

        $join = [
            [SYS_DB_PREFIX.'auth_group g', 'g.id = ss.group_id', 'LEFT'],
            [SYS_DB_PREFIX.'shop s', 's.id = g.shop_id', 'LEFT'],
        ];

        $where['ss.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
//        $where['g.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];

         $info= $this->modelSettingSalary->getList($where, $field, $order, $paginate, $join);
        return $info;
    }

    public function getSimpleSettingList($where=[],$field=true,$order,$paginate = DB_LIST_ROWS){
        return $this->modelSettingSalary->getList($where, $field, $order, $paginate);
    }


    /**
     * 新增编辑
     * @return array
     */
    public function SettingEdit($data=[]){

        $validate_result = $this->validateSettingSalary->scene('edit')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validateSettingSalary->getError()]; endif;


        $result = $this->modelSettingSalary->setInfo($data);

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '底薪规则' . $handle_text . '，group：' . $data['group_id']);

        return $result ? [RESULT_SUCCESS, '操作成功'] : [RESULT_ERROR, $this->modelPos->getError()];

    }

    /**
     * @param string $model 模型名
     * @param string $param
     * @param string $field
     * @param string $msg
     * @return array
     */
    public function setStatus($model = null, $param = null, $field = DATA_STATUS_NAME, $msg = '数据状态调整')
    {

        if (array_key_exists(DATA_STATUS_NAME,$param)&& $param[DATA_STATUS_NAME] ==DATA_NORMAL)
        {
            $where['group_id'] = $param['gid'];
            $where[DATA_STATUS_NAME] = DATA_NORMAL;
            $this->modelSettingSalary->setFieldValue($where, DATA_STATUS_NAME, 0);
        }

        return parent::setStatus($model,$param,$field,$msg);
//        $this->jump($this->logicAdminBase->setStatus('SalaryStructure', $this->param));
    }




}

