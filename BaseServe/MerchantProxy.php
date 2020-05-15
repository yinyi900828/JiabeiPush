<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/16
 * Time: 15:37
 */

namespace Composer\Push\BaseServe;


use Composer\Push\Merchant\Alidayu;
use Composer\Push\Merchant\JiGuang;
use Composer\Push\Merchant\MessageConsumption;
use Composer\Push\Merchant\MessageCredit;
use Composer\Push\Merchant\MessageIou;
use Composer\Push\Merchant\MessageServeInforms;
use Composer\Push\Merchant\Wechat;
use Composer\Push\Merchant\Youyi;
use Composer\Push\Merchant\YunPian;

class MerchantProxy
{
    public $pushInfo;

    public function __construct($pushInfo)
    {
        $this->pushInfo = $pushInfo;
    }

    public function sendToMerchant()
    {
        foreach ($this->getMerchant() as $merchant) {
            if (!$merchant->canPush()) {
                continue;
            }
            $merchant->send();
        }
        return true;
    }

    private function getMerchant()
    {
        return [
            new Alidayu($this->pushInfo),
            new YunPian($this->pushInfo),
            new Youyi($this->pushInfo),
            new JiGuang($this->pushInfo),
            new Wechat($this->pushInfo),
            new MessageConsumption($this->pushInfo),
            new MessageCredit($this->pushInfo),
            new MessageIou($this->pushInfo),
            new MessageServeInforms($this->pushInfo),
        ];
    }
}