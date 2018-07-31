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
class SettingCard  extends AdminBase
{



    /**
     * 添加与编辑通用方法
     */
    public function common()
    {
        $typeList = $this->logicCardType->getCardTypeList([],'*','',false);
        $list = $this->logicCard->getCardList([],'*','',false);

        $this->assign('list',$list);
        $this->assign('typeList',$typeList);
    }




}
