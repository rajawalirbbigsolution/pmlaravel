<?php
 namespace Laravel\Socialite\One; use Laravel\Socialite\AbstractUser; class User extends AbstractUser { public $token; public $tokenSecret; public function setToken($token, $tokenSecret) { $this->token = $token; $this->tokenSecret = $tokenSecret; return $this; } } 