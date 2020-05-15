<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/17
 * Time: 17:32
 */

namespace Composer\Push\BaseServe;


interface CodeConfig
{
    const EMPTY_DATA = '4001'; //模板为空
    const TEMPLATE_DISABLED = '4002'; //模板未启用
    const TEMPLATE_JIGUANG_DISABLED = '4003'; //模板未启用
    const TEMPLATE_WECHAT_DISABLED = '4004'; //模板未启用
    const TEMPLATE_YOUYI_DISABLED = '4005'; //模板未启用
    const TEMPLATE_ALIDAYU_DISABLED = '4006'; //模板未启用
    const NOT_FIND_KEYWORD = '4007'; //存在未传递的keyword
    const REGISTRATION_ID_MISSING = '4008'; //极光id不存在
    const MOBILE_MISSING = '4008'; //手机号不存在
    const WECHAT_OPEN_ID_MISSING = '4009'; //微信OPENID不存在
    const USER_ID_MISSING = '4010'; //USER_ID不存在

    const TEMPLATES_SWITCH = 0b0000000001; // 消息总开关
    const TEMPLATES_WECHAT_SWITCH = 0b0000000010; // 微信
    const TEMPLATES_YOUYI_SWITCH = 0b0000000100; // 有易
    const TEMPLATES_ALIDAYU_SWITCH = 0b0000001000; // 阿里大鱼
    const TEMPLATES_JIGUANG_SWITCH = 0b0000010000; // 极光
    const TEMPLATES_MESSAGE_CONSUMPTION_SWITCH = 0b0000100000; // 站内信 消费
    const TEMPLATES_MESSAGE_CREDIT_SWITCH = 0b0001000000; // 站内信 小优信用
    const TEMPLATES_MESSAGE_IOU_SWITCH = 0b0010000000; // 站内信 白条
    const TEMPLATES_MESSAGE_SERVE_INFORMS_SWITCH = 0b0100000000; // 站内信 服务
    const TEMPLATES_YUNPIAN_SWITCH = 0b1000000000; // 内容短信
}