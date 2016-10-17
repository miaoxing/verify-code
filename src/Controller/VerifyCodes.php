<?php

namespace Miaoxing\VerifyCode\Controller;

use miaoxing\plugin\BaseController;

class VerifyCodes extends BaseController
{
    protected $guestPages = ['verifyCodes'];

    public function createAction($req)
    {
        $ret = wei()->verifyCode->send($req['mobile']);

        return $ret;
    }
}
