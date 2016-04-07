<?php 

namespace LvApiAuth\Providers;

use Illuminate\Auth\UserProviderInterface;
use Illuminate\Auth\GenericUser;
use Illuminate\Auth\UserInterface;
use Config;
use Hash;

class LvApiAuthUserProvider implements UserProviderInterface {
	private $guzzle;
	private $options;

	public function __construct()
	{	
		$this->guzzle = new \GuzzleHttp\Client(['base_url' => Config::get('lv-api-auth::base_url')]);
		$this->options = [
			'debug' 	=> false,
			'headers'	=> [
				'Authorization' => 'Bearer ' . Config::get('lv-api-auth::static_token')
			]
		];
	}

	public function retrieveById($identifier) 
	{
		$this->options['query'] = [
			'id' => $identifier
		];

		try {
	        $response = $this->guzzle->post(Config::get('lv-api-auth::get_user_ep'), $this->options);

	        $response = $response->getBody();

	        $response = json_decode($response);

	        $user = isset($response->data) ? $response->data : null; 

	        if ( ! is_null($user))
			{
				$response = new GenericUser((array) $user);
			} else {
				$response = null;
			}
	    } catch (\Exception $e) {
	    	 throw $e;
	    	 $response = null;
	    }

	    return $response;
	}

	public function retrieveByCredentials(array $credentials) 
	{	
		if(isset($credentials['password_confirmation']))
			unset($credentials['password_confirmation']);

		$this->options['query'] = $credentials;

		try {
	        $response = $this->guzzle->post(Config::get('lv-api-auth::get_user_ep'), $this->options);

	        $response = $response->getBody();

	        $response = json_decode($response);

	        $user = isset($response->data) &&  ! empty($response->data) ? $response->data : null; 

	        if ( ! is_null($user))
			{
				$response = new GenericUser((array) $user);
			} else {
				$response = null;
			}
	    } catch (\Exception $e) {
	    	 throw $e;
	    	 $response = null;
	    }

	    return $response;
	}

	public function validateCredentials(UserInterface $user, array $credentials)
	{	
		$this->options['query'] = $credentials;

		$response = $this->guzzle->post(Config::get('lv-api-auth::user_login_ep'), $this->options);

        $response = $response->getBody();

        $response = json_decode($response);

        if (isset($response->status) && $response->status) {
        	\Session::put('acc_token',$response->token);

        	return true;
        }

		return false;
	}

	/**
	* Needed by Laravel 4.1.26 and above
	*/
	public function retrieveByToken($identifier, $token)
	{
	   return new \Exception('not implemented');
	}

	/**
	 * Needed by Laravel 4.1.26 and above
	*/

	public function updateRememberToken(UserInterface $user, $token)
	{
		try 
		{
			$query = [
				'id' => $user->getAuthIdentifier(),
				'remember_token' => $token
			];
			$this->options['query'] = $query;
			$response = $this->guzzle->patch(Config::get('lv-api-auth::user_update_token_ep'), $this->options);
	        $response = $response->getBody();
	        $response = json_decode($response);
	        if ($response->status == 1) {
	        	return true;
	        } else {
		    	return false;
		    }
	    } 
	    catch (\Exception $e)
	    {
	    	 throw $e;
	    }
	}
}