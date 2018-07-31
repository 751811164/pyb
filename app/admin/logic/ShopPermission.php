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

/**
 * 店铺权限
 */
class ShopPermission extends AdminBase
{

    /**
     * //构建店铺树形列表
     * @param array $where
     * @param string $field
     * @param string $order
     * @return mixed
     */
    public function getShopTree($where = [], $field = "*", $order = '',$paginate=false)
    {
        $where_type[DATA_STATUS_NAME] = DATA_NORMAL;
        $where_shop[DATA_STATUS_NAME] = DATA_NORMAL;
        $typeList                     = collection($this->logicShopType->getTypeList($where_type, 'id,name', $order, $paginate))->toArray();
        $shopList = collection($this->logicShop->getList($where_shop, 'id,name,number,type_id', $order, $paginate))->toArray();
        foreach ($typeList as $key => $type)
        {
            $typeList[$key]["children"]=[];
            foreach ($shopList as $k=>$shop){
                if ($shop['type_id']==$type['id']){
                    $typeList[$key]["children"][]= $shop;
                }
            }
        }

        return ['typeList'=>$typeList,'shopList'=>$shopList];
    }

    /**
     * 获取店铺拥有的操作权限信息
     * @param $where
     * @param $field
     * @return mixed
     */
    public function getPermissionInfo($where = [], $field = true)
    {
        return $this->modelShopPermission->getInfo($where, $field);
    }

    public function permissionsEdit($data=[])
    {
        $validate_result = $this->validateShopPermission->scene('add')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validateShopPermission->getError()]; endif;
        $url = url('shoptree');
        $result      = $this->modelShopPermission->setInfo($data);
        $handle_text = empty($data['id']) ? '新增': '编辑';

        $result && action_log($handle_text, '店铺权限信息'.$handle_text.'，name：权限');

        return $result!==false ? [RESULT_SUCCESS, '操作成功']: [RESULT_ERROR, $this->modelShopPermission->getError()];
    }


    /**
     * 快速复制权限
     * @param array $data
     * @return array
     */
    public function fastSet($data=[]){

        $validate_result   = $this->validateShopPermission->scene('fastSet')->check($data);
        if (!$validate_result) : return [RESULT_ERROR, $this->validateShopPermission->getError()]; endif;
        $url = url('shoptree');
        $where=[];
        //参考门店的权限
        $referPermission = $this->modelShopPermission->getInfo(['shop_id'=>$data['refer_id']])->toArray();
        unset($referPermission["id"]);
        unset($referPermission["shop_id"]);
        unset($referPermission["create_time"]);
        unset($referPermission["update_time"]);

        //TODO 后期优化处理
        foreach ( $data["shop_ids"] as $shop_id){
            //待设置门店权限
            $permission = $this->modelShopPermission->getInfo(['shop_id' => $shop_id]);
            if (empty($permission))
            {
                $referPermission["shop_id"] = $shop_id;
            }
            else
            {
                $where['shop_id'] = $shop_id;
            }

            $result = $this->modelShopPermission->setInfo($referPermission, $where);
        }

        return $result ? [RESULT_SUCCESS, '操作成功', $url, '']: [RESULT_ERROR, $this->modelShopPermission->getError()];
    }


}
