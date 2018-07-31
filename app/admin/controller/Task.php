<?php
/**
 * Author: Jeary
 * Date: 2018/7/27 11:50
 * Desc: created by PhpStorm
 */

namespace app\admin\controller;

use Crontab\Library\Crontab\AlpacaCrontab;
use Crontab\Library\Crontab\AlpacaDaemon;
use Crontab\Library\Crontab\AlpacaWorker;

/**
 * Class Task 定时任务
 */
class Task extends AdminBase
{
    public function test(){
        $res = AlpacaWorker::worker()->action(['REQUEST_URI' => "/admin/test/test"]);
        return $res;
    }
    /**
     * 查看定时任务守护进程状态
     */
    public function status()
    {
        //查看守护进程状态
        $result['code'] = 1;
        $result['msg']  = '操作成功';
        $result['data'] = AlpacaDaemon::daemon()->status();

        //返回结果
        return json($result);
    }


    /**
     * 查看定时任务列表
     */
    public function taskList()
    {
        //查找
        $data['list']   = AlpacaCrontab::crontab()->listTask();
        $data['total']  = count($data['list']);
        $data['status'] = AlpacaDaemon::daemon()->status();

        //返回结果
        $this->assign('data', $data);
        return $this->fetch('task_list');
    }


    /**
     * 开始定时任务
     */
    public function start()
    {
        //异步开启守护进程
        $result['code'] = 1;
        $result['msg']  = "操作成功";
        $result['data'] = AlpacaWorker::worker()->action(['REQUEST_URI' => "/admin/Crontab/start"]);

        //返回结果
        return json($result);
    }


    /**
     * 停止定时任务守护进程
     */
    public function stop()
    {
        //停止守护进程
        $result['code'] = 1;
        $result['msg']  = "操作成功";
        $result['data'] = AlpacaDaemon::daemon()->stop();

        //返回结果
        return json($result);
    }


    public function taskAdd()
    {
        if (IS_POST)
        {
            return $this->editTask();
        }
        return $this->fetch('edit_modal');
    }

    public function taskEdit()
    {
        if (IS_POST)
        {
            return $this->editTask();
        }
        // 检查参数
        if (empty($this->param['index']))
        {
            $result["code"] = 0;
            $result["msg"]  = "任务不能为空";
            return json($result);
        }

        $this->param['index'] -= 1;
        $data                 = AlpacaCrontab::crontab()->getIndexTask($this->param['index']);

        // 返回结果
        $this->assign('info', $data['result_data']);

        return $this->fetch('edit_modal');
    }

    /**
     * @todo 待处理
     * 添加,或者编辑定时任务
     */
    public function editTask()
    {
        /*
         * 1 获取输入参数
         * begin_time        开始时间
         * end_time          结束时间
         * interval          时间间隔
         * name              名称
         * status            状态 1-ENABLED,   0-DISABLE
         * type              类型 1-ONCE,      2-LOOP
         * action            要执行的Action
         * index             索引，null或者0时候，表示新建
         * */


        //2 检查参数
        if (empty($this->param['begin_time']))
        {
            $result["code"] = 0;
            $result["msg"]  = sprintf("参数不能为空%s", 'begin_time');
            return json($result);
        }
        if ($this->param['type'] == 2 && empty($this->param['end_time']))
        {
            $result["code"] = 0;
            $result["msg"]  = sprintf("参数不能为空%s", 'end_time');
            return json($result);
        }
        if (empty($this->param['action']))
        {
            $result["code"] = 0;
            $result["msg"]  = sprintf("参数不能为空%s", 'action');
            return json($result);
        }
        if ($this->param['type'] == 2 && empty($this->param['interval']))
        {
            $result["code"] = 0;
            $result["msg"]  = sprintf("参数不能为空%s", 'interval');
            return json($result);
        }

        if ($this->param['type'] == 1)
        {
            $this->param['interval'] = null;
        }
        //3 设置结束时间
        $now      = date('Y-m-d H:i:s', time());
        $nextTime = date('Y-m-d H:i:s', strtotime($this->param['interval'], strtotime($this->param['begin_time'])));
        if ($this->param['status'] == "1" || strtotime($now) < strtotime($this->param['begin_time']))
        {
            $nextTime = $this->param['begin_time'];
        }

        //4 创建任务
        $task = array(
            'name' => $this->param['name'],           //name
            'status' => $this->param['status'],         // 1-ENABLED,   0-DISABLE
            'type' => $this->param['type'],          // 1-ONCE,      2-LOOP
            'interval' => $this->param['interval'],       //year（年），month（月），hour（小时）minute（分），second（秒）
            'begin_time' => $this->param['begin_time'],     //开始时间
            'next_time' => $nextTime,                            //下次执行时间
            'last_time' => empty($this->param['last_time']) ? "": $this->param['last_time'],      //上次执行时间
            'action' => $this->param['action'],         //执行的action
            'end_time' => $this->param['end_time'],       //截止时间2
        );

        //5 判断是新建还是修改
        if (empty($this->param['index']))
        {
            //新建
            $info = AlpacaCrontab::crontab()->addTask($task);
        }
        else
        {
            $this->param['index'] -= 1;
            $info                 = AlpacaCrontab::crontab()->editTask($this->param['index'], $task);
        }

        //5 返回结果
        $result['code'] = 1;
        $result['msg']  = "操作成功";
        $result['url']  = url("tasklist");
        $result['data'] = $info;
        return json($result);
    }


    /**
     * 设置定时任务状态
     */
    public function changeTaskStatus()
    {
        //2 检查参数
        if (!array_key_exists('status',$this->param)||$this->param['status']=='')
        {
            $result["code"] = 0;
            $result["msg"]  = sprintf("参数不能为空%s", 'status');
            return json($result);
        }
        if (empty($this->param['index']))
        {
            $result["code"] = 0;
            $result["msg"]  = sprintf("参数不能为空%s", 'index');
            return json($result);
        }

        //3 修改状态
        $this->param['index'] -= 1;
        $data                 = AlpacaCrontab::crontab()->editTaskStatus($this->param['index'], $this->param['status']);

        //4 返回结果
        $result['code'] = 1;
        $result['msg']  = "操作成功";
        $result['data'] = $data;
        return json($result);
    }


    /**
     * 删除定时任务
     */
    public function removeTask()
    {
        //2 检查参数
        if (empty($this->param['index']))
        {
            $result["code"] = 0;
            $result["msg"]  = sprintf("参数不能为空%s", 'index');
            return json($result);
        }

        //3 删除
        $this->param['index'] -= 1;
        $data                 = AlpacaCrontab::crontab()->removeTask($this->param['index']);

        //4 返回结果
        $result['code'] = 1;
        $result['msg']  = "操作成功";
        $result['data'] = $data;
        return json($result);
    }


}