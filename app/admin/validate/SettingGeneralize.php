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
class SettingGeneralize extends AdminBase
{

    // 验证规则
    protected $rule = [
        'group_id' => 'require',
        'start_time' => 'require',
        'end_time' => 'require',
        'sign_offline' => 'require',
        'sign_wechat' => 'require',
        'subscribe_wemall' => 'require',
        'sign_app' => 'require',


    ];

    // 验证提示
    protected $message = [
        'group_id' => '请先选择岗位后再添加',
        'start_time' => '请填写有效起始日期',
        'end_time' => '请填写有效截止日期',
        'sign_offline' => '线下推荐注册奖励金额不能为空',
        'sign_wechat' => '微信推荐注册奖励金额不能为空',
        'subscribe_wemall' => '推荐关注微信商城注册奖励金额不能为空',
        'sign_app' => 'APP推荐注册奖励金额不能为空',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['group_id', 'start_time', 'end_time','sign_offline','sign_wechat','subscribe_wemall','sign_app'],

    ];
}
