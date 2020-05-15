<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/16
 * Time: 15:34
 */

namespace Composer\Push\BaseServe;


interface SendSmsInterface
{
    public function canPush();

    public function content();

    public function send();

}