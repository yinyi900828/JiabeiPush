<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/16
 * Time: 15:25
 */

namespace Composer\Push\Merchant;


use App\Models\TemplateALiDaYu;
use Composer\Push\BaseServe\CodeConfig;
use Composer\Push\BaseServe\SendSmsBase;
use Composer\Push\BaseServe\SendSmsInterface;
use Illuminate\Support\Facades\Cache;

class Alidayu extends SendSmsBase implements SendSmsInterface
{
    public function canPush()
    {
        if (!$this->canPushBefore()) {
            return false;
        }
        $template = $this->templates;
        if (!($template->getStatus() & CodeConfig::TEMPLATES_ALIDAYU_SWITCH)) {
            $this->setErrors(CodeConfig::TEMPLATE_DISABLED, '阿里大于模板未启用');
            return false;
        }
        if (!$this->getMobile()) {
            $this->setErrors(CodeConfig::MOBILE_MISSING, '阿里大于手机号不存在');
            return false;
        }
        return true;
    }

    private function getTemplateALiDaYu()
    {
        $templatesId = $this->templates->getId();
        if ($this->notCache) {
            return TemplateALiDaYu::where('templates_id', $templatesId)->first();
        }
        $templateALiDaYu = Cache::tags(['pushMessage', $this->getKey()])->rememberForever('Template:AliDaYuSmsPush:ID:' . $templatesId, function () use ($templatesId) {
            return TemplateALiDaYu::where('templates_id', $templatesId)->first();
        });
        return $templateALiDaYu;
    }

    public function content()
    {
        $template = $this->getTemplateALiDaYu();
        if ($template) {
            return [
                'user_id' => $this->getUserId(),
                'mobile' => $this->getMobile(),
                'type' => $this->getKey(),
                'template' => $template->template_key,
                'data' => json_encode($this->getParam())
            ];
        }
    }

    public function send()
    {
        dispatch(new AliDaYuSmsPush($this->content()));
        $this->setResult('ALiDaYu', $this->content());
    }
}