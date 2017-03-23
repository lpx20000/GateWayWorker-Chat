<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);


use Applications\app\Dispatch;
use \GatewayWorker\Lib\Gateway;
use Workerman\MySQL\Connection;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    /**
     * db instance
     * @var null
     */
    public static $db = null;
    public static $dispatch;
    /**
     * @param $worker
     * Create: 雨鱼
     */
    public static function onWorkerStart($worker)
    {
        self::$dispatch = new Dispatch();
        self::$db = new Connection('127.0.0.1', '3306', 'homestead', 'secret', 'swooleRoom');
    }
    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id) {
        echo 'new Connect'."\n";
        // 向当前client_id发送数据
        $data = array(
            'task' => 'open',
            'fd' => $client_id
        );
//        Gateway::sendToClient($client_id, json_encode($data) );
        // 向所有人发送
        Gateway::sendToAll(json_encode($data));
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message) {
        // 消息分发处理
       self::$dispatch->dispatchMessage($client_id, $message);
   }
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id) {
       Gateway::closeClient($client_id);
        $user = json_decode($_SESSION[$client_id], true);
        $_SESSION[$client_id] = null;
       // 向所有人发送 
       GateWay::sendToAll(json_encode([
           'type'=>'logout',
           'data' => [
               'message' => '<span style="color: red">&nbsp;&nbsp;'.$user['name'].'退出聊天室</span>',
               'user'=>$user,
               'time' =>$time = date('H:i:s', time())
           ]]));
   }

    public static function onWorkerStop()
    {
//
   }
}
