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
 * 会员卡类型控制器
 */
class CardType extends AdminBase
{
    /**
     * 会员卡类型列表
     */
    public function typeList()
    {
        $where=$this->logicCardType->getWhere($this->param);
        $this->assign('list', $this->logicCardType->getCardTypeList($where));

        return $this->fetch('card_type_list');
    }


    /**
     * 会员卡类型添加
     */
    public function typeAdd()
    {
        
        IS_POST && $this->jump($this->logicCardType->CardTypeEdit($this->param));
        
        return $this->fetch('edit_modal');
    }
    
    /**
     * 会员卡类型编辑
     */
    public function typeEdit()
    {
        
        IS_POST && $this->jump($this->logicCardType->CardTypeEdit($this->param));
        
        $info = $this->logicCardType->getCardTypeJoinInfo(['ct.id' => $this->param['id']]);
        
        $this->assign('info', $info);
        
        return $this->fetch('edit_modal');
    }
    

    /**
     * 会员卡类型删除
     */
    public function typeDel($id = 0)
    {
        
        $this->jump($this->logicCardType->CardTypeDel(['id' => $id]));
    }


    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('CardType', $this->param));
    }




}
