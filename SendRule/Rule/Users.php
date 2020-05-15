<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/19
 * Time: 17:13
 */

namespace Composer\Push\SendRule\Rule;


use Composer\Push\SendRule\RuleInterface;

class Users implements RuleInterface
{
    public function check($pushInfo)
    {
        return true;
    }
}