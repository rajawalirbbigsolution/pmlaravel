<?php
 namespace Laravel\Socialite\Two; use GuzzleHttp\Client; use Illuminate\Support\Arr; use Illuminate\Support\Str; use Illuminate\Http\Request; use GuzzleHttp\ClientInterface; use Illuminate\Http\RedirectResponse; use Laravel\Socialite\Contracts\Provider as ProviderContract; abstract class AbstractProvider implements ProviderContract { protected $request; protected $httpClient; protected $clientId; protected $clientSecret; protected $redirectUrl; protected $parameters = []; protected $scopes = []; protected $scopeSeparator = ','; protected $encodingType = PHP_QUERY_RFC1738; protected $stateless = false; protected $guzzle = []; public function __construct(Request $request, $clientId, $clientSecret, $redirectUrl, $guzzle = []) { $this->guzzle = $guzzle; $this->request = $request; $this->clientId = $clientId; $this->redirectUrl = $redirectUrl; $this->clientSecret = $clientSecret; } abstract protected function getAuthUrl($state); abstract protected function getTokenUrl(); abstract protected function getUserByToken($token); abstract protected function mapUserToObject(array $user); public function redirect() { $state = null; if ($this->usesState()) { $this->request->session()->put('state', $state = $this->getState()); } return new RedirectResponse($this->getAuthUrl($state)); } protected function buildAuthUrlFromBase($url, $state) { return $url.'?'.http_build_query($this->getCodeFields($state), '', '&', $this->encodingType); } protected function getCodeFields($state = null) { $fields = [ 'client_id' => $this->clientId, 'redirect_uri' => $this->redirectUrl, 'scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator), 'response_type' => 'code', ]; if ($this->usesState()) { $fields['state'] = $state; } return array_merge($fields, $this->parameters); } protected function formatScopes(array $scopes, $scopeSeparator) { return implode($scopeSeparator, $scopes); } public function user() { if ($this->hasInvalidState()) { throw new InvalidStateException; } $response = $this->getAccessTokenResponse($this->getCode()); $user = $this->mapUserToObject($this->getUserByToken( $token = Arr::get($response, 'access_token') )); return $user->setToken($token) ->setRefreshToken(Arr::get($response, 'refresh_token')) ->setExpiresIn(Arr::get($response, 'expires_in')); } public function userFromToken($token) { $user = $this->mapUserToObject($this->getUserByToken($token)); return $user->setToken($token); } protected function hasInvalidState() { if ($this->isStateless()) { return false; } $state = $this->request->session()->pull('state'); return ! (strlen($state) > 0 && $this->request->input('state') === $state); } public function getAccessTokenResponse($code) { $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body'; $response = $this->getHttpClient()->post($this->getTokenUrl(), [ 'headers' => ['Accept' => 'application/json'], $postKey => $this->getTokenFields($code), ]); return json_decode($response->getBody(), true); } protected function getTokenFields($code) { return [ 'client_id' => $this->clientId, 'client_secret' => $this->clientSecret, 'code' => $code, 'redirect_uri' => $this->redirectUrl, ]; } protected function getCode() { return $this->request->input('code'); } public function scopes($scopes) { $this->scopes = array_unique(array_merge($this->scopes, (array) $scopes)); return $this; } public function setScopes($scopes) { $this->scopes = array_unique((array) $scopes); return $this; } public function getScopes() { return $this->scopes; } public function redirectUrl($url) { $this->redirectUrl = $url; return $this; } protected function getHttpClient() { if (is_null($this->httpClient)) { $this->httpClient = new Client($this->guzzle); } return $this->httpClient; } public function setHttpClient(Client $client) { $this->httpClient = $client; return $this; } public function setRequest(Request $request) { $this->request = $request; return $this; } protected function usesState() { return ! $this->stateless; } protected function isStateless() { return $this->stateless; } public function stateless() { $this->stateless = true; return $this; } protected function getState() { return Str::random(40); } public function with(array $parameters) { $this->parameters = $parameters; return $this; } } 