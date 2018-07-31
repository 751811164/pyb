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

use app\common\logic\LogicBase;

/**
 * Admin基础逻辑
 */
class AdminBase extends LogicBase
{

    /**
     * 权限检测
     */
    public function authCheck($url = '', $url_list = [])
    {

        $pass_data = [RESULT_SUCCESS, '权限检查通过'];

        $allow_url = config('allow_url');

        $allow_url_list = parse_config_attr($allow_url);

        if (IS_ROOT)
        {

            return $pass_data;
        }

        $s_url = strtolower($url);

        if (!empty($allow_url_list))
        {

            foreach ($allow_url_list as $v)
            {

                if (strpos($s_url, strtolower($v)) !== false)
                {

                    return $pass_data;
                }
            }
        }
        $result = in_array($s_url, array_map("strtolower", $url_list)) ? true: false;
        //ajax请求时忽略menu里面不存在的url
        if (!$result && IS_AJAX && !IS_PJAX)
        {
            $module = request()->module();
            $menu   = $this->logicMenu->getMenuInfo(["url" => str_replace($module."/", '', $s_url)]);
            $result = empty($menu) ?: $result;

        }


        !('admin/index/index' == $s_url && !$result) ?: clear_login_session();

        return $result ? $pass_data: [RESULT_ERROR, '未授权操作'];
    }

    /**
     * 获取过滤后的菜单树
     */
    public function getMenuTree($menu_list = [], $url_list = [])
    {

        foreach ($menu_list as $key => $menu_info)
        {

            list($status, $message) = $this->authCheck(strtolower(MODULE_NAME.SYS_DS_PROS.$menu_info['url']), $url_list);

            [$message];

            if ((!IS_ROOT && RESULT_ERROR == $status) || !empty($menu_info['is_hide']))
            {

                unset($menu_list[$key]);
            }
        }

        return $this->getListTree($menu_list);
    }

    /**
     * 获取列表树结构
     */
    public function getListTree($list = [])
    {

        if (is_object($list))
        {

            $list = $list->toArray();
        }

        return list_to_tree(array_values($list), 'id', 'pid', 'child');
    }

    /**
     * 通过完整URL获取检查标准URL
     */
    public function getCheckUrl($full_url = '')
    {

        $temp_url = sr($full_url, URL_ROOT);

        $url_array_tmp = explode(SYS_DS_PROS, $temp_url);

        if (strpos($url_array_tmp[3], '.'))
        {

            $action_arr = explode('.', $url_array_tmp[3]);

            $url_array_tmp[3] = $action_arr[0];
        }

        return $url_array_tmp[1].SYS_DS_PROS.$url_array_tmp[2].SYS_DS_PROS.$url_array_tmp[3];
    }

    /**
     * 过滤页面内容
     */
    public function filter($content = '', $url_list = [])
    {
        $results = [];

        preg_match_all('/<ob_link>.*?<\/ob_link>/', $content, $results);

        foreach ($results[0] as $a)
        {

            $match_results = [];

            preg_match_all('/href="(.+?)"|data-url="(.+?)"|url="(.+?)"/', $a, $match_results);

            $full_url = '';

            if (empty($match_results[1][0]) && empty($match_results[2][0]))
            {

                continue;
            }
            elseif (!empty($match_results[1][0]))
            {

                $full_url = $match_results[1][0];
            }
            else
            {

                $full_url = $match_results[2][0];
            }

            if (!empty($full_url))
            {

                $result = $this->authCheck($this->getCheckUrl($full_url), $url_list);

                $result[0] != RESULT_SUCCESS && $content = sr($content, $a);
            }
        }

        $content = $this->filterPrice($content);

        return $content;
    }

    /**
     * 价格列表过滤
     */
    public function filterPrice($content = '')
    {

        $results = [];
        preg_match_all('/<ob_price.*?>(.*?)<\/ob_price>/s', $content, $results);

        if (ADMIN_ID != IS_ROOT)
        {
            $permission = $this->modelAdminPermission->getInfo(["admin_id" => ADMIN_ID], "price");
            //$permission = session("admin_info");
            $priceInfo = empty($permission) ? []: $permission["price"];

            foreach ($results[0] as $key => $item)
            {
                preg_match_all('/rule="(.+?)"/', $item, $match_results);
                $price = "";

                if (empty($match_results[1][0]))
                {
                    $content = sr($content, $item, $results[1][$key]);
                    continue;
                }
                else
                {
                    $price = $match_results[1][0];
                }
                if (!empty($price))
                {
                    $result = in_array($price, $priceInfo);
                    $result != RESULT_SUCCESS && $content = sr($content, $item);
                }
                $content = sr($content, $item, $results[1][$key]);
            }
        }
        else
        {
            foreach ($results[0] as $key => $item)
            {
                preg_match_all('/rule="(.+?)"/', $item, $match_results);
                $content = sr($content, $item, $results[1][$key]);
            }
        }

        return $content;
    }


    /**
     * 获取首页数据
     */
    public function getIndexData()
    {

        $query = new \think\db\Query();

        $system_info_mysql = $query->query("select version() as v;");

        // 系统信息
        $data['ob_version']    = SYS_VERSION;
        $data['think_version'] = THINK_VERSION;
        $data['os']            = PHP_OS;
        $data['software']      = $_SERVER['SERVER_SOFTWARE'];
        $data['mysql_version'] = $system_info_mysql[0]['v'];
        $data['upload_max']    = ini_get('upload_max_filesize');
        $data['php_version']   = PHP_VERSION;

        // 产品信息
        $data['product_name'] = 'OneBase开源免费基础架构';
        $data['author']       = 'Bigotry';
        $data['website']      = 'www.onebase.org';
        $data['qun']          = '<a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=3807aa892b97015a8e016778909dee8f23bbd54a4305d827482eda88fcc55b5e"><img border="0" src="//pub.idqqimg.com/wpa/images/group.png" alt="OneBase ①" title="OneBase ①"></a>';
        $data['document']     = '<a target="_blank" href="http://document.onebase.org">http://document.onebase.org</a>';

        return $data;
    }

    /**
     * 数据状态设置(onebase)
     * @deprecated 使用新的方法setStatus
     */
    public function setStatus_origin($model = null, $param = null)
    {

        if (empty($model) || empty($param))
        {

            return [RESULT_ERROR, '非法操作'];
        }

        $status = (int)$param[DATA_STATUS_NAME];

        $model_str = LAYER_MODEL_NAME.$model;

        $obj = $this->$model_str;

        is_array($param['ids']) ? $ids = array_extract((array)$param['ids'], 'value'): $ids[] = (int)$param['ids'];

        $result = $obj->setFieldValue(['id' => ['in', $ids]], DATA_STATUS_NAME, $status);

        $result && action_log('数据状态', '数据状态调整'.'，model：'.$model.'，ids：'.arr2str($ids).'，status：'.$status);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $obj->getError()];
    }


    /**
     * 数据状态设置
     */
    public function setStatus($model = null, $param = null, $field = DATA_STATUS_NAME, $msg = '数据状态调整')
    {

        if (empty($model) || empty($param))
        {

            return [RESULT_ERROR, '非法操作'];
        }

        $status = (int)$param[$field];

        $model_str = LAYER_MODEL_NAME.$model;

        $obj = $this->$model_str;

        is_array($param['ids']) ? $ids = array_extract((array)$param['ids'], 'value'): $ids[] = (int)$param['ids'];

        if (array_key_exists('is_del', $param) && $param['is_del'])
        {
            $result = $obj->deleteInfo(['id' => ['in', $ids]], true);
        }
        else
        {
            $result = $obj->setFieldValue(['id' => ['in', $ids]], $field, $status);
        }

        $result && action_log('数据状态', $msg.'，model：'.$model.'，ids：'.arr2str($ids).'，status：'.$status);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $obj->getError()];
    }


    /**
     * 数据排序设置
     */
    public function setSort($model = null, $param = null)
    {

        $model_str = LAYER_MODEL_NAME.$model;

        $obj = $this->$model_str;

        $result = $obj->setFieldValue(['id' => (int)$param['id']], 'sort', (int)$param['value']);

        $result && action_log('数据排序', '数据排序调整'.'，model：'.$model.'，id：'.$param['id'].'，value：'.$param['value']);

        return $result ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $obj->getError()];
    }

    /**
     *生成最大id+1的编号
     * @param     $data
     * @param int $len 编号长度
     * @return string 编号
     */
    public function createCode($data = [], $len = 6)
    {
        $model_str = LAYER_MODEL_NAME.$this->name;
        $obj       = $this->$model_str;
        $table     = config('database.prefix').humpToLine(lcfirst($this->name));

        $res   = $obj->query("show table status where `name`='".$table."'");
        $maxId = (!empty($res)) ? $res[0]['Auto_increment']: 1;
        $code  = sprintf("1%0{$len}s", $maxId);
        //        $maxId++;
        //        $obj->execute("alter table {$table} AUTO_INCREMENT={$maxId};");
        return $code;

    }

    public function authShopPermission($admin_id=ADMIN_ID,$field="sp.*,s.type_id,g.id as goods_id")
    {
        //人员所在店铺
        $join = [
            [SYS_DB_PREFIX.'auth_group_access ga', 'ga.group_id = g.id'],
            [SYS_DB_PREFIX.'shop s', 'g.shop_id = s.id'],
            [SYS_DB_PREFIX.'shop_permission sp', 'g.shop_id = sp.shop_id','LEFT'],
        ];
        $res  = $this->logicAuthGroup->getGroupInfo(["ga.admin_id" => $admin_id], $field, $join);
        return $res;


    }

}
