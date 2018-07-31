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

namespace app\admin\model;

/**
 * 会员卡类型模型
 */
class CardType extends AdminBase
{
    /**
     * 是否积分状态获取器
     */
    public function getIsPointTextAttr()
    {
        $status = [DATA_DISABLE => "<span class='badge bg-red'>否</span>", DATA_NORMAL => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['is_point']];
    }

    /**
     * 是否积分状态获取器
     */
    public function getIsPostsaveTextAttr()
    {
        $status = [DATA_DISABLE => "<span class='badge bg-red'>否</span>", DATA_NORMAL => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['is_postsave']];
    }

    /**
     * 是否积分状态获取器
     */
    public function getIsCountTextAttr()
    {
        $status = [DATA_DISABLE => "<span class='badge bg-red'>否</span>", DATA_NORMAL => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['is_count']];
    }
}
