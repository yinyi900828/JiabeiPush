<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/19
 * Time: 15:00
 */

namespace Composer\Push\Merchant;


use App\Models\TemplateMessageConsumption;
use Composer\Push\BaseServe\CodeConfig;
use Composer\Push\BaseServe\SendSmsBase;
use Composer\Push\BaseServe\SendSmsInterface;
use App\Models\MessageConsumption as MessageConsumptionModel;
use Illuminate\Support\Facades\Cache;

class MessageConsumption extends SendSmsBase implements SendSmsInterface
{

    public function canPush()
    {
        if (!$this->canPushBefore()) {
            return false;
        }
        $template = $this->templates;
        if (!($template->getStatus() & CodeConfig::TEMPLATES_MESSAGE_CONSUMPTION_SWITCH)) {
            $this->setErrors(CodeConfig::TEMPLATE_DISABLED, '站内信-消费模板未启用');
            return false;
        }
        if (!$this->getUserId()) {
            $this->setErrors(CodeConfig::WECHAT_OPEN_ID_MISSING, '站内信-消费USER_ID不存在');
            return false;
        }
        return true;
    }

    private function getTemplateConsumption()
    {
        $templatesId = $this->templates->getId();
        if ($this->notCache) {
            return TemplateMessageConsumption::where('templates_id', $templatesId)->first();
        }
        $template = Cache::tags(['pushMessage', $this->getKey()])->rememberForever('Template:MessageConsumption:ID:' . $templatesId, function () use ($templatesId) {
            return TemplateMessageConsumption::where('templates_id', $templatesId)->first();
        });
        return $template;
    }

    public function content()
    {
        return true;
    }

    public function send()
    {
        $template = $this->getTemplateConsumption();
        if ($template) {
            $content = $this->replaceContent($template->data);
            $model = new MessageConsumptionModel();
            $model->user_id = $this->getUserId();
            $model->url = $this->getUrl() ?: $template->url ?: '';
            $model->action = $template->action;
            $model->title = $template->title;
            $model->first = $template->first;
            $model->remark = $template->remark;
            $model->data = $content;
            $send = $model->save();
            $this->setResult('MessageConsumption', $send);
        }
    }
}