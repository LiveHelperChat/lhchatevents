<?php

class erLhcoreClassExtensionLhchatevents
{
    public function __construct()
    {
        
    }

    private $settings = array();

    public function run()
    {
        $this->settings = include ('extension/lhchatevents/settings/settings.ini.php');
            
        $dispatcher = erLhcoreClassChatEventDispatcher::getInstance();
        
        $dispatcher->listen('chat.close', array(
            $this,
            'chatClose'
        ));       
    }

    public function executeRequest($postFields) {
        $postFields = array_filter($postFields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->settings['host']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_REFERER, "");
        curl_setopt($ch, CURLOPT_POST , 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS , $postFields);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 10);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $content = curl_exec($ch);
       
        return $content;
    }

    /*
     * array('chat' => & $chat, 'user_data' => $operator)
     * */
    public function chatClose($params)
    {
        $chat = $params['chat'];

        if ($chat->additional_data != '') {
           $this->executeRequest(array(
                'additional_data' => $chat->additional_data,
                'chat_id' => $chat->id
            ));
        }
    }
}
