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

namespace app\admin\validate;

/**
 * 请假管理验证器
 */
class Leave extends AdminBase
{

    // 验证规则
    protected $rule = [
        'admin_id' => 'require',
        'start_time' => 'date',
        'end_time' => 'date|endAfterBefore',
        'reason' => 'require',

    ];

    // 验证提示
    protected $message = [
        'admin_id.require' => '员工不能为空',
        'start_time.date' => '起始时间不符合要求',
        'end_time.date' => '结束时间不符合要求',
        'end_time.endAfterBefore' => '结束时间必须大于开始时间',
        'reason.require' => '请假原因不能为空',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['admin_id', 'start_time', 'end_time','reason']
    ];

    //验证日期
    protected function endAfterBefore($value,$rule,$data){
        $start = $data['start_time'];
        $end   = $data['end_time'];
        $diff = strtotime($end)-strtotime($start);
        return $diff>=0;
    }
}
