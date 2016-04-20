<?php 

namespace Ehtasham89\LvApiAuth;

use Illuminate\Auth\UserInterface;

class LvApiUser implements UserInterface {
	
	/*
	 * protected user object
	 */
	protected $user;

	public function __construct($userInfo)
	{
		$this->user = $userInfo;
	}
	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return (string) $this->user['id'];
	}
	/**
	 * Get the return user info in array.
	 *
	 * @return array
	 */
	public function getAuthUserDetail()
	{
		return $this->user;
	}
	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
	}
	public function getRememberToken()
	{
		return $this->user[$this->getRememberTokenName()];
	}
	/**
	 * Needed by Laravel 4.1.26 and above
	 */
	public function setRememberToken($token)
	{
		$this->user[$this->getRememberTokenName()] = $token;
	}
	public function getRememberTokenName()
	{
		return 'remember_token';
	}
}
