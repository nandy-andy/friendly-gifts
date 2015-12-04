<?php
namespace FriendlyGifts\User;

class User {

	private $login;
	private $password;
	private $name;

	public function __construct($user, $pass) {
		$this->login = $user;
		$this->password = $pass;
	}

	public function login() {
		if( $this->login === 'test' && $this->password === 'test' ) {
			$this->name = 'Tester';
		}
	}

	public function isLoggedIn() {
		return !empty($this->name);
	}

	public function getName() {
		return $this->name;
	}

}