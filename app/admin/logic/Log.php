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
 * 行为日志逻辑
 */
class Log extends AdminBase
{
    
    /**
     * 获取日志列表
     */
    public function getLogList()
    {
        
        $sub_admin_ids = $this->logicAdmin->getSubAdminIds(ADMIN_ID);
        
        $where = [];
        
        $sub_admin_ids[] = ADMIN_ID;
        
        !IS_ROOT && $where['admin_id'] = ['in', $sub_admin_ids];
        
        return $this->modelActionLog->getList($where, true, 'create_time desc');
    }
  
    /**
     * 日志删除
     */
    public function logDel($where = [])
    {
        
        return $this->modelActionLog->deleteInfo($where) ? [RESULT_SUCCESS, '日志删除成功'] : [RESULT_ERROR, $this->modelActionLog->getError()];
    }
    
    /**
     * 日志添加
     */
    public function logAdd($name = '', $describe = '')
    {
        
        $admin_info = session('admin_info');
        
        $request = request();
        
        $data['admin_id'] = $admin_info['id'];
        $data['username']  = $admin_info['username'];
        $data['ip']        = $request->ip();
        $data['url']       = $request->url();
        $data['status']    = DATA_NORMAL;
        $data['name']      = $name;
        $data['describe']  = $describe;
        
        $url = url('logList');
        
        return $this->modelActionLog->setInfo($data) ? [RESULT_SUCCESS, '日志添加成功', $url] : [RESULT_ERROR, $this->modelActionLog->getError()];
    }
}
