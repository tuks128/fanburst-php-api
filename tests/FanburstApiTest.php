<?php

/**
 * @author: Martin Liprt
 * @email: tuxxx128@protonmail.com
 */

use PHPUnit\Framework\TestCase;
use WaProduction\Fanburst\FanburstApi;

class FanburstApiTest extends TestCase
{
    private $fanburstApi;

    public function __construct()
    {
        $this->fanburstApi = new FanburstApi('5b2dccde-4c6c-4a0f-bb46-da85214345fc',
            '5b93557e590ed959c6a7794bf462b295a858d0c81aa1e8377456f007f4ea7461',
            'http://fanburst-php-api.loc/');
    }

    public function testUrlForLogin()
    {
        $urlForLogin = FanburstApi::OAUTH_URL;
        $urlForLogin .= '?client_id=';
        $urlForLogin .= $this->fanburstApi->getClientId();
        $urlForLogin .= '&response_type=code';
        $urlForLogin .= '&redirect_uri=';
        $urlForLogin .= urlencode($this->fanburstApi->getRedirectUri());

        $this->assertContains($urlForLogin,
            $this->fanburstApi->getOauthLoginUrl());
    }
}
