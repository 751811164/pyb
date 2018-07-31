<?php
/**
 * Author: Jeary
 * Date: 2018/7/27 14:53
 * Desc: created by PhpStorm
 */

namespace app\admin\controller;
use Crontab\Library\Crontab\AlpacaCrontab;
use Crontab\Library\Crontab\AlpacaDaemon;
use Crontab\Library\Crontab\AlpacaWorker;

class Crontab
{


    /**
     * 开始定时任务的守护进程
     */
    public function start()
    {
        //开始守护进程
        $result['code'] = 1;
        $result['msg']  = 'success';


        ignore_user_abort(true);     // 忽略客户端断开
        set_time_limit(0);           // 设置执行不超时

        //在守护进程中注入定时任务
        $events = ['0'=>function(){
            AlpacaWorker::worker()->action(['REQUEST_URI'=>"/admin/Crontab/task"]);
        }];
        AlpacaDaemon::daemon()->setEvents($events);
        AlpacaDaemon::daemon()->start();

        //返回结果
        return json($result);
    }

    /**
     * 停止定时任务的守护进程
     */
    public function stop()
    {
        //停止守护进程
        $result['code'] = 1;
        $result['msg']  = 'success';
        $result['data'] = AlpacaDaemon::daemon()->stop();

        //返回结果
        return json($result);
    }

    /**
     * 执行定时任务
     */
    public function task()
    {
        //执行定时任务
        $result['code'] = 1;
        $result['msg']  = 'success';
        $result['data'] = AlpacaCrontab::crontab()->doTask();

        //返回结果
        return json($result);
    }
}
