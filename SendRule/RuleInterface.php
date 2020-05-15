<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/19
 * Time: 17:13
 */

namespace Composer\Push\SendRule;


interface  RuleInterface
{
    public function check($pushInfo);
}

