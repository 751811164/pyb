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
 * 权限组验证器
 */
class AuthGroup extends AdminBase
{
    
    // 验证规则
    protected $rule =   [
        
        'name' => 'require',
//        'number' => 'require',
        'shop_id'=>'require',
        'id' => 'require|gt:0',
        'refer_id'=> 'require|gt:0',

    ];

    // 验证提示
    protected $message  =   [
        
        'name.require' => '岗位名称不能为空',
//        'number.require' => '编号不能为空',
        'id' => '请选择非管理员岗位',
        'refer_id'=>'参考的岗位不能为空',
    ];

    // 应用场景
    protected $scene = [
        
        'add'  => ['name','shop_id'],
        'edit' => ['name'],
        'fastSet'=>['id','refer_id'],
    ];
    
}
