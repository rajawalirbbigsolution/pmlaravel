<?php
 namespace League\OAuth1\Client\Signature; use League\OAuth1\Client\Credentials\ClientCredentialsInterface; use League\OAuth1\Client\Credentials\CredentialsInterface; abstract class Signature implements SignatureInterface { protected $clientCredentials; protected $credentials; public function __construct(ClientCredentialsInterface $clientCredentials) { $this->clientCredentials = $clientCredentials; } public function setCredentials(CredentialsInterface $credentials) { $this->credentials = $credentials; } protected function key() { $key = rawurlencode($this->clientCredentials->getSecret()).'&'; if ($this->credentials !== null) { $key .= rawurlencode($this->credentials->getSecret()); } return $key; } } 