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

namespace app\admin\logic;

use tree\PHPTree;

/**
 * 逻辑
 */
class Region extends AdminBase
{


    /**
     * 获取列表
     */
    public function getRegionList($where = [],$field=true)
    {


        if (is_array($where))
        {
            $cache_key = 'cache_region_' . md5(serialize($where));

            $cache_list = cache($cache_key);

            if (!empty($cache_list)) {

                return $cache_list;
            }
        }


        $list = $this->modelRegion->getList($where, $field, 'id', false);

        !empty($list)&& (is_array($where)) && cache($cache_key, $list);

        return $list;
    }

    /**
     * 生成父子相邻带level的规则列表
     * @param $list
     * @return mixed
     */
    public function makeTreeForHtml($list, $options=[])
    {
        $config = [
            /* 主键 */
            'primary_key' => 'id',
            /* 父键 */
            'parent_key' => 'upid',
            /* 展开属性 */
            'expanded_key' => 'expanded',
            /* 叶子节点属性 */
            'leaf_key' => 'leaf',
            /* 孩子节点属性 */
            'children_key' => 'children',
            /* 是否展开子节点 */
            'expanded' => false
        ];
        $config = array_merge($config, $options);
        $tree   = PHPTree::makeTreeForHtml($list, $config);
        if (is_array($list))
        {
            $list = $tree;
        }
        else
        {
            //collection replace添加替换方法
            $list->replace($tree);
        }

        return $list;
    }

    /**
     * 生成层级父子带level的规则列表
     * @param $list
     * @return mixed
     */
    public function makeTree($list, $options=[], $closure = false)
    {

        $config = [
            /* 主键 */
            'primary_key' => 'id',
            /* 父键 */
            'parent_key' => 'upid',
            /* 展开属性 */
            'expanded_key' => 'expanded',
            /* 叶子节点属性 */
            'leaf_key' => 'leaf',
            /* 孩子节点属性 */
            'children_key' => 'child',
            /* 是否展开子节点 */
            'expanded' => false
        ];
        $config = array_merge($config, $options);
        $list = PHPTree::makeTree($list, $config, $closure);
        return $list;
    }





    /**
     * 获取自己及父分类ids
     * @param int $region_id 分类id
     * @param int $type 返回类型 1:数组 0:字符串
     * @return array|string
     */
    public function getParentRegionIds($region_id = 0, $type = 1)
    {

        $info = $this->getRegionInfo(['id' => $region_id]);
        $ids  = $info["path"].",".$region_id;
        return $type ? explode(",", $ids): $ids;
    }


    /**
     * 获取某个的 儿子 孙子  重子重孙 的 id
     * @param type $cat_id
     */
    function getCatGrandson($cat_id)
    {
        $GLOBALS['catGrandson']     = [];
        $GLOBALS['region_id_arr'] = [];
        // 先把自己的id 保存起来
        $GLOBALS['catGrandson'][] = $cat_id;
        // 把整张表找出来
        $GLOBALS['region_id_arr'] = $this->modelRegion->getColumn([DATA_STATUS_NAME => DATA_NORMAL], "pid", "id");
        // 先把所有儿子找出来
        $son_id_arr = $this->modelRegion->getColumn([DATA_STATUS_NAME => DATA_NORMAL, "pid" => $cat_id], "id");
        foreach ($son_id_arr as $k => $v)
        {
            $this->getCatGrandson2($v);
        }
        return $GLOBALS['catGrandson'];
    }


    /**
     * 递归调用找到 重子重孙
     * @param type $cat_id
     */
    function getCatGrandson2($cat_id)
    {
        $GLOBALS['catGrandson'][] = $cat_id;
        foreach ($GLOBALS['region_id_arr'] as $k => $v)
        {
            // 找到孙子
            if ($v == $cat_id)
            {
                $this->getCatGrandson2($k); // 继续找孙子
            }
        }
    }




}
