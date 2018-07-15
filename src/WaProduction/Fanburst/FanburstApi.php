<?php

/**
 * @author: Martin Liprt
 * @email: tuxxx128@protonmail.com
 */

namespace WaProduction\Fanburst;

class FanburstApi
{
    CONST API_VERSION = 'v1',
        API_END_POINT_URL = 'https://api.fanburst.com',
        OAUTH_URL = 'https://fanburst.com/oauth/authorize',
        OAUTH_TOKEN_URL = 'https://fanburst.com/oauth/token';

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var string */
    private $redirectUri;

    /** @var string */
    private $accessToken;

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     */
    public function __construct($clientId, $clientSecret, $redirectUri)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri  = $redirectUri;
    }

    /**
     * Return URL for authorize of APP.
     * @param array $options
     * @return string
     */
    public function getOauthLoginUrl(array $options = [])
    {
        $url = self::OAUTH_URL;

        $parameters = [
            'client_id' => $this->clientId,
            'response_type' => isset($options['response_type']) ? $options['response_type']
                    : 'code',
            'redirect_uri' => $this->redirectUri,
        ];

        if (isset($options['state'])) {
            $parameters['state'] = $options['state'];
        }

        return $url.'?'.http_build_query($parameters);
    }

    /**
     * Exchange code for access token.
     * @param type $code
     * @return type
     */
    public function exachangeCodeForAccessToken($code)
    {
        $parameters = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
        ];

        $resultObject = $this->doHttpRequest(self::OAUTH_TOKEN_URL, 'POST',
            $parameters);

        $this->setAccessToken($resultObject->access_token);

        return $this->accessToken;
    }

    /**
     * Multiple execute API calls.
     * @param array $targets
     * @param callback $callbFce
     * @param array $stats
     * @return mixed
     */
    public function multipleCallTargets(array $targets, callable $callbFce,
                                        array &$stats = null)
    {
        foreach ($targets as $target) {
            if ($stats) {
                $stats[] = call_user_func($callbFce, $target);
            } else {
                if(!call_user_func($callbFce, $target)) {
                    return false;
                }
            }
        }

        return ($stats) ? $stats : true;
    }
    
    /**
     * Search a user - https://developers.fanburst.com/#search_users
     * @param string $q
     * @return object
     */
    public function searchUsers($q)
    {
        return $this->doHttpApiRequest('/users/search', [], ['query' => $q]);
    }

    /**
     * Search single a user - https://developers.fanburst.com/#search_users
     * @param string $q
     * @return object
     */
    public function searchUser($q)
    {
        return $this->searchUsers($q)[0];
    }

    /**
     * Follow a user - https://developers.fanburst.com/#follow-user
     * @param string $userId
     * @return object
     */
    public function followUser($userId)
    {
        return $this->followingUserPrototype($userId, 'POST');
    }

    /**
     * UN/Follow a user (ready for POST, DELETE) - https://developers.fanburst.com/#follow-user
     * @param string $userId
     * @return object
     */
    private function followingUserPrototype($userId, $method)
    {
        return $this->doHttpApiRequest('/me/following', ['user_id' => $userId],
                [], $method);
    }

    /**
     * Abstract get results from API resource - see more on: https://developers.fanburst.com
     * @param string $source
     * @return object
     */
    public function callApi($source)
    {
        return $this->doHttpApiRequest($source);
    }

    private function doHttpApiRequest($source, array $parameters = [],
                                      array $queryParameters = [],
                                      $method = 'GET')
    {
        if (!$this->getAccessToken()) {
            throw new FanburstApiException('First you must set acess token and after you can call API methods!');
        }

        $qp = $queryParameters + [
            'client_id' => $this->clientId,
            'access_token' => $this->getAccessToken(),
        ];

        return $this->doHttpRequest(self::API_END_POINT_URL, $method,
                $parameters, [], $source.'?'.http_build_query($qp));
    }

    private function doHttpRequest($endPoint, $method, array $parameters = [],
                                   array $headers = [], $source = null)
    {
        $headers['Accept-Version: '.self::API_VERSION] = true;

        $curlInit = curl_init($endPoint.$source);

        switch ($method) {
            case 'POST':
                curl_setopt($curlInit, CURLOPT_POST, count($parameters));
                curl_setopt($curlInit, CURLOPT_POSTFIELDS,
                    http_build_query($parameters));
                break;
        }

        curl_setopt($curlInit, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);

        $response = new FanburstResult(json_decode(curl_exec($curlInit)));
        curl_close($curlInit);

        return $response;
    }

    /**
     * Set custom access token.
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get client id.
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get access token.
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Get redirect uri.
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }
}
