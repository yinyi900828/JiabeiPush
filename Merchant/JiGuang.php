<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/16
 * Time: 15:25
 */

namespace Composer\Push\Merchant;


use App\Jobs\JPush;
use App\Models\TemplateJiGuang;
use Composer\Push\BaseServe\CodeConfig;
use Composer\Push\BaseServe\SendSmsBase;
use Composer\Push\BaseServe\SendSmsInterface;
use Illuminate\Support\Facades\Cache;

class JiGuang extends SendSmsBase implements SendSmsInterface
{
    public function canPush()
    {
        if (!$this->canPushBefore()) {
            return false;
        }
        $template = $this->templates;
        if (!($template->getStatus() & CodeConfig::TEMPLATES_JIGUANG_SWITCH)) {
            $this->setErrors(CodeConfig::TEMPLATE_DISABLED, '极光模板未启用');
            return false;
        }
        if (!$this->getRegistrationId()) {
            $this->setErrors(CodeConfig::REGISTRATION_ID_MISSING, '极光id不存在');
            return false;
        }
        return true;
    }

    private function getTemplateJiGuang()
    {
        $templatesId = $this->templates->getId();
        if ($this->notCache) {
            return TemplateJiGuang::where('templates_id', $templatesId)->first();
        }
        $templateJiGuang = Cache::tags(['pushMessage', $this->getKey()])->rememberForever('Template:JPush:ID:' . $templatesId, function () use ($templatesId) {
            return TemplateJiGuang::where('templates_id', $templatesId)->first();
        });
        return $templateJiGuang;
    }

    public function content()
    {
        $templateJiGuang = $this->getTemplateJiGuang();
        if ($templateJiGuang) {
            $content = $this->replaceContent($templateJiGuang->content);
            return [
                'type' => $this->getKey(),
                'content' => $content,
                'url' => $this->getUrl() ?: $templateJiGuang->url ?: '',
                'registration_id' => $this->getRegistrationId(),
                'user_id' => $this->getUserId(),
                'push_type' => $templateJiGuang->push_type,
            ];
        }
    }

    public function send()
    {
        dispatch(new JPush($this->content()));
        $this->setResult('JiGuang', $this->content());
    }
}