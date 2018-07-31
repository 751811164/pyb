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
 * 积分记录控制器
 */
class PointLog extends AdminBase
{

    #region ----------------------------------------积分奖励-----------------------------------------------

    /**
     * 积分奖励记录列表
     */
    public function plusLogList()
    {
        $where         = $this->logicPointLog->getWhere($this->param);
        $where['type'] = 1;
        $this->assign('list', $this->logicPointLog->getPointLogList($where));

        return $this->fetch('plus_log_list');
    }


    /**
     * 积分奖励添加
     */
    public function plusLogAdd()
    {

        if (IS_POST)
        {
            $this->param['type']=1;
            $this->jump($this->logicPointLog->PointLogEdit($this->param));
        }

        return $this->fetch('plus_edit_modal');
    }

    /**
     * 积分奖励保存
     */
    public function plusLogEdit()
    {
        if (IS_POST)
        {
            $this->param['type']=1;
            $this->jump($this->logicPointLog->PointLogEdit($this->param));
        }

    }

    #endregion


    /**
     * 查询会员信息
     */
    public function searchMember()
    {
        $where['number']         = $this->param['number'];
        $where[DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        $memberInfo              = $this->logicMember->getMemberInfo($where);
        return json($memberInfo);

    }

    #region ----------------------------------------积分冲减-----------------------------------------------

    /**
     * 积分冲减记录列表
     */
    public function minusLogList()
    {
        $where         = $this->logicPointLog->getWhere($this->param);
        $where['type'] = -1;
        $this->assign('list', $this->logicPointLog->getPointLogList($where));

        return $this->fetch('minus_log_list');
    }

    /**
     * 积分冲减添加
     */
    public function minusLogAdd()
    {

        if (IS_POST)
        {
            $this->param['type']=-1;
            $this->jump($this->logicPointLog->PointLogEdit($this->param));
        }

        return $this->fetch('minus_edit_modal');
    }

    /**
     * 积分冲减保存
     */
    public function minusLogEdit()
    {
        if (IS_POST)
        {
            $this->param['type']=1;
            $this->jump($this->logicPointLog->PointLogEdit($this->param));
        }
    }

    #endregion




    #region ----------------------------------------积分兑换-----------------------------------------------
    /**
     * 积分兑换记录列表
     */
    public function exchangeLogList()
    {
        $where = $this->logicPointLog->getWhere($this->param);
        $this->assign('list', $this->logicPointLog->getExchangePointLogList($where));

        return $this->fetch('exchange_log_list');
    }

    #endregion

    /**
     * 积分记录删除
     */
    public function logDel()
    {

        $this->jump($this->logicPointLog->PointLogDel($this->param));
    }


    /**
     * 审核状态设置
     */
    public function checking()
    {

        $this->jump($this->logicPointLog->checking($this->param));
    }


}
