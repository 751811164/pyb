<?php

namespace Crontab\Library\Crontab;
/**
 * 定时任务
 * @author Chengcheng
 * @date 2016年10月21日 17:04:44
 */
class AlpacaCrontab
{
    //定时任务文件
    private $task_json = __DIR__.'/crontab.json';

    //单例
    private static $instance;

    //单例
    public static function crontab()
    {
        return self::getInstance();
    }

    //单例
    private static function getInstance()
    {
        if(!self::$instance){
            self::$instance = new self();
            //self::$instance->task_json = base_path('storage') . '/crontab/crontab.json';
            self::$instance->task_json = __DIR__ .'/crontab.json';;
        }
        return self::$instance;
    }

    /**
     * 配置

     * @param array $crontab

     * @return array
     */
    public function setConfig($crontab)
    {
        $this->task_json = $crontab;
        return $this;
    }

    /**
     * 查看定时任务


     * @return array
     */
    public function listTask()
    {
        $tasks = json_decode(file_get_contents($this->task_json),true);
        if(empty($tasks)){
            return [];
        }
        $i = 0;
        foreach ($tasks as $task)
        {
            $tasks[$i]['interval'] = $this->timeToStr($tasks[$i]['interval']);
            $i++;
        }
        return $tasks;
    }

    /**
     * 添加定时任务


     * @return array
     */
    public function addTask($task)
    {
        $result["result_code"] = "1";
        $result["result_message"] = "添加成功";
        $tasks = json_decode(file_get_contents($this->task_json),true);
        $tasks[count($tasks)] = $task;
        file_put_contents($this->task_json, json_encode($tasks), LOCK_EX);
        return $result;
    }

    /**
     * 编辑定时任务

     * @param string $index
     * @param string $task

     * @return array
     */
    public function editTask($index,$task)
    {
        $result["result_code"] = "1";
        $result["result_message"] = "修改成功";
        $tasks = json_decode(file_get_contents($this->task_json),true);
        $tasks[$index] = $task;
        file_put_contents($this->task_json, json_encode($tasks), LOCK_EX);
        return $result;
    }

    /**
     * 编辑定时任务状态

     * @param string $index
     * @param string $status

     * @return array
     */
    public function editTaskStatus($index,$status)
    {
        $result_data["result_code"] = "1";
        $result_data["result_message"] = "修改状态成功[".$status."]";
        $tasks = json_decode(file_get_contents($this->task_json),true);
        $tasks[$index]['status'] = $status;
        file_put_contents($this->task_json, json_encode($tasks), LOCK_EX);
        return $result_data;
    }

    /**
     * 获取定时任务

     * @param string $index

     * @return array
     */
    public function getIndexTask($index)
    {
        $result_data["result_code"] = "1";
        $result_data["result_message"] = "获取任务成功【".$index."】";
        $tasks = json_decode(file_get_contents($this->task_json),true);
        $result_data["result_data"] = $tasks[$index];
        return $result_data;
    }

    /**
     * 删除定时任务

     * @param string $index

     * @return array
     */
    public function removeTask($index)
    {
        $result_data["result_code"] = "1";
        $result_data["result_message"] = "删除任务【".$index."】成功";
        $tasks = json_decode(file_get_contents($this->task_json),true);
        array_splice($tasks, $index, 1);
        file_put_contents($this->task_json, json_encode($tasks), LOCK_EX);
        return $result_data;
    }

    /**
     * 执行定时任务


     * @return array
     */
    public function doTask()
    {
        $tasks = json_decode(file_get_contents($this->task_json) ,true);
        if(empty($tasks)){ return ;}
        $now = date('Y-m-d H:i:s',time());
        foreach ($tasks as &$task){
            if(empty($task['status']) || empty($task['type'])  || empty($task['begin_time']) || empty($task['action']) )
            {
                continue;
            }

            if($task['status'] != 1)
            {
                continue;
            }

            if(!empty($task['end_time']) && strtotime($now)>=strtotime($task['end_time'])){
                $task['next_time']='end';
                continue;
            }

            if($task['type'] == 1 && empty($task['next_time']) )
            {
                continue;
            }

            if($task['type'] == 2 && empty($task['interval']) )
            {
                continue;
            }

            if(!empty($task['next_time']) && $task['next_time']=='end' )
            {
                continue;
            }

            if($task['type'] == 1 && (strtotime($now)>=strtotime($task['next_time'])))
            {
                $task['last_time']= $now;
                $task['next_time']='end';
                $task['status']=0;
                AlpacaWorker::worker()->action(['REQUEST_URI'=>"{$task['action']}"]);
                continue;
            }

            if($task['type'] == 2)
            {
                if(empty($task['next_time'])){
                    $task['next_time'] = $task['begin_time'];
                }

                if(strtotime($now)>=strtotime($task['next_time'])){
                    $task['last_time']= $now;
                    $task['next_time']= date('Y-m-d H:i:s',strtotime($task['interval']));
                    AlpacaWorker::worker()->action(['REQUEST_URI'=>"{$task['action']}"]);
                }
                continue;
            }
        }

        file_put_contents($this->task_json, json_encode($tasks), LOCK_EX);
        return $tasks;
    }

    /**
     * 格式化时间

     * @param string $interval

     * @return array
     */
    private function timeToStr($interval)
    {
        $result = "";
        if($interval != null && $interval != ""){
            $temp = explode(" ", $interval);
            $iNumTemp = $temp[0];
            $iType = $temp[1];
            $iNum = str_replace("+", "", $iNumTemp);
            $str = "";
            switch ($iType){
                case "year":
                    $str = "（年）";
                    break;
                case "month":
                    $str = "（月）";
                    break;
                case "day":
                    $str = "（日）";
                    break;
                case "hour":
                    $str = "（小时）";
                    break;
                case "minute":
                    $str = "（分）";
                    break;
                case "second":
                    $str = "（秒）";
                    break;
                default:
                    break;
            }
           $result = $iNum. $str;
        }
        return $result;
    }
}