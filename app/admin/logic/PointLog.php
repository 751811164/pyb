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

use think\Db;

/**
 * 积分记录逻辑
 */
class PointLog extends AdminBase
{

    //    /**
    //     * 获取积分记录信息
    //     */
    //    public function getPointLogInfo($where = [], $field = true)
    //    {
    //        return $this->modelPointLog->getInfo($where, $field);
    //    }

    //    /**
    //     * 获取积分记录信息
    //     */
    //    public function getPointLogJoinInfo($where = [], $field = "pl.*,add.username admin_add_name,edit.username admin_edit_name")
    //    {
    //        $this->modelPointLog->alias('pl');
    //        $join                          = [
    //            [SYS_DB_PREFIX.'admin add', 'add.id = pl.admin_add_id', 'LEFT'],
    //            [SYS_DB_PREFIX.'admin edit', 'edit.id = pl.admin_edit_id', 'LEFT'],
    //        ];
    //        $where['pl.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
    //        return $this->modelPointLog->getInfo($where, $field, $join);
    //    }

    /**
     * 获取积分增减记录列表
     */
    public function getPointLogList($where = [], $field = "m.name,m.number,pl.*,add.username admin_add_name,check.username admin_check_name", $order = 'id desc', $paginate = 0)
    {
        $this->modelPointLog->alias('pl');
        $where['pl.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $join                          = [
            [SYS_DB_PREFIX.'member m', 'm.id = pl.member_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin add', 'add.id = pl.admin_add_id', 'LEFT'],
            [SYS_DB_PREFIX.'admin check', 'check.id = pl.admin_check_id', 'LEFT'],
        ];
        return $this->modelPointLog->getList($where, $field, $order, $paginate, $join);
    }

    /**
     * 获取积分消费记录列表
     */
    public function getExchangePointLogList($where = [], $field = "m.name member_name,m.number,pl.*,gb.barcode,gf.name goods_name", $order = 'id desc', $paginate = 0)
    {
        $this->modelPointLog->alias('pl');
        $where['pl.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $where['pl.'.'type'] = 0;
        $join                          = [
            [SYS_DB_PREFIX.'member m', 'm.id = pl.member_id', 'LEFT'],
            [SYS_DB_PREFIX.'goods_barcode gb', 'gb.id = pl.barcode_id', 'LEFT'],
            [SYS_DB_PREFIX.'goods_profile gf', 'gf.id = gb.profile_id', 'LEFT'],
        ];
        return $this->modelPointLog->getList($where, $field, $order, $paginate, $join);
    }
    /**
     * 获取积分记录列表
     */
    public function getSimplePointLogList($where = [], $field = "pl.*", $order = '', $paginate = 0, $join = [])
    {
        $this->modelPointLog->alias('pl');
        $where['pl.'.DATA_STATUS_NAME] = ['neq', DATA_DELETE];

        return $this->modelPointLog->getList($where, $field, $order, $paginate, $join);
    }


    public function getWhere($data = [])
    {
        $where = [];
        !empty($data['search_data']) && $where['m.name'] = ['like', '%'.$data['search_data'].'%'];
        if (!empty($data['start_time']) || !empty($data['end_time']))
        {
            $date                    = getdate();
            $end_time                = empty($data['end_time']) ? mktime(0, 0, 0, $date['mon'], $date['mday'] + 1, $date['year']): $data['end_time']." 23:59:59";
            $start_time              = empty($data['start_time']) ? 0: $data['start_time'];
            $where['pl.create_time'] = ['between time', [$start_time, $end_time]];
        }
        return $where;
    }

    /**
     * 积分记录信息编辑
     */
    public function pointLogEdit($data = [])
    {

        $validate_result = $this->validatePointLog->scene('edit')->check($data);

        if (!$validate_result)
        {

            return [RESULT_ERROR, $this->validatePointLog->getError()];
        }

        if (empty($data['id']))
        {
            $data['admin_add_id'] = ADMIN_ID;
            $handle_text          = '新增';
        }
        $text = $data['type'] == 1 ? '奖励': '冲减'.$data['num'].'积分';

        $result = $this->modelPointLog->setInfo($data);

        $result && action_log($handle_text, '积分记录'.$handle_text.$text.'会员id:'.$data['member_id']);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $this->modelPointLog->getError()];
    }


    /**
     * 积分记录删除
     */
    public function pointLogDel($data = [])
    {

        $where['check_status']=DATA_DISABLE;
        $where['id']=['in',$data['ids']];
        $result = $this->modelPointLog->deleteInfo($where);

        $result && action_log('删除', '积分记录删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功']: [RESULT_ERROR, $this->modelPointLog->getError()?:"未做更改"];
    }

    /**
     * 审核
     */
    public function checking($data = [])
    {
        //查询未审核的增减积分信息
        $logList = $this->modelPointLog->getList(['id' => ['in', $data['ids']], 'check_status' => DATA_DISABLE], 'id,num,type,member_id', '', false);
        $list    = [];
        $ids     = [];
        foreach ($logList as $key => $item)
        {
            $ids[]                       = $item['id'];
            $list[$key]['id']            = $item['member_id'];
            $list[$key]['current_point'] = ['exp', 'current_point + '.$item['num'] * $item['type']];

        }
        $fn = function() use ($list, $ids) {
            $this->modelMember->setList($list, true);//变更会员的当前积分信息
            $this->modelPointLog->setInfo(['check_status' => DATA_NORMAL], ['id' => ['in', $ids]]);//修改审核状态
        };

        closure_list_exe([$fn]);
        action_log('审核积分', '积分记录'.http_build_query($logList));

        return  [RESULT_SUCCESS, '操作成功'];
    }

}
