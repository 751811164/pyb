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

namespace app\common\validate;

/**
 * 会员验证器
 */
class Shop extends ValidateBase
{
    
    // 验证规则
    protected $rule = [
        'number'  => 'require',
        'name'  => 'require',
        'representative'  => 'require',
        'phone'  => 'require',
        'region'  => 'require',
        'address'  => 'require',
        'longitude'  => 'require',
        'latitude'  => 'require',
        'is_join'  => 'require',
    ];

    // 验证提示
    protected $message = [
        'number.require'    => '编号不能为空',
        'name.require'    => '店铺名不能为空',
        'representative.require'    => '责任人不能为空',
        'phone.require'    => '电话不能为空',
        'region.require'    => '区域不能为空',
        'address.require'    => '详细地址不能为空',
        'longitude.require'    => '经度不能为空',
        'latitude.require'     => '纬度不能为空',
       // 'is_join.require'     => '用户名已存在',
    ];

    // 应用场景
    protected $scene = [
        'add'  =>  ['number','name','representative','phone','region','address','longitude','latitude'],
    ];
}
