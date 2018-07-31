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
class SettingCategory extends AdminBase
{

    protected $type=[
        'rules'=>'json',
        'start_time'=>'timestamp',
        'end_time'=>'timestamp',
    ];



    public function getStatusTextAttr()
    {
        $status = [DATA_DELETE => '回收站', DATA_DISABLE => "<span class='badge bg-red'>停用</span>", DATA_NORMAL => "<span class='badge bg-green'>启用</span>"];

        return $status[$this->data[DATA_STATUS_NAME]];

    }

    public function getTypeTextAttr()
    {
        $status = [ '类别销售额提成', "类别毛利额提成", ];

        return $status[$this->data['type']];

    }

    public function getModeTextAttr()
    {
        $status = [ '达成率计提', "固定值计提",];

        return $status[$this->data['mode']];

    }

}
