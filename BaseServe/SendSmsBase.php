<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/16
 * Time: 15:53
 */

namespace Composer\Push\BaseServe;


use App\Models\Templates;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendSmsBase
{
    public $pushInfo;

    protected $notCache = true;

    public $errors;

    public $sendResult;

    public function __construct($pushInfo)
    {
        $this->pushInfo = $pushInfo;
        $this->templates = $this->getTemplates();
    }
    public function canPushBefore()
    {
        $template = $this->templates;
        if (!$template) {
            $this->setErrors(CodeConfig::EMPTY_DATA, '不存在此模板');
            return false;
        }
        if (!($template->getStatus() & CodeConfig::TEMPLATES_SWITCH)) {
            $this->setErrors(CodeConfig::TEMPLATE_DISABLED, '模板未启用');
            return false;
        }
        $isExists = $this->keywordsExists($template->getKeywords());
        if (!$isExists) {
            $this->setErrors(CodeConfig::NOT_FIND_KEYWORD, '存在未传递的keyword');
            return false;
        }
        return true;
    }

    public function getTemplates()
    {
        $key = $this->getKey();
        if ($this->notCache) {
            return Templates::where('key', $key)->select('id', 'key', 'status', 'title', 'keywords')->first();
        }
        $templates = Cache::tags(['pushMessage', $key])->rememberForever('Template:Key:' . $key, function () use ($key) {
            return Templates::where('key', $key)->select('id', 'key', 'status', 'title', 'keywords')->first();
        });
        return $templates;
    }


    private function keywordsExists($keywords)
    {
        if (!$keywords) {
            return true;
        }
        $keywordsArray = explode(',', $keywords);
        for ($i = 0; $i < count($keywordsArray); $i++) {
            $words = ltrim($keywordsArray[$i], '$');
            if (!array_key_exists($words, $this->pushInfo->getParam())) {
                return false;
            }
        }
        return true;
    }

    protected function replaceContent($content)
    {
        $param = $this->getParam();
        $keysArray = array_keys($param);
        $old = [];
        $new = [];
        for ($i = 0; $i < count($keysArray); $i++) {
            $old[$i] = '{$' . $keysArray[$i] . '}';
            $new[$i] = $param[$keysArray[$i]];
        }
        $old[] = '{$now}';
        $new[] = date('Y-m-d H:i:s');
        $content = str_replace($old, $new, $content);
        return $content;
    }

    public function setErrors($code, $message)
    {
        $this->errors[] = [$code, $message];
        $this->wirteErrorLog();
    }

    public function setResult($code, $message)
    {
        $this->sendResult[] = [$code, $message];
        $this->wirteErrorLog();
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getResult()
    {
        return $this->sendResult;
    }

    public function getUserId()
    {
        return $this->pushInfo->getTo()['user_id'] ?? null;
    }

    public function getMobile()
    {
        return $this->pushInfo->getTo()['mobile'] ?? null;
    }

    public function getRegistrationId()
    {
        return $this->pushInfo->getTo()['registration_id'] ?? null;
    }

    public function getWeChatOpenId()
    {
        return $this->pushInfo->getTo()['wechat_open_id'] ?? null;
    }

    public function getKey()
    {
        return $this->pushInfo->getKey();
    }

    public function getParam()
    {
        return $this->pushInfo->getParam();
    }

    public function getUrl()
    {
        return $this->pushInfo->getUrl();
    }

    public function getTemplate()
    {
        return $this->templates;
    }

    public function wirteErrorLog()
    {
        if (config('push.push_debug')) {
            Log::driver('push')->notice('key');
            Log::driver('push')->notice($this->getKey());
            Log::driver('push')->notice('param');
            Log::driver('push')->notice($this->getParam());
            Log::driver('push')->notice('to');
            Log::driver('push')->notice($this->pushInfo->getTo());
            Log::driver('push')->notice('result');
            Log::driver('push')->notice($this->getResult());
            Log::driver('push')->notice('error');
            Log::driver('push')->notice($this->getErrors());
        }

    }

}