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

namespace app\admin\controller;

/**
 * 控制器
 */
class Storage extends AdminBase
{

    /**
     *  寄存列表
     */
    public function storageList()
    {

        //非总部看自己门店库粗
        $shop_permission = session("auth_shop_permission");
        if (empty($this->param['shop_id'])&&!IS_ROOT&&$shop_permission['shop_id']!=SHOP_ID )
        {
            $this->param['shop_id'] = $shop_permission['shop_id'];
        }
        $where = $this->logicStorage->getWhere($this->param);


        $this->assign('list', $this->logicStorage->getStorageList($where));
        return $this->fetch('storage_list');
    }


    /**
     * 取货记录
     */
    public function takeLogList(){
//        $where = $this->logicStorageTakeLog->getWhere($this->param);
        $this->assign('list', $this->logicStorageTakeLog->getLogList($this->param,true,'id desc',false));
        return $this->fetch('take_log_list');
    }


}
