<?php
 namespace Laravel\Socialite\One; use Illuminate\Http\Request; use InvalidArgumentException; use Illuminate\Http\RedirectResponse; use League\OAuth1\Client\Server\Server; use League\OAuth1\Client\Credentials\TokenCredentials; use Laravel\Socialite\Contracts\Provider as ProviderContract; abstract class AbstractProvider implements ProviderContract { protected $request; protected $server; protected $userHash; public function __construct(Request $request, Server $server) { $this->server = $server; $this->request = $request; } public function redirect() { $this->request->session()->put( 'oauth.temp', $temp = $this->server->getTemporaryCredentials() ); return new RedirectResponse($this->server->getAuthorizationUrl($temp)); } public function user() { if (! $this->hasNecessaryVerifier()) { throw new InvalidArgumentException('Invalid request. Missing OAuth verifier.'); } $token = $this->getToken(); $user = $this->server->getUserDetails( $token, $this->shouldBypassCache($token->getIdentifier(), $token->getSecret()) ); $instance = (new User)->setRaw($user->extra) ->setToken($token->getIdentifier(), $token->getSecret()); return $instance->map([ 'id' => $user->uid, 'nickname' => $user->nickname, 'name' => $user->name, 'email' => $user->email, 'avatar' => $user->imageUrl, ]); } public function userFromTokenAndSecret($token, $secret) { $tokenCredentials = new TokenCredentials(); $tokenCredentials->setIdentifier($token); $tokenCredentials->setSecret($secret); $user = $this->server->getUserDetails( $tokenCredentials, $this->shouldBypassCache($token, $secret) ); $instance = (new User)->setRaw($user->extra) ->setToken($tokenCredentials->getIdentifier(), $tokenCredentials->getSecret()); return $instance->map([ 'id' => $user->uid, 'nickname' => $user->nickname, 'name' => $user->name, 'email' => $user->email, 'avatar' => $user->imageUrl, ]); } protected function getToken() { $temp = $this->request->session()->get('oauth.temp'); return $this->server->getTokenCredentials( $temp, $this->request->get('oauth_token'), $this->request->get('oauth_verifier') ); } protected function hasNecessaryVerifier() { return $this->request->has('oauth_token') && $this->request->has('oauth_verifier'); } protected function shouldBypassCache($token, $secret) { $newHash = sha1($token.'_'.$secret); if (! empty($this->userHash) && $newHash !== $this->userHash) { $this->userHash = $newHash; return true; } $this->userHash = $this->userHash ?: $newHash; return false; } public function setRequest(Request $request) { $this->request = $request; return $this; } } 