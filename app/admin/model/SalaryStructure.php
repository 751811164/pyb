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
class SalaryStructure extends AdminBase
{

    /**
     * 状态获取器
     * @return mixed
     */
    public function getStatusTextAttr()
    {
        $status = [DATA_DELETE => '回收站', DATA_DISABLE => "<span class='badge bg-red'>停用</span>", DATA_NORMAL => "<span class='badge bg-green'>启用</span>"];

        return $status[$this->data[DATA_STATUS_NAME]];
    }


    public function getSalaryTypesTextAttr(){
        $where['id'] =['in',$this->data['salary_types']] ;
        $list =  $this->modelSalaryType->getList($where,'name,symbol');
        $names = "";
        foreach ($list as $item)
        {
            if ($item->getAttr('symbol'))
            {
                $names .= "+".$item->getAttr('name');
            }
            else
            {
                $names .= "-".$item->getAttr('name');
            }
        }
        
        return trim($names,"-+") ;
    }

}
