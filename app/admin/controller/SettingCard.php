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
        $list = $this->logicCard->getCardList([],'c.*,ct.is_postsave,ct.is_count,ct.is_point','',false);
        $list0=$list1=[];
        foreach ($list as $key=>$item)
        {
            //储值
            if ($item['is_postsave']==1)
            {
                $list0[]= $item;
            }
            //计次
            if ($item['is_count']==1)
            {
                $list1[]= $item;
            }
        }

        $this->assign('list0', $list0);
        $this->assign('list1', $list1);
    }




}
