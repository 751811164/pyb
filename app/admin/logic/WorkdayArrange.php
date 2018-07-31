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
 * 班次逻辑
 */
class WorkdayArrange extends AdminBase
{

    /**
     * 班次信息
     */
    public function getArrangeInfo($where = [], $field = true)
    {
        $info = $this->modelWorkdayArrange->getInfo($where, $field);
        return $info;
    }

    /**
     * 获取员工班次列表
     */
    public function getArrangeList($where = [], $field = 'wa.id,a.username,a.number,a.portrait_id,g.name group_name,wa.*,a.id admin_id', $order = '', $paginate = DB_LIST_ROWS)
    {

        $this->modelWorkdayArrange->alias('wa');

        $join = [
            [SYS_DB_PREFIX.'admin a', 'a.id = wa.admin_id', 'RIGHT'],
            [SYS_DB_PREFIX.'auth_group_access ga', 'ga.admin_id = a.id', 'LEFT'],
            [SYS_DB_PREFIX.'auth_group g', 'g.id = ga.group_id', 'LEFT'],
        ];

        $where['a.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['wa.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['a.id'] = ['neq', SYS_ADMINISTRATOR_ID];

         $info= $this->modelWorkdayArrange->getList($where, $field, $order, $paginate, $join);
        return $info;
    }

    public function getSimpleArrageList($where=[],$field=true,$order,$paginate = DB_LIST_ROWS){
        return $this->modelWorkdayArrange->getList($where, $field, $order, $paginate);
    }


    /**
     * 获取班次列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];

        !empty($data['search_data']) && $where['a.username|a.number|a.mobile'] = ['like', '%'.$data['search_data'].'%'];
        return $where;
    }



    /**
     * 班次编辑
     */
    public function arrangeEdit($data = [])
    {
        //保存班次信息
        $validate_result = $this->validateWorkdayArrange->scene('edit')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validateWorkdayArrange->getError()]; endif;

        $data[DATA_STATUS_NAME]=DATA_NORMAL;

        $this->modelWorkdayArrange->setInfo([DATA_STATUS_NAME=>DATA_DISABLE],['admin_id'=>$data['admin_id']]);
        $result= $this->modelWorkdayArrange->setInfo($data);
        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '班次管理' . $handle_text . '，admin_id：' . $data['admin_id']);

        return $result ? [RESULT_SUCCESS, '班次编辑成功']: [RESULT_ERROR, $this->modelWorkdayArrange->getError()];
    }


    public function setStatusDisable($where = [])
    {
        $this->modelWorkdayArrange->setFieldValue($where, DATA_STATUS_NAME, 0);
    }






}

