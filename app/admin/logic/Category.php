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
 * 商品分类逻辑
 */
class Category extends AdminBase
{
    // 菜单Select结构
    public static $categorySelect = [];

    public function getCategoryColumn($where = [], $field = 'id', $key = '')
    {
        if (array_key_exists(DATA_STATUS_NAME, $where))
        {
            $where[DATA_STATUS_NAME] = DATA_NORMAL;
        }
        return $this->modelCategory->getColumn($where, $field, $key);
    }

    /**
     * 获取商品分类信息
     */
    public function getCategoryInfo($where = [], $field = true)
    {

        return $this->modelCategory->getInfo($where, $field);
    }

    /**
     * 获取商品分类列表
     */
    public function getCategoryList($where = [], $field = true, $order = '', $paginate = 0)
    {
        $where[DATA_STATUS_NAME] = DATA_NORMAL;
        $list                    = $this->modelCategory->getList($where, $field, $order, $paginate);

        return $list;
    }

    /**
     * 生成父子相邻带level的规则列表
     * @param $list
     * @return mixed
     */
    public function makeTreeForHtml($list, $options = [])
    {
        $config = [
            /* 主键 */
            'primary_key' => 'id',
            /* 父键 */
            'parent_key' => 'pid',
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
    public function makeTree($list, $options = [], $closure = false)
    {

        $config = [
            /* 主键 */
            'primary_key' => 'id',
            /* 父键 */
            'parent_key' => 'pid',
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
        $list   = PHPTree::makeTree($list, $config, $closure);
        return $list;
    }


    /**
     * 生成层级父子带level的规则列表
     * @param $list
     * @return mixed
     */
    public function makeTreeForTreeView($data)
    {

        $closure = function(&$item) {
            $level = $item["level"];
            $id    = $item["id"];
            $pid   = $item["pid"];
            if ($level >= config("logic_config")['category_level'])
            {

                $item["after_html"] = "
                                <span class='button_z'>&nbsp;&nbsp;&nbsp;&nbsp;
                                <ob_link><button type='button' class='btn btn btn-info btn-xs' url=".url('categoryEdit')." onclick='edit($id,$pid,$level)'>编辑</button></ob_link>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <ob_link><a class=\"btn btn-danger btn-xs confirm ajax-get\"
                                 href=".url('categoryDel', array('id' => $id)).">删 除</a>
                                 </ob_link>
                                </span>";
            }
            else
            {
                $item["after_html"] = "
                                <span class='button_z'>
                                 <ob_link><button type='button' class='btn btn btn-info btn-xs' url=".url('categoryAdd')." onclick='add($id,$pid,$level);'>添加</button></ob_link>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <ob_link><button type='button' class='btn btn btn-info btn-xs'  url=".url('categoryEdit')." onclick='edit($id,$pid,$level)'>编辑</button></ob_link>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                 <ob_link><a class=\"btn btn-danger btn-xs confirm ajax-get\"
                                 href=".url('categoryDel', array('id' => $id)).">删 除</a>
                                 </ob_link>
                                </span>";
            }
            return $item;
        };
        $res     =empty($data)?[]: $this->makeTree($data, ["children_key" => "nodes"], $closure);
        $list[]  = [
            'text' => "<span style='font-size: 18px;font-weight: bolder;'>分类管理</span>",
            'selectable' => false,
            'nodes' => $res,
            'after_html' => " <span class='button_z'>
                                 <ob_link><button type='button' class='btn btn btn-info btn-xs' url=".url('categoryAdd')." onclick='add(0,-1,-1);'>添加</button></ob_link>
                                </span>",
        ];

        return $list;
    }


    /**
     * 分类转Select
     */
    public function categoryToSelect($category_list = [], $level = 0, $name = 'name', $child = 'child')
    {

        foreach ($category_list as $info)
        {

            $tmp_str = str_repeat("&nbsp;", $level * 4);

            $tmp_str .= "├";

            $info['level'] = $level;

            $info[$name] = empty($level) || empty($info['pid']) ? $info[$name]."&nbsp;": $tmp_str.$info[$name]."&nbsp;";

            if (!array_key_exists($child, $info))
            {

                array_push(self::$categorySelect, $info);
            }
            else
            {

                $tmp_ary = $info[$child];

                unset($info[$child]);

                array_push(self::$categorySelect, $info);

                $this->categoryToSelect($tmp_ary, ++$level, $name, $child);
            }
        }

        return self::$categorySelect;
    }


    /**
     * 获取自己及父分类ids
     * @param int $category_id 分类id
     * @param int $type        返回类型 1:数组 0:字符串
     * @return array|string
     */
    public function getParentCategoryIds($category_id = 0, $type = 1)
    {

        $info = $this->getCategoryInfo(['id' => $category_id]);
        $ids  = $info["path"].",".$category_id;
        return $type ? explode(",", $ids): $ids;
    }


    /**
     * 获取某个商品分类的 儿子 孙子  重子重孙 的 id
     * @param type $cat_id
     */
    function getCatGrandson($cat_id)
    {
        $GLOBALS['catGrandson']     = [];
        $GLOBALS['category_id_arr'] = [];
        // 先把自己的id 保存起来
        $GLOBALS['catGrandson'][] = $cat_id;
        // 把整张表找出来
        $GLOBALS['category_id_arr'] = $this->modelCategory->getColumn([DATA_STATUS_NAME => DATA_NORMAL], "pid", "id");
        // 先把所有儿子找出来
        $son_id_arr = $this->modelCategory->getColumn([DATA_STATUS_NAME => DATA_NORMAL, "pid" => $cat_id], "id");
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
        foreach ($GLOBALS['category_id_arr'] as $k => $v)
        {
            // 找到孙子
            if ($v == $cat_id)
            {
                $this->getCatGrandson2($k); // 继续找孙子
            }
        }
    }


    /**
     * 商品分类信息编辑
     */
    public function categoryEdit($data = [])
    {

        $validate_result = $this->validateCategory->scene(empty($data['id']) ? 'add': 'edit')->check($data);

        if (!$validate_result)
        {

            return [RESULT_ERROR, $this->validateCategory->getError()];
        }

        $url = url('categoryList');
        if (in_array('pid', $data))
        {
            $p_cate   = $this->getCategoryInfo(["id" => $data["pid"]], "path");
            $level    = $p_cate ? count(explode(',', $p_cate["path"])): 0;
            $maxLevel = config("logic_config")['category_level'];
            if ($level > $maxLevel)
            {
                return [RESULT_ERROR, "最多只能添加{$maxLevel}级菜单"];
            };
            $data["path"]  = $p_cate ? $p_cate["path"].",".$data["pid"]: 0;
            $data["level"] = $level;
        }


        $result = $this->modelCategory->setInfo($data);

        $handle_text = empty($data['id']) ? '新增': '编辑';

        $result && action_log($handle_text, '商品分类'.$handle_text.'，name：'.$data['name']);

        return $result ? [RESULT_SUCCESS, '操作成功', $url]: [RESULT_ERROR, $this->modelCategory->getError()];
    }


    /**
     * 商品分类删除
     */
    public function categoryDel($where = [])
    {
        $whereExsit['pid']            = $where['id'];
        $whereExsit[DATA_STATUS_NAME] = DATA_NORMAL;
        $category                     = $this->getCategoryList($whereExsit, true, '', 1);
        if (!$category->isEmpty())
        {
            return [RESULT_ERROR, "请先删除此分类下的子分类"];
        }


        $result = $this->modelCategory->deleteInfo($where);

        $result && action_log('删除', '商品分类删除'.'，where：'.http_build_query($where));

        return $result ? [RESULT_SUCCESS, '删除成功']: [RESULT_ERROR, $this->modelCategory->getError()];
    }


}
