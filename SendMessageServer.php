<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/16
 * Time: 15:23
 */

namespace App\Repositories\SendMessage;


use Composer\Push\BaseServe\MerchantProxy;
use Composer\Push\BaseServe\PushInfo;
use Composer\Push\SendRule\RuleProxy;

class SendMessageServer
{
    public $pushInfo;

    public function __construct(string $key, array $to, array $param, string $url = null)
    {
        $this->pushInfo = new PushInfo;
        $this->pushInfo->setKey($key)->setTo($to)->setParam($param)->setUrl($url);
    }

    public function sendMessage()
    {
        if (!(new RuleProxy)->Verification($this->pushInfo)) {
            return false;
        }
        $result = (new MerchantProxy($this->pushInfo))->sendToMerchant();
        return $result;
    }


}