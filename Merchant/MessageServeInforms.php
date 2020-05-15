<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/19
 * Time: 15:26
 */

namespace Composer\Push\Merchant;


use App\Models\TemplateMessageServeInforms;
use Composer\Push\BaseServe\CodeConfig;
use Composer\Push\BaseServe\SendSmsBase;
use Composer\Push\BaseServe\SendSmsInterface;
use App\Models\MessageServeInforms as MessageServeInformsModel;
use Illuminate\Support\Facades\Cache;

class MessageServeInforms extends SendSmsBase implements SendSmsInterface
{

    /**
     * @return bool
     */
    public function canPush()
    {
        if (!$this->canPushBefore()) {
            return false;
        }
        $template = $this->templates;
        if (!($template->getStatus() & CodeConfig::TEMPLATES_MESSAGE_SERVE_INFORMS_SWITCH)) {
            $this->setErrors(CodeConfig::TEMPLATE_DISABLED, '站内信-服务模板未启用');
            return false;
        }
        if (!$this->getUserId()) {
            $this->setErrors(CodeConfig::WECHAT_OPEN_ID_MISSING, '站内信-服务模板USER_ID不存在');
            return false;
        }
        return true;
    }

    public function content()
    {
        return true;
    }

    private function getTemplateServeInforms()
    {
        $templatesId = $this->templates->getId();
        if ($this->notCache) {
            return TemplateMessageServeInforms::where('templates_id', $templatesId)->first();
        }
        $template = Cache::tags(['pushMessage', $this->getKey()])->rememberForever('Template:MessageServeInforms:ID:' . $templatesId, function () use ($templatesId) {
            return TemplateMessageServeInforms::where('templates_id', $templatesId)->first();
        });
        return $template;
    }

    public function send()
    {
        $template = $this->getTemplateServeInforms();
        if ($template) {
            $content = $this->replaceContent($template->sub_title);

            $model = new MessageServeInformsModel();
            $model->user_id = $this->getUserId();
            $model->main_title = $template->main_title;
            $model->sub_title = $content;
            $model->url = $this->getUrl() ?: $template->url ?: '';

            $send = $model->save();

            $this->setResult('MessageServeInforms', $send);
        }
    }
}