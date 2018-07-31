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
 * 机构验证器
 */
class Shop extends AdminBase
{

    // 验证规则
    protected $rule = [

//        'number' => 'require',
        'type_id' => 'headShopUnique',
        'name' => 'require',
        'representative' => 'require',
        'mobile' => 'require',
        'region' => 'require',
        'position' => 'require',
        'longitude' => 'require|gt:0',
        'latitude' => 'require',

        'cover_id' => 'gt:0',
        'identity_front_id' => 'gt:0',
        'identity_behind_id' => 'gt:0',
        'identity_hold_id' => 'gt:0',
        'business_license_id' => 'gt:0',
        'goods_license_id' => 'gt:0',
        'verify' => 'require|captcha',

    ];

    // 验证提示
    protected $message = [

        'number.require' => '编号不能为空',
        'type_id' => '只能有一个总部',
        'name.require' => '机构名称不能为空',
        'username.require' => '法人不能为空',
        'mobile.require' => '联系电话不能为空',
        'region.require' => '地区不能为空',
        'position.require' => '地址不能为空',
        'longitude'=>'请至少通过地图选址一次',
        'cover_id.gt' => '机构logo不能为空',
        'identity_front_id.gt' => '身份证正面照不能为空',
        'identity_behind_id.gt' => '身份证背面照不能为空',
        'identity_hold_id.gt' => '身份证手持照不能为空',
        'business_license_id.gt' => '营业许可证不能为空',
        'goods_license_id.gt' => '食品流通许可证不能为空',
        'verify.require' => '验证码不能为空',
        'verify.captcha' => '验证码不正确',
    ];

    // 应用场景
    protected $scene = [
        'auth' => ['id', 'identity_front_id', 'identity_behind_id', 'identity_hold_id', 'business_license_id', 'goods_license_id'],
        'all' => [ 'type_id', 'mobile', 'name', 'username', 'position','longitude', 'identity_front_id', 'identity_behind_id', 'identity_hold_id', 'business_license_id', 'goods_license_id',],
        //'edit' => ['name', 'content', 'category_id']
    ];

    /**
     * 总部唯一
     */
    public function headShopUnique($value, $rule, $data, $field)
    {
        $res=false;
        if ($data['type_id']==HEAD_OFFICE)
        {
            $res = model('shop')->where([DATA_STATUS_NAME => ['neq', DATA_DELETE], 'type_id' => HEAD_OFFICE, 'id' => ['neq', $data['id']]])->field('id')->find();
        }
        return empty($res);
    }
}
