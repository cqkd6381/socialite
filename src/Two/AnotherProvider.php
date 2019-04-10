<?php


namespace Laravel\Socialite\Two;


use Exception;

class AnotherProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @var array
     */
    protected $scopes = ['user'];

    /**
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('http://laravel-oauth2-server.test/oauth/authorize', $state);
    }

    /**
     * @return string
     */
    protected function getTokenUrl()
    {
        return 'http://laravel-oauth2-server.test/oauth/token';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://www.googleapis.com/oauth2/v3/userinfo', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        $user = json_decode($response->getBody(), true);

        return $user;
    }


    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['name'],
            'name' => Arr::get($user, 'name'),
            'email' => Arr::get($user, 'email'),
        ]);
    }

    protected function getRequestOptions()
    {
        return [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];
    }

}
