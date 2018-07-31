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
 * 逻辑
 */
class AdminPermission extends AdminBase
{


    public function setStatus($model = 'AdminPermission', $data = [], $field = DATA_STATUS_NAME, $msg = "数据状态调整")
    {

        $keys = array_keys($data);
        if (count($keys) != 2)
        {
            return [RESULT_ERROR, "数据有误"];
        }


        $result = $this->modelAdminPermission->setFieldValue(['admin_id' => $data['ids']], $keys[1],$data[ $keys[1]]);


        $result && action_log('数据状态', $msg.'，model：'.$model.'，admin_id：'.$data['ids'].'，'.$keys[1].'：'.$data[ $keys[1]]);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $this->modelAdminPermission->getError()];
    }

}

