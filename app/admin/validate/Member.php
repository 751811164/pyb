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
class Member extends AdminBase
{

    // 验证规则
    protected $rule = [
        'name' => 'require',
        'card_id'=>'require',
        'mobile' => 'require',
        'shop_id' => 'require',

        'password' => 'require|confirm|length:6,20',

    ];

    // 验证提示
    protected $message = [
        'name' => '类型名不能为空',
        'card_id' => '请选择办理的会员卡',
        'shop_id' => '请选择发卡机构',
        'mobile' => '手机号不能为空',
        'password.require' => '密码不能为空',
        'password.confirm' => '两次密码不一致',
        'password.length' => '密码长度为6-20字符',
    ];

    // 应用场景
    protected $scene = [
        'edit' => ['name','card_id','shop_id']
    ];
}
