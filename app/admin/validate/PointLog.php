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
 * 验证器
 */
class PointLog extends AdminBase
{

    // 验证规则
    protected $rule = [

        'member_id' => 'require',
        'num' => 'require',

    ];

    // 验证提示
    protected $message = [

        'member_id.require' => '会员信息不能为空',
        'num.unique' => '积分数量',

    ];

    // 应用场景
    protected $scene = [

        'edit' => ['member_id','num'],
    ];

}
