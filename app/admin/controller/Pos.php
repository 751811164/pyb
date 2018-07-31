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
 * 首页控制器
 */
class Pos extends AdminBase
{

    /**
     *  POS机列表
     */
    public function posList()
    {
        $where = $this->logicPos->getWhere($this->param);
        $this->assign('list', $this->logicPos->getPosList($where,'p.*,s.name as shop_name','p.create_time desc'));
        return $this->fetch('pos_list');
    }



    /**
     * POS机添加与编辑通用方法
     */
    public function PosCommon()
    {
        IS_POST && $this->jump($this->logicPos->posEdit($this->param));

        //$where[DATA_STATUS_NAME]=DATA_NORMAL;
        //$this->assign('shop_list', $this->logicShop->getShopList($where, 'id,name', '', false));
    }


    /**
     * POS机添加
     */
    public function posAdd()
    {
        $this->PosCommon();
        return $this->fetch('pos_edit');
    }

    /**
     * POS机编辑
     */
    public function posEdit()
    {
        $this->PosCommon();
        $info = $this->logicPos->getPosInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('pos_edit');
    }

    /**
     * POS机删除
     */
    public function posDel($id = 0)
    {

        $this->jump($this->logicPos->posDel(['id' => $id]));
    }

    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('Pos', $this->param));
    }


}
