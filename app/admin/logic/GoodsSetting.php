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

namespace app\admin\logic;

/**
 * 商品设置逻辑
 */
class GoodsSetting extends AdminBase
{

    /**
     *  获取信息
     */
    public function getSettingInfo($where = [], $field = true)
    {

        return $this->modelGoodsSetting->getInfo($where, $field);
    }

    /**
     * 保存设置
     * @param array $data
     */
    public function setSeting($data=[]){

        if (empty($data['price_adjust_type']))
        {
            $data['price_adjust_type'] = [];
        }
        $result= $this->modelGoodsSetting->setInfo($data);

        $handle_text = empty($data['id']) ? '新增' : '编辑';

        $result && action_log($handle_text, '商品设置' . $handle_text );

        return $result ? [RESULT_SUCCESS, '商品设置编辑成功']: [RESULT_ERROR, $this->modelGoodsSetting->getError()];
    }

}
