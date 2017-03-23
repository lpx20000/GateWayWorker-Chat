<?php
/**
 * Created by PhpStorm.
 * User: 雨鱼
 * Date: 2017/3/17
 * Time: 10:13
 */

namespace Applications\app;


use Events;
use GatewayWorker\Lib\Gateway;

class Dispatch
{
    public function __construct()
    {
        echo 'sdfs'."\n";
    }
    /**
     * @param $client_id
     * @param $message
     * @return mixed
     * @throws \Exception
     * Create: 雨鱼
     */
    public function dispatchMessage($client_id, $message)
    {
        $message = json_decode($message, true);
        if (method_exists($this, $message['type'].'Dispatch')) {
            return call_user_func_array([$this, $message['type'].'Dispatch'], [$message, $client_id]);
        }
        $err_msg = "the method your call do not exists";
        throw new \Exception($err_msg);
    }
    /**
     * @param $message
     * @param $client_id
     * @return bool
     * Create: 雨鱼
     */
    protected function loginDispatch($message, $client_id)
    {
        $user = $this->checkAuthorized($message);
        $loginMessage = '欢迎您的登录<span style="color: red">&nbsp;&nbsp;' . $user['name'] . '</span>';
        $broadCastMessage = '欢迎<span style="color: red">&nbsp;&nbsp;' . $user['name'] . '加入聊天室</span>';
        $this->bindUser($client_id, $user);
        $this->broadCast($client_id, $user, $broadCastMessage, 'newUser', $this->getAllClientId());
        $this->sendToSingleClient($client_id, $user, $loginMessage, 'success', $this->onLineClient());
    }

    /**
     * @param $message
     * @return array
     * Create: 雨鱼
     */
    protected function checkAuthorized($message):array
    {
        list(,$name, $email) = fixMessage($message);

        $info = Events::$db->select(['id', 'name', 'email', 'image', 'login_at'])->where('email= :email')
            ->bindValues(['email' => $email])->from('users')->query();

        if (empty($info)) {
            $user = [
                'name'=>$name,
                'email'=>$email,
                'image' => '/static/images/avatar/f1/f_'.rand(1,12).'.jpg',
                'login_at' => date('Y-m-d H:i:s', time())
            ];
            $user_id = self::$db->insert('users')->cols($user)->query();
            $user['id'] = $user_id;
        }else{
            $login_at = date('Y-m-d H:i:s', time());
            Events::$db->update('users')->cols(['login_at'=>$login_at])->where($info[0]['id'])->query();
            $user = $info[0];
            $user['login_at'] = $login_at;
        }
        return $user;
    }

    /**
     * @param $client_id
     * @param $user
     * @param $message
     * @param $type
     * @param null $allUser
     * Create: 雨鱼
     */
    protected function sendToSingleClient($client_id, $user, $message, $type, $allUser = null)
    {
        Gateway::sendToClient($client_id, json_encode([
            'type' => $type,
            'data' => [
                'message' => $message,
                'user' => $user,
                'allUser' => $allUser,
            ]]));
    }
    /**
     * send message to all client
     * @param $message
     * @param $client_id
     * Create: 雨鱼
     */
    protected function allDispatch($message, $client_id)
    {
        $talkUser = Gateway::getSession($client_id);
        $talkUser['time'] = date('Y-m-d H:i:s', time());
        $this->broadCast($client_id, $talkUser, $message['message'], 'message', $this->getAllClientId());
        $this->handlMessage($client_id, $message['message']);
    }

    /**
     * 处理发送过来的消息，@人等
     * @param $client_id
     * @param $message
     * Create: 雨鱼
     */
    protected function handlMessage($client_id, $message)
    {
        if (strpos($message, '@') != false) {
            $patten = '/\<.*?\>@(.*?)<.*?>/';
            preg_match_all($patten, $message, $match);
            $match = array_unique($match[1]);
            $users = implode("','", $match);
           $remindUsers = Events::$db->select('id')->from('users')->where("name in ('$users')")->query();
            $clientId = [];
            var_dump($message);
            foreach ($remindUsers as $remindUser) {
                //不在线，暂时不发送
                if (!Gateway::getClientIdByUid($remindUser['id'])) continue;
                $clientId[] = Gateway::getClientIdByUid($remindUser['id'])[0];
            }
            $user = json_decode($_SESSION[$client_id], true);
            $message = $user['name'].'@了你...';
            $this->broadCast($client_id, null, $message, 'remind', $clientId);
        }
    }
    /**
     * @param $client_id
     * @param $user
     * Create: 雨鱼
     */
    protected function bindUser($client_id, $user):void
    {
        Gateway::setSession($client_id, $user);
        Gateway::bindUid($client_id, $user['id']);
        $_SESSION[$client_id] = json_encode($user);
    }

    /**
     * @param $client_id
     * @param $user
     * Create: 雨鱼
     * @param $message
     * @param $type
     * @param null $sendTo
     */
    protected function broadCast($client_id, $user, $message, $type, $sendTo = null)
    {
        Gateway::sendToAll(json_encode([
            'type' => $type,
            'data' => [
                'message' => $message,
                'user' => $user,
            ]]), $sendTo, $client_id);
    }

    /**
     * 获取所有已登录的客户端信息
     * @return mixed Create: 雨鱼
     * Create: 雨鱼
     */
    protected function onLineClient()
    {
        return removeEmptyClientId(Gateway::getAllClientSessions());
    }

    /**
     *
     * @return array
     * Create: 雨鱼
     */
    protected function getAllClientId()
    {
        $onLineClient = $this->onLineClient();
        return array_keys($onLineClient);
    }

    /**
     * @param $message
     * @param $client_id
     * @return bool
     * Create: 雨鱼
     */
    public function checkDispatch($message, $client_id)
    {
        if ($message['message'] == 'checkUserList'){
            echo count($this->getAllClientId());
            if (count($this->getAllClientId()) <= 1)
           $this->sendToSingleClient($client_id, null, '', 'check', $allUser = null);
            return true;
        }
        //私聊记录
    }
}