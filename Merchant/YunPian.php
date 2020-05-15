<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/16
 * Time: 15:26
 */

namespace Composer\Push\Merchant;


use App\Jobs\AliDaYuSmsPush;
use App\Jobs\YunPianPush;
use App\Models\TemplateContentSms;
use Composer\Push\BaseServe\CodeConfig;
use Composer\Push\BaseServe\SendSmsBase;
use Composer\Push\BaseServe\SendSmsInterface;
use Illuminate\Support\Facades\Cache;

class YunPian extends SendSmsBase implements SendSmsInterface
{
    public function canPush()
    {
        if (!$this->canPushBefore()) {
            return false;
        }
        $template = $this->templates;
        if (!($template->getStatus() & CodeConfig::TEMPLATES_YUNPIAN_SWITCH)) {
            $this->setErrors(CodeConfig::TEMPLATE_DISABLED, '云片模板未启用');
            return false;
        }
        if (!$this->getMobile()) {
            $this->setErrors(CodeConfig::MOBILE_MISSING, '云片手机号不存在');
            return false;
        }
        return true;
    }

    private function getTemplateYunPian()
    {
        $templatesId = $this->templates->getId();
        if ($this->notCache) {
            return TemplateContentSms::where('templates_id', $templatesId)->first();
        }
        $templateContent = Cache::tags(['pushMessage', $this->getKey()])->rememberForever('Template:ContentSmsPush:ID:' . $templatesId, function () use ($templatesId) {
            return TemplateContentSms::where('templates_id', $templatesId)->first();
        });
        return $templateContent;
    }

    public function content()
    {
        $templateYunPian = $this->getTemplateYunPian();
        $content = $this->replaceContent($templateYunPian->content);
        return [
            'user_id' => $this->getUserId(),
            'mobile' => $this->getMobile(),
            'type' => $this->getKey(),
            'content' => $content,
        ];
    }

    public function send()
    {
        dispatch(new YunPianPush($this->content()));
        $this->setResult('YunPian', $this->content());
    }
}