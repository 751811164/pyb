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
 * 会员验证器
 */
class Admin extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        'username' => 'require|unique:admin',
        'birthday' => 'require',
        'identity' => 'require',
        'portrait_id' => 'gt:0',
        'address' => 'require',
//        'number'=>'require',
        'entry_time'=>'require',
        'group_id' => 'require',
        'mobile' => 'require|unique:admin',
        'email' => 'require|email|unique:admin',
        'verify' => 'require|captcha',
        //fastset
        'password' => 'require|confirm|length:6,20',
        'ids'=>'require',
    ];
    
    // 验证提示
    protected $message  =   [
        'number'=>'编号不能为空',
        'entry_time'=>'入职时间不能为空',
        'username.require' => '姓名不能为空',
        'username.unique' => '姓名已存在',
        'password.require' => '密码不能为空',
        'password.confirm' => '两次密码不一致',
        'password.length' => '密码长度为6-20字符',
        'email.require' => '邮箱不能为空',
        'email.email' => '邮箱格式不正确',
        'email.unique' => '邮箱已存在',
        'mobile.require' => '手机号不能为空',
        'mobile.unique' => '手机号已存在',
        'address.require' => '地址不能为空',
        'portrait_id.gt' => '用户头像不能为空',
        'verify.require' => '验证码不能为空',
        'verify.captcha' => '验证码不正确',
        'group_id' => '请选择岗位',
        'identity' => '身份证不能为空',
        'ids'=>'请选择员工'
    ];

    // 应用场景
    protected $scene = [
        'add'=>['username','gender','birthday','identity','portrait_id','address','entry_time','group_id','mobile','password'],
        'edit'  =>  ['username','gender','birthday','identity','portrait_id','address','entry_time','group_id','mobile','password'],
        'fastSet'  =>  ['ids','entry_time','group_id'],
    ];
}
