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
 * 分类验证器
 */
class Category extends AdminBase
{

    // 验证规则
    protected $rule = [
        'name' => 'require|unique:category',
        'number' => 'require|unique:category',
    ];

    // 验证提示
    protected $message = [
        'name.require' => '分类名称不能为空',
        'name.unique' => '分类名称已经存在',
        'number.unique' => '编号已经存在',
        'number' => '编号不能为空',
    ];

    // 应用场景
    protected $scene = [
        'add' => ['name', 'number'],
        'edit' => ['name'],
    ];


    public function unique($value, $rule, $data, $field)
    {
        $id=empty($data['id'])?'':$data['id'];
        $rule = 'category,status=1&'.$field.'='.$value.','.$id.',id';
        $res  = parent::unique($value, $rule, $data, $field);
        return $res;
    }

}
