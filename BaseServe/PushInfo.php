<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/16
 * Time: 15:54
 */

namespace Composer\Push\BaseServe;


class PushInfo
{
    public $key;
    public $to;
    public $param;
    public $url;

    public function setKey($messageTempKey)
    {
        $this->key = $messageTempKey;
        return $this;
    }

    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    public function setParam($param)
    {
        $this->param = $param;
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getParam()
    {
        return $this->param;
    }

    public function getUrl()
    {
        return $this->url;
    }

}