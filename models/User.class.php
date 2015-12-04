<?php
namespace FriendlyGifts\User;

class User {

	private $login;
	private $password;
	private $name;
	private $fid;

	public function __construct($data) {
		if( !empty($data['fid']) && !empty($data['name']) ) {
			$this->login = 'facebook_' . $data['fid'];
			$this->fid = $data['fid'];
			$this->name = $data['name'];
		} else if( !empty($data['login']) && !empty($data['password']) ) {
			$this->login = $data['login'];
			$this->password = $data['password'];
		}
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