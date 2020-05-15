<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/19
 * Time: 17:13
 */

namespace Composer\Push\SendRule;


use Composer\Push\SendRule\Rule\Users;

class RuleProxy
{
    public function ruleClass()
    {
        return [
            new Users,
        ];
    }

    public function Verification($pushInfo)
    {
        foreach ($this->ruleClass() as $rule) {
            if (!$rule->check($pushInfo)) {
                return false;
            }
        }
        return true;
    }
}