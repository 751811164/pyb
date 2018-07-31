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
 * 品牌验证器
 */
class Brand extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        'number'          => 'require',
        'name'          => 'require',
//        'origin'          => 'require',
    ];

    // 验证提示
    protected $message  =   [
        'number.unique'        => '编号不能为空',
        'name.require'         => '分类名称不能为空',
//        'origin.unique'          => '产地不能为空',
    ];
    
    // 应用场景
    protected $scene = [
        'edit'  =>  ['number','name',],
    ];
}
