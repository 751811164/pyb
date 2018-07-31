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
 * 成员模型
 */
class Admin extends AdminBase
{
    // 设置返回数据集的对象名
//    protected $resultSetType = 'collection';

    public function adminPermission()
    {
        return $this->hasOne('AdminPermission','admin_id','id');
    }
    /**
     * 状态获取器
     * @return mixed
     */
    public function getStatusTextAttr()
    {
        $status = [DATA_DELETE => '回收站', DATA_DISABLE => "<span class='badge bg-red'>离职</span>", DATA_NORMAL => "<span class='badge bg-green'>在职</span>"];

        return $status[$this->data[DATA_STATUS_NAME]];
    }


    /**
     * PC收银状态获取器
     * @return mixed
     */
    public function getPcCashierTextAttr()
    {

        $status = [DATA_NO => "<span class='badge bg-yellow'>否</span>", DATA_YES => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['pc_cashier']?:DATA_NO];
    }

    /**
     *mob收银状态获取器
     * @return mixed
     */
    public function getMobCashierTextAttr()
    {
        $status = [DATA_NO => "<span class='badge bg-yellow'>否</span>", DATA_YES => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['mob_cashier']?:DATA_NO];
    }


    /**
     * 库存状态获取器
     * @return mixed
     */
    public function getTakeStockTextAttr()
    {
        $status = [DATA_NO => "<span class='badge bg-yellow'>否</span>", DATA_YES => "<span class='badge bg-green'>是</span>"];

        return $status[$this->data['take_stock']?:DATA_NO];
    }

    /**
     * 性别状态获取器
     * @return mixed
     */
    public function getGenderTextAttr()
    {
        $status = [DATA_DELETE => '未知', DATA_DISABLE => "女", DATA_NORMAL => "男"];

        return $status[$this->data['gender']];
    }




}
