<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/19
 * Time: 15:16
 */

namespace Composer\Push\Merchant;


use App\Models\TemplateMessageCredit;
use Composer\Push\BaseServe\CodeConfig;
use Composer\Push\BaseServe\SendSmsBase;
use Composer\Push\BaseServe\SendSmsInterface;
use Illuminate\Support\Facades\Cache;
use App\Models\MessageCredit as MessageCreditModel;

class MessageCredit extends SendSmsBase implements SendSmsInterface
{

    public function canPush()
    {

        if (!$this->canPushBefore()) {
            return false;
        }
        $template = $this->templates;
        if (!($template->getStatus() & CodeConfig::TEMPLATES_MESSAGE_CREDIT_SWITCH)) {
            $this->setErrors(CodeConfig::TEMPLATE_DISABLED, '站内信-信用率模板未启用');
            return false;
        }
        if (!$this->getUserId()) {
            $this->setErrors(CodeConfig::WECHAT_OPEN_ID_MISSING, '站内信-信用率USER_ID不存在');
            return false;
        }
        return true;
    }

    private function getTemplateCredit()
    {
        $templatesId = $this->templates->getId();
        if ($this->notCache) {
            return TemplateMessageCredit::where('templates_id', $templatesId)->first();
        }
        $template = Cache::tags(['pushMessage', $this->getKey()])->rememberForever('Template:MessageCredit:ID:' . $templatesId, function () use ($templatesId) {
            return TemplateMessageCredit::where('templates_id', $templatesId)->first();
        });
        return $template;
    }

    public function content()
    {
        return true;
    }

    public function send()
    {
        $template = $this->getTemplateCredit();
        if ($template) {
            $content = $this->replaceContent($template->content);
            $model = new MessageCreditModel();
            $model->user_id = $this->getUserId();
            $model->title = $template->title;
            $model->content = $content;
            $model->type = $template->type;
            $model->url = $this->getUrl() ?: $template->url ?: '';
            $send = $model->save();
            $this->setResult('MessageCredit', $send);
        }
    }
}