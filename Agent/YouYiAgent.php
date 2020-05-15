<?php

namespace Toplan\PhpSms;

class YouYiAgent extends Agent implements ContentSms
{
    protected static $smsUrl = 'http://sms.ue35.net/sms/interface/sendmess.htm';

    public function sendContentSms($to, $content) {

        $result = $this->curlPost(self::$smsUrl, [
            'mobiles' => str_replace(',', ';', $to),
            'content' => $content,
            'username' => config('phpsms.agents.YouYi.username'),
            'userpwd' => config('phpsms.agents.YouYi.userpwd'),
            'mobilecount' => count(explode(',', $to)),
        ]);
        $this->setResult($result);
    }

    protected function setResult($result) {
        if ($result['request']) {
            $dom = simplexml_load_string($result['response']);
            $this->result(Agent::CODE, (int)$dom->errorcode);
            $this->result(Agent::INFO, (string)$dom->message);
            $this->result(Agent::SUCCESS, $dom->errorcode == 1);
        } else {
            $this->result(Agent::INFO, 'request failed');
        }
    }
}