<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Recarditory | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\controller;

/**
 * 控制器
 */
class OtherAward extends AdminBase
{

    /**
     *  列表
     */
    public function awardList()
    {
        $where = $this->logicOtherAward->getWhere($this->param);
        $this->assign('list', $this->logicOtherAward->getAwardList($where));
        return $this->fetch('award_list');
    }



    /**
     * 添加与编辑通用方法
     */
    public function AwardCommon()
    {
        IS_POST && $this->jump($this->logicOtherAward->awardEdit($this->param));

        $where[DATA_STATUS_NAME]=DATA_NORMAL;
        $this->assign('shop_list', $this->logicShop->getShopList($where, 'id,name', '', false));
    }


    /**
     * 添加
     */
    public function awardAdd()
    {
        $this->AwardCommon();
        return $this->fetch('award_edit');
    }

    /**
     * 编辑
     */
    public function awardEdit()
    {
        $this->AwardCommon();
        $info = $this->logicOtherAward->getAwardInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('award_edit');
    }

    /**
     * 删除
     */
    public function awardDel($id = 0)
    {

        $this->jump($this->logicOtherAward->awardDel(['id' => $id]));
    }




}
