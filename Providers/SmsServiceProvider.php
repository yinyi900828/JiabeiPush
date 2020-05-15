<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/19
 * Time: 11:40
 */

namespace Composer\Push\Providers;

use Illuminate\Support\ServiceProvider;
use Toplan\PhpSms\Sms;

class SmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (config('push.push_debug')) {
            Sms::beforeAgentSend(function ($task, $driver, $index, $handlers, $prevReturn) {
                $to = $task->data['to'];
                $programmer = config('push.test_mobiles');
                if (in_array($to, $programmer)) {
                    return true;
                }
                Sms::scheme('Log', '100 backup');
                if ($driver->name !== 'Log') {
                    //如果返回false会停止使用当前代理器
                    return false;
                }
            });
        }
    }
}