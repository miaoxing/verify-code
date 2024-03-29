<?php

namespace MiaoxingTest\VerifyCode\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

class VerifyCodeTest extends BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        wei()->sms->setOption([
            'maxIpTimes' => 100,
            'maxMobileTimes' => 100,
        ]);
    }

    public function testSendSuc()
    {
        wei()->verifyCode->forget();

        wei()->sms->setOption('drivers', ['logSms']);

        $ret = wei()->verifyCode->send('13800138000');

        $this->assertRetSuc($ret);
    }

    public function testCheckSuc()
    {
        wei()->verifyCode->forget();

        wei()->sms->setOption('drivers', ['logSms']);

        $ret = wei()->verifyCode->send('13800138000');

        $this->assertRetSuc($ret);

        $code = wei()->session['verifyCode']['code'];
        $ret = wei()->verifyCode->check('13800138000', $code);

        $this->assertRetSuc($ret);
    }

    public function testSendWithErrorMobile()
    {
        wei()->verifyCode->forget();

        $ret = wei()->verifyCode->send('ttt');

        $this->assertRetErr($ret, '手机号码必须是11位长度的数字,以13,14,15,17或18开头', -1);
    }

    public function testCheckBeforeSend()
    {
        wei()->verifyCode->forget();

        $ret = wei()->verifyCode->check(123, 123);

        $this->assertRetErr($ret, '请先发送验证码', -1);
    }

    public function testCheckWithErrorMobile()
    {
        wei()->verifyCode->forget();

        wei()->sms->setOption('drivers', ['logSms']);

        $ret = wei()->verifyCode->send('13800138000');

        $this->assertRetSuc($ret);

        $ret = wei()->verifyCode->check('13800138001', '123456');

        $this->assertRetErr($ret, '验证码不正确,请重新获取', -2);
    }

    public function testCheckWithErrorVerifyCode()
    {
        wei()->verifyCode->forget();

        wei()->sms->setOption('drivers', ['logSms']);

        $ret = wei()->verifyCode->send('13800138000');

        $this->assertRetSuc($ret);

        $code = wei()->session['verifyCode']['code'];
        $ret = wei()->verifyCode->check('13800138000', $code + 1);

        $this->assertRetErr($ret, '验证码不正确,请重新获取', -2);
    }

    public function testCanSend()
    {
        wei()->verifyCode->forget();

        wei()->sms->setOption('drivers', ['logSms']);

        $ret = wei()->verifyCode->send('13800138000');

        $this->assertRetSuc($ret);

        $ret = wei()->verifyCode->send('13800138000');

        $this->assertRetErr($ret);

        $this->assertRegExp('/请过[0-9]+秒后再试/', $ret['message']);
    }
}
