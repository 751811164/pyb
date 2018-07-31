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
 * 供应商验证器
 */
class Supplier extends AdminBase
{

    // 验证规则
    protected $rule = [

//        'number' => 'require',
        'name' => 'require',
        'address' => 'require',
        'legalname' => 'require',
        'mobile' => 'require',
        'settlement_period' => 'require',


//        'identity_front_id' => 'gt:0',
//        'identity_behind_id' => 'gt:0',
//        'identity_hold_id' => 'gt:0',
//        'business_license_id' => 'gt:0',
//        'goods_license_id' => 'gt:0',
    ];

    // 验证提示
    protected $message = [

        'name.require' => '名称不能为空',
        'address.require' => '地址不能为空',
        'legalname.require' => '法人不能为空',
        'mobile.require' => '联系电话不能为空',
        'settlement_period.require' => '结算周期不能为空',


//        'identity_front_id.gt' => '身份证正面照不能为空',
//        'identity_behind_id.gt' => '身份证背面照不能为空',
//        'identity_hold_id.gt' => '身份证手持照不能为空',
//        'business_license_id.gt' => '营业许可证不能为空',
//        'goods_license_id.gt' => '食品流通许可证不能为空',
    ];

    // 应用场景
    protected $scene = [

        'edit' => ['name', 'address', 'legalname', 'mobile', 'settlement_period']
    ];
}
