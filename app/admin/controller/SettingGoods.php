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
class SettingGoods  extends AdminBase
{



    /**
     * 添加与编辑通用方法
     */
    public function common($info)
    {
        if (!empty($info))
        {
            $goods_ids= $info->getAttr('goods_ids');
            $where['g.id'] =['in',$goods_ids];

//            $join = [
//                [SYS_DB_PREFIX.'goods_profile gf', 'gf.goods_id = g.id ', 'LEFT'],
//            ];
            $list  = collection( $this->logicGoods->getGoodsList($where, "g.id,gf.barcode1,gf.name,specification,unit,1 as checked", "", false))->toArray();
        }
        else
        {
            $list=[];
        }
        $brandList = $this->logicBrand->getBrandList([],true,'',false);
        $this->assign('brandList', $brandList);

        $this->assign('list', $list);

    }




}
