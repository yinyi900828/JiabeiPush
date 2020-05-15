<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/19
 * Time: 15:20
 */

namespace Composer\Push\Merchant;


use App\Models\TemplateMessageIou;
use Composer\Push\BaseServe\CodeConfig;
use Composer\Push\BaseServe\SendSmsBase;
use Composer\Push\BaseServe\SendSmsInterface;
use Illuminate\Support\Facades\Cache;
use App\Models\MessageIou as MessageIouModel;

class MessageIou extends SendSmsBase implements SendSmsInterface
{

    public function canPush()
    {
        if (!$this->canPushBefore()) {
            return false;
        }
        $template = $this->templates;
        if (!($template->getStatus() & CodeConfig::TEMPLATES_MESSAGE_IOU_SWITCH)) {
            $this->setErrors(CodeConfig::TEMPLATE_DISABLED, '站内信-白条模板未启用');
            return false;
        }
        if (!$this->getUserId()) {
            $this->setErrors(CodeConfig::WECHAT_OPEN_ID_MISSING, '站内信-白条模板USER_ID不存在');
            return false;
        }
        return true;
    }

    public function content()
    {
        return true;
    }

    private function getTemplateIou()
    {
        $templatesId = $this->templates->getId();
        if ($this->notCache) {
            return TemplateMessageIou::where('templates_id', $templatesId)->first();
        }
        $template = Cache::tags(['pushMessage', $this->getKey()])->rememberForever('Template:MessageIou:ID:' . $templatesId, function () use ($templatesId) {
            return TemplateMessageIou::where('templates_id', $templatesId)->first();
        });
        return $template;
    }

    public function send()
    {
        $template = $this->getTemplateIou();
        if ($template) {
            $content = $this->replaceContent($template->sub_title);
            $model = new MessageIouModel();
            $model->user_id = $this->getUserId();
            $model->color = $template->color;
            $model->action = $template->action;
            $model->main_title = $template->main_title;
            $model->sub_title = $content;
            $model->total_amount = ($this->getParam()['amount'] * 100) ?? 0;
            $model->url = $this->getUrl() ?: $template->url ?: '';
            $send = $model->save();
            $this->setResult('MessageIou', $send);
        }
    }
}