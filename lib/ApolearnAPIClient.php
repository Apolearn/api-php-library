<?php

/**
 * APOLEARN-API-PHP-CLIENT : Simple PHP client for the Apolearn API
 *
 * PHP version 1.0.1
 *
 * @category Awesomeness
 * @package  APOLEARN-API-PHP-CLIENT
 * @author   Apolearn <tech@apolearn.com>
 * @license  MIT License
 * @version  1.0.1
 * @link     https://github.com/Apolearn/apolearn-api-php-client
 */

	
class ApolearnAPIClient
{
	/**
     * @var array
     */
	private $settings;
	
	/**
     * @var string
     */
	private $token;

	/**
     * Create the API access object.
     * Requires the cURL library
     *
     * @param string $instance_url Your instance URL with https:// and without trailing slash. Ex: https://yourcompany.apolearn.com
     * @param string $public_key Your API public key
     * @param string $private_key Your API private key
     */
    function __construct($instance_url, $public_key, $private_key) {
	    $this->settings->instance_url = $instance_url;
	    $this->settings->public_key = $public_key;
	    $this->settings->private_key = $private_key;
    }
    
    /**
     * Login to Apolearn.
     *
     * @param string $username An admin username
     * @param string $password Password of your admin user
     *
     * @return string return the auth_token
     */
    public function login($username, $password)
	{
		$args = array('username' => $username, 'password' => $password);

		$result = $this->request('auth.gettoken', 'post', $args);

		if ($result->status === 0 && $result->result)
		{
			$this->token = $result->result;
			return $this->token;
		}
		return false;	
	}
	
	/**
     * List all users
     *
     * @param integer $offset Where to start. 0 by default
     * @param interger $limit Number of user to return. 20 by default
     * @param boolean $enabledonly get only enabled users. false by default
     *
     * @return array An array of user object
     */
	public function getUsers($offset = 0; $limit = 20; $enabledonly = false)
	{
		return $this->request('users', 'get', array('limit' => $limit, 'offset' => $offset, 'enabledonly' => $enabledonly, 'auth_token' => $this->token));
	}
	
	/**
     * List all users types
     *
     * @return array An array of users types
     */
	public function getUsersTypes()
	{
		return $this->request('users/types', 'get', array('auth_token' => $this->token));
	}
	
	/**
     * Create a new user
     *
     * @param string $firstname User firstname
     * @param string $lastname User lastname
     * @param string $lastname User email
     * @param array $otherfields optional user fields:: usertype_id, password, gender (male or female), company, job, city, country, language (fr, en, es)
     * @param bolean $sendusercredential Send credential by email to the user. false by default
     *
     * @return the user id, password and other profile information
     */
	public function addUser($firstname, $lastname, $email, $otherfields = array(), $sendusercredential = false)
	{
		$otherfields['firstname'] = $firstname;
		$otherfields['lastname'] = $lastname;
		$otherfields['email'] = $email;
		$otherfields['sendusercredential'] => $sendusercredential;
		$otherfields['auth_token'] = $this->token;
		
		return $this->request('users', 'post', $otherfields);
	}
	
	/**
     * Edit user information and password
     *
     * @param integer $user_id Apolearn User id
     * @param array $fields an array of fields you want to edit firstname, lastname, email, usertype_id, password, gender (male or female), company, job, city, country, language (fr, en, es)
     * @param bolean $sendusercredential Send new credential by email to the user. false by default
     *
     * @return the user id, password and other profile information
     */
	public function editUser($user_id, $fields, $sendusercredential = false)
	{
		$fields['auth_token'] = $this->token;
		
		return $this->request("users/{$user_id}", 'put', $fields);
	}
	
	/**
     * Disable user account
     *
     * @param integer $user_id Apolearn User id
     * @param string $reason optional reason why you've disabled his account
     *
     * @return the user id
     */
	public function disableUser($user_id, $reason = '')
	{
		return $this->request("users/disable/{$user_id}", 'put', array('auth_token' => $this->token, 'reason' => $reason));
	}
	
	/**
     * Enable a disabled user account
     *
     * @param integer $user_id Apolearn User id
     *
     * @return the user id
     */
	public function enableUser($user_id)
	{
		return $this->request("users/enable/{$user_id}", 'put', array('auth_token' => $this->token));
	}
	
	/**
     * Call an Apolearn API method
     *
     * @param string $call name of the method
     * @param string $method GET, POST, PUT
     * @param array $args an array of arguments
     * @param interger $timeout call timeout
     *
     */
	public function request($call = 'system.api.list', $method = 'get', $args = array(), $timeout = 10)
    {
        $url = $this->settings->instance_url . '/services/api/rest/json/' . $call;

        if ($this->settings->public_key)
            $args['api_key'] = $this->settings->public_key;

        $ch = curl_init();
        if ($method == 'post')
        {
            curl_setopt($ch, CURLOPT_POST,true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        }
        else if ($method == 'put')
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        }
        else
        {
            if (count($args) > 0)
            {
                foreach ($args as $key => $value)
                    $url .= "&{$key}={$value}";
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        if ($this->httpauth)
                curl_setopt($ch, CURLOPT_USERPWD, $this->httpauth);
        if(($data = curl_exec($ch)) === false)
		{
		    echo 'Erreur Curl : ' . curl_error($ch);
		     curl_close($ch);
		    return false;
		}
        curl_close($ch);
        $result = json_decode($data);
        return $result;
    }
}