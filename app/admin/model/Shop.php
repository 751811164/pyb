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
 * 店铺模型
 */
class Shop extends AdminBase
{

    /**
     * 状态获取器
     * @return mixed
     */
    public function getStatusTextAttr()
    {
        $status = [DATA_DELETE => '回收站', DATA_DISABLE => "<span class='badge bg-red'>暂停营业</span>", DATA_NORMAL => "<span class='badge bg-green'>营业</span>"];

        return $status[$this->data[DATA_STATUS_NAME]];
    }


    /**
     * 审核状态获取器
     * @return mixed
     */
    public function getCheckStatusTextAttr()
    {
        $status = [DATA_DELETE => "<span class='badge bg-gray'>不通过</span>", DATA_DISABLE => "<span class='badge bg-yellow'>待审核</span>", DATA_NORMAL => "<span class=''>已审核</span>"];

        return $status[$this->data['check_'.DATA_STATUS_NAME]];

    }

    /**
     * 店铺加盟状态获取器
     */
    public function getISJoinTextAttr()
    {
        $status = [ DATA_NO => "否", DATA_YES => "<span class='badge bg-blue'>是</span>"];
        return $status[$this->data["is_join"]];
    }


    /**
     * 商品状态获取器
     * @return mixed
     */
    public function getGenderTextAttr()
    {
        $status = [DATA_DELETE => '未知', DATA_DISABLE => "女", DATA_NORMAL => "男"];

        return $status[$this->data['gender']];
    }


}
