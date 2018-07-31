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

namespace addon\shopselect\logic;

use app\common\model\Addon;

/**
 * 插件逻辑
 */
class Index extends Addon
{

    /**
     * 组合下拉框选项信息
     */
    public function combineOptions( $list = [], $default_option_text = '')
    {
        
        $data = "<option value =''>$default_option_text</option>";
        
        foreach ($list as $vo)
        {
            $data .= "<option ";
            
            $data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
        }
        
        return $data;
    }
    
    /**
     * 获取列表
     */
    public function getShopList($where = [])
    {
        $shop_id = session('auth_shop_permission')['shop_id'];
        if (!IS_ROOT&&$shop_id!=SHOP_ID)
        {
            $where['id'] = $shop_id;
        }
        $list = $this->modelShop->getList($where, true, 'id', false);
        return $list;
    }
}
