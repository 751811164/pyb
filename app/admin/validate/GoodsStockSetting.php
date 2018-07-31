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
class GoodsStockSetting extends AdminBase
{

    // 验证规则
    protected $rule = [

        'stock_ceil' => 'checkEmpty',
        'stock_floor' => 'checkEmpty',

    ];

    // 验证提示
    protected $message = [

        'stock_ceil' => '存在为0的库存上限',
        'stock_floor' => '存在未0的库存下限',
    ];

    // 应用场景
    protected $scene = [

        'edit' => [
            'stock_ceil',
            'stock_floor',

        ],
    ];


    /**
     * 检测是否有为空
     */
    public function checkEmpty($value, $rule, $data, $field)
    {
        $res = true;
        foreach ($value as $ke => $val)
        {
            if (empty($val))
            {
                $res=false;
                break;
            }
        }
        return $res;

    }



}

