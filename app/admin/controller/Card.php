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
class Card extends AdminBase
{

    /**
     *  会员卡列表
     */
    public function cardList()
    {
        $where = $this->logicCard->getWhere($this->param);
        $this->assign('list', $this->logicCard->getCardList($where));

        $this->assign('typeList', $this->logicCardType->getSimpleCardTypeList([],'id,concat("[",number,"]",name) name','id',false));
        return $this->fetch('card_list');
    }



    /**
     * 会员卡添加与编辑通用方法
     */
    public function CardCommon()
    {
        IS_POST && $this->jump($this->logicCard->cardEdit($this->param));

        $where[DATA_STATUS_NAME]=DATA_NORMAL;
        $this->assign('typeList', $this->logicCardType->getSimpleCardTypeList($where, 'id,name,number,is_point', '', false));
        //会员等级
        $this->assign('memberGradeList', $memberGradeList = $this->logicMemberGrade->getSimpleMemberGradeList($where, 'id,name', '', false));
    }


    /**
     * 会员卡添加
     */
    public function cardAdd()
    {
        $this->CardCommon();
        return $this->fetch('edit_modal');
    }

    /**
     * 会员卡编辑
     */
    public function cardEdit()
    {
        $this->CardCommon();
        $info = $this->logicCard->getCardJoinInfo(['c.id' => $this->param['id']]);

        $this->assign('info', $info);

        return $this->fetch('edit_modal');
    }

//    /**
//     * 会员卡删除
//     */
//    public function cardDel($id = 0)
//    {
//
//        $this->jump($this->logicCard->cardDel(['id' => $id]));
//    }

    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('Card', $this->param));
    }


}
