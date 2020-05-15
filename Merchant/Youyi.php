<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/16
 * Time: 15:26
 */

namespace Composer\Push\Merchant;


use App\Jobs\YouYiSmsPush;
use App\Models\TemplateYouYi;
use Composer\Push\BaseServe\CodeConfig;
use Composer\Push\BaseServe\SendSmsBase;
use Composer\Push\BaseServe\SendSmsInterface;
use Illuminate\Support\Facades\Cache;

class Youyi extends SendSmsBase implements SendSmsInterface
{
    public function canPush()
    {
        if (!$this->canPushBefore()) {
            return false;
        }
        $template = $this->templates;
        if (!($template->getStatus() & CodeConfig::TEMPLATES_YOUYI_SWITCH)) {
            $this->setErrors(CodeConfig::TEMPLATE_DISABLED, '有易模板未启用');
            return false;
        }
        if (!$this->getMobile()) {
            $this->setErrors(CodeConfig::MOBILE_MISSING, '有易手机号不存在');
            return false;
        }
        return true;
    }

    private function getTemplateYouYi()
    {
        $templatesId = $this->templates->getId();
        if ($this->notCache) {
            return TemplateYouYi::where('templates_id', $templatesId)->first();
        }
        $templateYouYi = Cache::tags(['pushMessage', $this->getKey()])->rememberForever('Template:SmsPush:ID:' . $templatesId, function () use ($templatesId) {
            return TemplateYouYi::where('templates_id', $templatesId)->first();
        });
        return $templateYouYi;
    }

    public function content()
    {
        $templateYouYi = $this->getTemplateYouYi();
        if ($templateYouYi) {
            $content = $this->replaceContent($templateYouYi->content);
            return [
                'user_id' => $this->getUserId(),
                'mobile' => $this->getMobile(),
                'type' => $this->getKey(),
                'content' => $content,
            ];
        }
    }

    public function send()
    {
        dispatch(new YouYiSmsPush($this->content()));
        $this->setResult('YouYi', $this->content());
    }
}