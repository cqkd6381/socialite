<?php


namespace Laravel\Socialite\Two;

use Exception;
use Illuminate\Support\Arr;

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

    /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        return Arr::add(
            parent::getTokenFields($code), 'grant_type', 'authorization_code'
        );
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('http://laravel-oauth2-server.test/user', [
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
