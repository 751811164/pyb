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
class Member extends AdminBase
{

    /**
     *  会员列表
     */
    public function memberList()
    {
        $this->assign('cardTree',$this->logicCard->getCardTree());
        $where = $this->logicMember->getWhere($this->param);
        $this->assign('list', $this->logicMember->getMemberList($where));

        return $this->fetch('member_list');
    }



    /**
     * 会员添加与编辑通用方法
     */
    public function MemberCommon()
    {
        IS_POST && $this->jump($this->logicMember->memberEdit($this->param));

        $where[DATA_STATUS_NAME]=DATA_NORMAL;
        $this->assign('typeList', $this->logicCardType->getSimpleCardTypeList($where, 'id,name,number', '', false));
        $this->assign('cardList', $this->logicCard->getSimpleCardList($where, 'id,name,number,type_id', '', false));
        $this->assign('shopList', $this->logicShop->getSimpleShopList($where, 'id,name,number', '', false));

        $join = [
            [SYS_DB_PREFIX.'auth_group_access ga', 'ga.admin_id = a.id', 'LEFT'],
            [SYS_DB_PREFIX.'auth_group g', 'g.id = ga.group_id', 'LEFT'],
            [SYS_DB_PREFIX.'shop s', 's.id = g.shop_id', 'LEFT'],
        ];
        $where_admin['a.'.DATA_STATUS_NAME] =DATA_NORMAL;
        $where_admin['a.id']  = ['neq', SYS_ADMINISTRATOR_ID];//列表不显示超级管理员
        $this->assign('adminList', $this->logicAdmin->getSimpleAdminList($where_admin, 'a.id,a.username,a.number,g.shop_id', '', false,$join));
    }


    /**
     * 会员添加
     */
    public function memberAdd()
    {
        $this->MemberCommon();
        $this->assign('children', []);
        return $this->fetch('edit_modal');
    }

    /**
     * 会员编辑
     */
    public function memberEdit()
    {
        $this->MemberCommon();
        $info = $this->logicMember->getMemberJoinInfo(['m.id' => $this->param['id']]);
        $where_chilren['member_id'] = $this->param['id'];
        $where_chilren[DATA_STATUS_NAME] = DATA_NORMAL;
        $children = $this->logicMemberChildren->getSimpleMemberChildrenList($where_chilren,true,'',false);

        $where_storage['member_id'] = $this->param['id'];
        $where_storage['st.'.DATA_STATUS_NAME] = DATA_NORMAL;
        $storageList = $this->logicStorage->getStorageList($where_storage,'st.*, gp.name goods_name,sh.name shop_name','',false);

        $this->assign('info', $info);
        $this->assign('children', $children);
        $this->assign('storageList', $storageList);

        return $this->fetch('edit_modal');
    }

    /**
     * 会员删除
     */
    public function memberDel($id = 0)
    {

        $this->jump($this->logicMember->memberDel(['id' => $id]));
    }

    /**
     * 会员子女删除
     */
    public function childDel($id=0){
        $this->jump($this->logicMemberChildren->memberChildrenDel(['id' => $id]));
    }

    /**
     * 数据状态设置
     */
    public function setStatus()
    {

        $this->jump($this->logicAdminBase->setStatus('Member', $this->param));
    }


}
