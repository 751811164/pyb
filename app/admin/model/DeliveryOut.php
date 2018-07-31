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
 * 模型
 */
class DeliveryOut extends AdminBase
{
//    protected $type=[
//        'valid_time'=>'timestamp:Y-m-d'
//    ];
//
//    public function getValidTimeAttr($value,$data)
//    {
//
//        if (empty($data['id']))
//        {
//            $value=strtotime('+1 month');
//            return date("Y-m-d",$value);
//        }
//        else
//        {
//            if (empty($value))
//            {
//                return '';
//            }
//            else
//            {
//                return date("Y-m-d",$value);
//            }
//        }
//    }




    public function getUseStatusTextAttr()
    {
        $status = [-1 => '终止', 0 => '未使用', 1 => '已使用',2=>'部分使用'];

        return $status[$this->data['use_status']];
    }

    public function getStatusTextAttr()
    {
        $status = [DATA_DELETE => '删除', DATA_DISABLE => '<span class="badge bg-red">未审核</span>', DATA_NORMAL => '<span class="badge bg-green">已审核</span>'];

        return $status[$this->data[DATA_STATUS_NAME]];
    }


}
