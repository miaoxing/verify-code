<?php

namespace Miaoxing\VerifyCode\Service;

use miaoxing\plugin\BaseService;

/**
 * @property \Wei\Session $session
 */
class VerifyCode extends BaseService
{
    /**
     * 默认短信内容
     *
     * @var string
     */
    protected $smsContent = '您的验证码为%s，请于1分钟内正确输入验证码';

    /**
     * 各短信服务商的验证码模板ID
     *
     * 如['ucpaas' => '14153', 'xxx' => 'xxx']
     *
     * @var array
     */
    protected $tplIds = [];

    /**
     * 验证码超时时间
     *
     * @var int
     */
    protected $intervalTime = 60;

    /**
     * {@inheritdoc}
     */
    protected $providers = [
        'app' => 'app.db',
    ];

    /**
     * 获取超时时间
     *
     * @return int
     */
    public function getIntervalTime()
    {
        return $this->intervalTime;
    }

    /**
     * 申请验证码
     *
     * @param string $mobile
     * @return array
     */
    public function send($mobile)
    {
        $param = get_defined_vars();

        // 1. 检查是否可以发送验证码
        $ret = $this->canSend();
        if ($ret['code'] !== 1) {
            return $this->sendRet($ret['code'], $ret['message'], $param);
        }

        // 2. 生成验证码并发送短信
        $code = mt_rand(100000, 999999);
        $ret = wei()->sms->send([
            'mobile' => $mobile,
            'content' => sprintf($this->smsContent, $code),
            'tplIds' => $this->tplIds,
            'data' => [$code, floor(1.0 * $this->intervalTime / 60)], // 1分钟
        ]);
        if ($ret['code'] !== 1) {
            return $this->sendRet($ret['code'], $ret['message'], $param);
        }

        // 3. 记录手机号码和验证码到Session
        $this->session['verifyCode'] = [
            'code' => $code,
            'mobile' => $mobile,
            'canSendTime' => time() + $this->intervalTime
        ];
        return $this->sendRet($ret['code'], $ret['message'], $param);
    }

    /**
     * 校验验证码
     *
     * @param $mobile
     * @param string $code
     * @return array
     */
    public function check($mobile, $code)
    {
        $param = get_defined_vars();

        if (!isset($this->session['verifyCode'])) {
            return $this->checkRet(-1, '请先发送验证码', $param);
        }

        $verifyCode = $this->session['verifyCode'];
        if ($verifyCode['mobile'] != $mobile || $verifyCode['code'] != $code) {
            unset($this->session['verifyCode']);
            return $this->checkRet(-2, '验证码不正确,请重新获取', $param);
        }

        return $this->checkRet(1, '验证通过', $param);
    }

    /**
     * @param int $code
     * @param string $message
     * @param array $param
     * @return array
     */
    protected function sendRet($code, $message, $param)
    {
        $ret = ['code' => $code, 'message' => $message];
        wei()->user->log('发送验证码', ['param' => $param, 'ret' => $ret]);
        return $ret;
    }

    /**
     * @param int $code
     * @param string $message
     * @param array $param
     * @return array
     */
    protected function checkRet($code, $message, $param)
    {
        $ret = ['code' => $code, 'message' => $message];
        wei()->user->log('校验验证码', ['param' => $param, 'ret' => $ret]);

        return $ret;
    }

    /**
     * 删除验证码信息
     *
     * @return $this
     */
    public function forget()
    {
        unset($this->session['verifyCode']);

        return $this;
    }

    /**
     * 判断是否可以发送验证码
     *
     * @return bool
     */
    protected function canSend()
    {
        $verifyCode = $this->session['verifyCode'];
        if (isset($verifyCode['canSendTime']) && $verifyCode['canSendTime'] > time()) {
            return ['code' => -1, 'message' => sprintf('请过%s秒后再试', $verifyCode['canSendTime'] - time())];
        }

        return ['code' => 1, 'message' => '可以发送'];
    }
}
