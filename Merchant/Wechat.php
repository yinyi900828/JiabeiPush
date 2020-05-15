<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/3/19
 * Time: 14:43
 */

namespace Composer\Push\Merchant;


use App\Jobs\WechatPush;
use App\Models\TemplateWeChat;
use Composer\Push\BaseServe\CodeConfig;
use Composer\Push\BaseServe\SendSmsBase;
use Composer\Push\BaseServe\SendSmsInterface;
use Illuminate\Support\Facades\Cache;

class Wechat extends SendSmsBase implements SendSmsInterface
{
    public function canPush()
    {
        if (!$this->canPushBefore()) {
            return false;
        }
        $template = $this->templates;
        if (!($template->getStatus() & CodeConfig::TEMPLATES_WECHAT_SWITCH)) {
            $this->setErrors(CodeConfig::TEMPLATE_DISABLED, '微信模板未启用');
            return false;
        }
        if (!$this->getWeChatOpenId()) {
            $this->setErrors(CodeConfig::WECHAT_OPEN_ID_MISSING, '微信OPENID不存在');
            return false;
        }
        return true;
    }

    private function getTemplateWeChat()
    {
        $templatesId = $this->templates->getId();
        if ($this->notCache) {
            return TemplateWeChat::where('templates_id', $templatesId)->first();
        }
        $templateWeChat = Cache::tags(['pushMessage', $this->getKey()])->rememberForever('Template:WeChatPush:ID:' . $templatesId, function () use ($templatesId) {
            return TemplateWeChat::where('templates_id', $templatesId)->first();
        });
        return $templateWeChat;
    }

    private function arrangeWeChatBody($content, $template_id)
    {
        return [
            'touser' => $this->getWeChatOpenId(),
            'template_id' => $template_id,
            'url' => $this->getUrl(),
            'data' => json_decode($content, true),
        ];
    }

    public function content()
    {
        $templateWeChat = $this->getTemplateWeChat();
        if (!$templateWeChat) {
            return [];
        }
        $content = $this->replaceContent($templateWeChat->content);
        $body = $this->arrangeWeChatBody($content, $templateWeChat->template_key);
        return [
            'user_id' => $this->getUserId(),
            'body' => $body,
            'push_type' => $this->getKey(),
        ];
    }

    public function send()
    {
        if (!$this->content()) {
            return;
        }
        dispatch(new WechatPush($this->content()));
        $this->setResult('WeChat', $this->content());
    }

}