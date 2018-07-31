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
 * 请假逻辑
 */
class Leave extends AdminBase
{

    /**
     * 班次信息
     */
    public function getLeaveInfo($where = [], $field = true)
    {
        $info = $this->modelLeave->getInfo($where, $field);
        return $info;
    }

    /**
     * 获取员工班次列表
     */
    public function getLeaveList($where = [], $field = 'a.username,a.number,a.portrait_id,g.name group_name,s.name shop_name,l.*,a.id admin_id', $order = '', $paginate = DB_LIST_ROWS)
    {

        $this->modelLeave->alias('l');

        $join = [
            [SYS_DB_PREFIX.'admin a', 'a.id = l.admin_id', 'RIGHT'],
            [SYS_DB_PREFIX.'auth_group_access ga', 'ga.admin_id = a.id', 'LEFT'],
            [SYS_DB_PREFIX.'auth_group g', 'g.id = ga.group_id', 'LEFT'],
            [SYS_DB_PREFIX.'shop s', 's.id = g.shop_id', 'LEFT'],
        ];

        $where['a.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['l.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['a.id'] = ['neq', SYS_ADMINISTRATOR_ID];

         $info= $this->modelLeave->getList($where, $field, $order, $paginate, $join);
        return $info;
    }

    public function getSimpleArrageList($where=[],$field=true,$order,$paginate = DB_LIST_ROWS){
        return $this->modelLeave->getList($where, $field, $order, $paginate);
    }


    /**
     * 获取班次列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];

        !empty($data['search_data']) && $where['a.username|a.number'] = ['like', '%'.$data['search_data'].'%'];
        return $where;
    }



    /**
     * 班次编辑
     */
    public function LeaveEdit($data = [])
    {
        if (!empty($data['id']))
        {
            $info= $this->modelLeave->getInfo(['id'=>$data['id']]);
            if ($info&&$info["check_status"]==1)
            {
              return  [RESULT_ERROR, '已审核,不能再次修改'];
            }
        }
        //保存班次信息
        $validate_result = $this->validateLeave->scene('edit')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validateLeave->getError()]; endif;

        $url = url('leaveList');
        $result = $this->modelLeave->setInfo($data);

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '请假管理' . $handle_text . '，admin_id：' . $data['admin_id']);

        return $result ? [RESULT_SUCCESS, '请假编辑成功',$url]: [RESULT_ERROR, $this->modelLeave->getError()];
    }




}

