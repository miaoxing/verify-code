<?php

namespace Miaoxing\VerifyCode\Controller;

use Miaoxing\Plugin\BaseController;

class VerifyCodes extends BaseController
{
    protected $guestPages = ['verifyCodes'];

    public function createAction($req)
    {
        $ret = wei()->verifyCode->send($req['mobile']);

        return $ret;
    }
}
