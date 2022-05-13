<?php
 namespace Symfony\Component\HttpFoundation; use Symfony\Component\HttpFoundation\Exception\ConflictingHeadersException; use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException; use Symfony\Component\HttpFoundation\Session\SessionInterface; class Request { const HEADER_FORWARDED = 0b00001; const HEADER_X_FORWARDED_FOR = 0b00010; const HEADER_X_FORWARDED_HOST = 0b00100; const HEADER_X_FORWARDED_PROTO = 0b01000; const HEADER_X_FORWARDED_PORT = 0b10000; const HEADER_X_FORWARDED_ALL = 0b11110; const HEADER_X_FORWARDED_AWS_ELB = 0b11010; const HEADER_CLIENT_IP = self::HEADER_X_FORWARDED_FOR; const HEADER_CLIENT_HOST = self::HEADER_X_FORWARDED_HOST; const HEADER_CLIENT_PROTO = self::HEADER_X_FORWARDED_PROTO; const HEADER_CLIENT_PORT = self::HEADER_X_FORWARDED_PORT; const METHOD_HEAD = 'HEAD'; const METHOD_GET = 'GET'; const METHOD_POST = 'POST'; const METHOD_PUT = 'PUT'; const METHOD_PATCH = 'PATCH'; const METHOD_DELETE = 'DELETE'; const METHOD_PURGE = 'PURGE'; const METHOD_OPTIONS = 'OPTIONS'; const METHOD_TRACE = 'TRACE'; const METHOD_CONNECT = 'CONNECT'; protected static $trustedProxies = array(); protected static $trustedHostPatterns = array(); protected static $trustedHosts = array(); protected static $trustedHeaders = array( self::HEADER_FORWARDED => 'FORWARDED', self::HEADER_CLIENT_IP => 'X_FORWARDED_FOR', self::HEADER_CLIENT_HOST => 'X_FORWARDED_HOST', self::HEADER_CLIENT_PROTO => 'X_FORWARDED_PROTO', self::HEADER_CLIENT_PORT => 'X_FORWARDED_PORT', ); protected static $httpMethodParameterOverride = false; public $attributes; public $request; public $query; public $server; public $files; public $cookies; public $headers; protected $content; protected $languages; protected $charsets; protected $encodings; protected $acceptableContentTypes; protected $pathInfo; protected $requestUri; protected $baseUrl; protected $basePath; protected $method; protected $format; protected $session; protected $locale; protected $defaultLocale = 'en'; protected static $formats; protected static $requestFactory; private $isHostValid = true; private $isForwardedValid = true; private static $trustedHeaderSet = -1; private static $trustedHeaderNames = array( self::HEADER_FORWARDED => 'FORWARDED', self::HEADER_CLIENT_IP => 'X_FORWARDED_FOR', self::HEADER_CLIENT_HOST => 'X_FORWARDED_HOST', self::HEADER_CLIENT_PROTO => 'X_FORWARDED_PROTO', self::HEADER_CLIENT_PORT => 'X_FORWARDED_PORT', ); private static $forwardedParams = array( self::HEADER_X_FORWARDED_FOR => 'for', self::HEADER_X_FORWARDED_HOST => 'host', self::HEADER_X_FORWARDED_PROTO => 'proto', self::HEADER_X_FORWARDED_PORT => 'host', ); public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null) { $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content); } public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null) { $this->request = new ParameterBag($request); $this->query = new ParameterBag($query); $this->attributes = new ParameterBag($attributes); $this->cookies = new ParameterBag($cookies); $this->files = new FileBag($files); $this->server = new ServerBag($server); $this->headers = new HeaderBag($this->server->getHeaders()); $this->content = $content; $this->languages = null; $this->charsets = null; $this->encodings = null; $this->acceptableContentTypes = null; $this->pathInfo = null; $this->requestUri = null; $this->baseUrl = null; $this->basePath = null; $this->method = null; $this->format = null; } public static function createFromGlobals() { $server = $_SERVER; if ('cli-server' === PHP_SAPI) { if (array_key_exists('HTTP_CONTENT_LENGTH', $_SERVER)) { $server['CONTENT_LENGTH'] = $_SERVER['HTTP_CONTENT_LENGTH']; } if (array_key_exists('HTTP_CONTENT_TYPE', $_SERVER)) { $server['CONTENT_TYPE'] = $_SERVER['HTTP_CONTENT_TYPE']; } } $request = self::createRequestFromFactory($_GET, $_POST, array(), $_COOKIE, $_FILES, $server); if (0 === strpos($request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded') && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH')) ) { parse_str($request->getContent(), $data); $request->request = new ParameterBag($data); } return $request; } public static function create($uri, $method = 'GET', $parameters = array(), $cookies = array(), $files = array(), $server = array(), $content = null) { $server = array_replace(array( 'SERVER_NAME' => 'localhost', 'SERVER_PORT' => 80, 'HTTP_HOST' => 'localhost', 'HTTP_USER_AGENT' => 'Symfony/3.X', 'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5', 'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7', 'REMOTE_ADDR' => '127.0.0.1', 'SCRIPT_NAME' => '', 'SCRIPT_FILENAME' => '', 'SERVER_PROTOCOL' => 'HTTP/1.1', 'REQUEST_TIME' => time(), ), $server); $server['PATH_INFO'] = ''; $server['REQUEST_METHOD'] = strtoupper($method); $components = parse_url($uri); if (isset($components['host'])) { $server['SERVER_NAME'] = $components['host']; $server['HTTP_HOST'] = $components['host']; } if (isset($components['scheme'])) { if ('https' === $components['scheme']) { $server['HTTPS'] = 'on'; $server['SERVER_PORT'] = 443; } else { unset($server['HTTPS']); $server['SERVER_PORT'] = 80; } } if (isset($components['port'])) { $server['SERVER_PORT'] = $components['port']; $server['HTTP_HOST'] = $server['HTTP_HOST'].':'.$components['port']; } if (isset($components['user'])) { $server['PHP_AUTH_USER'] = $components['user']; } if (isset($components['pass'])) { $server['PHP_AUTH_PW'] = $components['pass']; } if (!isset($components['path'])) { $components['path'] = '/'; } switch (strtoupper($method)) { case 'POST': case 'PUT': case 'DELETE': if (!isset($server['CONTENT_TYPE'])) { $server['CONTENT_TYPE'] = 'application/x-www-form-urlencoded'; } case 'PATCH': $request = $parameters; $query = array(); break; default: $request = array(); $query = $parameters; break; } $queryString = ''; if (isset($components['query'])) { parse_str(html_entity_decode($components['query']), $qs); if ($query) { $query = array_replace($qs, $query); $queryString = http_build_query($query, '', '&'); } else { $query = $qs; $queryString = $components['query']; } } elseif ($query) { $queryString = http_build_query($query, '', '&'); } $server['REQUEST_URI'] = $components['path'].('' !== $queryString ? '?'.$queryString : ''); $server['QUERY_STRING'] = $queryString; return self::createRequestFromFactory($query, $request, array(), $cookies, $files, $server, $content); } public static function setFactory($callable) { self::$requestFactory = $callable; } public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null) { $dup = clone $this; if (null !== $query) { $dup->query = new ParameterBag($query); } if (null !== $request) { $dup->request = new ParameterBag($request); } if (null !== $attributes) { $dup->attributes = new ParameterBag($attributes); } if (null !== $cookies) { $dup->cookies = new ParameterBag($cookies); } if (null !== $files) { $dup->files = new FileBag($files); } if (null !== $server) { $dup->server = new ServerBag($server); $dup->headers = new HeaderBag($dup->server->getHeaders()); } $dup->languages = null; $dup->charsets = null; $dup->encodings = null; $dup->acceptableContentTypes = null; $dup->pathInfo = null; $dup->requestUri = null; $dup->baseUrl = null; $dup->basePath = null; $dup->method = null; $dup->format = null; if (!$dup->get('_format') && $this->get('_format')) { $dup->attributes->set('_format', $this->get('_format')); } if (!$dup->getRequestFormat(null)) { $dup->setRequestFormat($this->getRequestFormat(null)); } return $dup; } public function __clone() { $this->query = clone $this->query; $this->request = clone $this->request; $this->attributes = clone $this->attributes; $this->cookies = clone $this->cookies; $this->files = clone $this->files; $this->server = clone $this->server; $this->headers = clone $this->headers; } public function __toString() { try { $content = $this->getContent(); } catch (\LogicException $e) { return trigger_error($e, E_USER_ERROR); } $cookieHeader = ''; $cookies = array(); foreach ($this->cookies as $k => $v) { $cookies[] = $k.'='.$v; } if (!empty($cookies)) { $cookieHeader = 'Cookie: '.implode('; ', $cookies)."\r\n"; } return sprintf('%s %s %s', $this->getMethod(), $this->getRequestUri(), $this->server->get('SERVER_PROTOCOL'))."\r\n". $this->headers. $cookieHeader."\r\n". $content; } public function overrideGlobals() { $this->server->set('QUERY_STRING', static::normalizeQueryString(http_build_query($this->query->all(), '', '&'))); $_GET = $this->query->all(); $_POST = $this->request->all(); $_SERVER = $this->server->all(); $_COOKIE = $this->cookies->all(); foreach ($this->headers->all() as $key => $value) { $key = strtoupper(str_replace('-', '_', $key)); if (in_array($key, array('CONTENT_TYPE', 'CONTENT_LENGTH'))) { $_SERVER[$key] = implode(', ', $value); } else { $_SERVER['HTTP_'.$key] = implode(', ', $value); } } $request = array('g' => $_GET, 'p' => $_POST, 'c' => $_COOKIE); $requestOrder = ini_get('request_order') ?: ini_get('variables_order'); $requestOrder = preg_replace('#[^cgp]#', '', strtolower($requestOrder)) ?: 'gp'; $_REQUEST = array(); foreach (str_split($requestOrder) as $order) { $_REQUEST = array_merge($_REQUEST, $request[$order]); } } public static function setTrustedProxies(array $proxies) { self::$trustedProxies = $proxies; if (2 > func_num_args()) { @trigger_error(sprintf('The %s() method expects a bit field of Request::HEADER_* as second argument since Symfony 3.3. Defining it will be required in 4.0. ', __METHOD__), E_USER_DEPRECATED); return; } $trustedHeaderSet = (int) func_get_arg(1); foreach (self::$trustedHeaderNames as $header => $name) { self::$trustedHeaders[$header] = $header & $trustedHeaderSet ? $name : null; } self::$trustedHeaderSet = $trustedHeaderSet; } public static function getTrustedProxies() { return self::$trustedProxies; } public static function getTrustedHeaderSet() { return self::$trustedHeaderSet; } public static function setTrustedHosts(array $hostPatterns) { self::$trustedHostPatterns = array_map(function ($hostPattern) { return sprintf('#%s#i', $hostPattern); }, $hostPatterns); self::$trustedHosts = array(); } public static function getTrustedHosts() { return self::$trustedHostPatterns; } public static function setTrustedHeaderName($key, $value) { @trigger_error(sprintf('The "%s()" method is deprecated since Symfony 3.3 and will be removed in 4.0. Use the $trustedHeaderSet argument of the Request::setTrustedProxies() method instead.', __METHOD__), E_USER_DEPRECATED); if ('forwarded' === $key) { $key = self::HEADER_FORWARDED; } elseif ('client_ip' === $key) { $key = self::HEADER_CLIENT_IP; } elseif ('client_host' === $key) { $key = self::HEADER_CLIENT_HOST; } elseif ('client_proto' === $key) { $key = self::HEADER_CLIENT_PROTO; } elseif ('client_port' === $key) { $key = self::HEADER_CLIENT_PORT; } elseif (!array_key_exists($key, self::$trustedHeaders)) { throw new \InvalidArgumentException(sprintf('Unable to set the trusted header name for key "%s".', $key)); } self::$trustedHeaders[$key] = $value; if (null !== $value) { self::$trustedHeaderNames[$key] = $value; self::$trustedHeaderSet |= $key; } else { self::$trustedHeaderSet &= ~$key; } } public static function getTrustedHeaderName($key) { if (2 > func_num_args() || func_get_arg(1)) { @trigger_error(sprintf('The "%s()" method is deprecated since Symfony 3.3 and will be removed in 4.0. Use the Request::getTrustedHeaderSet() method instead.', __METHOD__), E_USER_DEPRECATED); } if (!array_key_exists($key, self::$trustedHeaders)) { throw new \InvalidArgumentException(sprintf('Unable to get the trusted header name for key "%s".', $key)); } return self::$trustedHeaders[$key]; } public static function normalizeQueryString($qs) { if ('' == $qs) { return ''; } $parts = array(); $order = array(); foreach (explode('&', $qs) as $param) { if ('' === $param || '=' === $param[0]) { continue; } $keyValuePair = explode('=', $param, 2); $parts[] = isset($keyValuePair[1]) ? rawurlencode(urldecode($keyValuePair[0])).'='.rawurlencode(urldecode($keyValuePair[1])) : rawurlencode(urldecode($keyValuePair[0])); $order[] = urldecode($keyValuePair[0]); } array_multisort($order, SORT_ASC, $parts); return implode('&', $parts); } public static function enableHttpMethodParameterOverride() { self::$httpMethodParameterOverride = true; } public static function getHttpMethodParameterOverride() { return self::$httpMethodParameterOverride; } public function get($key, $default = null) { if ($this !== $result = $this->attributes->get($key, $this)) { return $result; } if ($this !== $result = $this->query->get($key, $this)) { return $result; } if ($this !== $result = $this->request->get($key, $this)) { return $result; } return $default; } public function getSession() { return $this->session; } public function hasPreviousSession() { return $this->hasSession() && $this->cookies->has($this->session->getName()); } public function hasSession() { return null !== $this->session; } public function setSession(SessionInterface $session) { $this->session = $session; } public function getClientIps() { $ip = $this->server->get('REMOTE_ADDR'); if (!$this->isFromTrustedProxy()) { return array($ip); } return $this->getTrustedValues(self::HEADER_CLIENT_IP, $ip) ?: array($ip); } public function getClientIp() { $ipAddresses = $this->getClientIps(); return $ipAddresses[0]; } public function getScriptName() { return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', '')); } public function getPathInfo() { if (null === $this->pathInfo) { $this->pathInfo = $this->preparePathInfo(); } return $this->pathInfo; } public function getBasePath() { if (null === $this->basePath) { $this->basePath = $this->prepareBasePath(); } return $this->basePath; } public function getBaseUrl() { if (null === $this->baseUrl) { $this->baseUrl = $this->prepareBaseUrl(); } return $this->baseUrl; } public function getScheme() { return $this->isSecure() ? 'https' : 'http'; } public function getPort() { if ($this->isFromTrustedProxy() && $host = $this->getTrustedValues(self::HEADER_CLIENT_PORT)) { $host = $host[0]; } elseif ($this->isFromTrustedProxy() && $host = $this->getTrustedValues(self::HEADER_CLIENT_HOST)) { $host = $host[0]; } elseif (!$host = $this->headers->get('HOST')) { return $this->server->get('SERVER_PORT'); } if ('[' === $host[0]) { $pos = strpos($host, ':', strrpos($host, ']')); } else { $pos = strrpos($host, ':'); } if (false !== $pos) { return (int) substr($host, $pos + 1); } return 'https' === $this->getScheme() ? 443 : 80; } public function getUser() { return $this->headers->get('PHP_AUTH_USER'); } public function getPassword() { return $this->headers->get('PHP_AUTH_PW'); } public function getUserInfo() { $userinfo = $this->getUser(); $pass = $this->getPassword(); if ('' != $pass) { $userinfo .= ":$pass"; } return $userinfo; } public function getHttpHost() { $scheme = $this->getScheme(); $port = $this->getPort(); if (('http' == $scheme && 80 == $port) || ('https' == $scheme && 443 == $port)) { return $this->getHost(); } return $this->getHost().':'.$port; } public function getRequestUri() { if (null === $this->requestUri) { $this->requestUri = $this->prepareRequestUri(); } return $this->requestUri; } public function getSchemeAndHttpHost() { return $this->getScheme().'://'.$this->getHttpHost(); } public function getUri() { if (null !== $qs = $this->getQueryString()) { $qs = '?'.$qs; } return $this->getSchemeAndHttpHost().$this->getBaseUrl().$this->getPathInfo().$qs; } public function getUriForPath($path) { return $this->getSchemeAndHttpHost().$this->getBaseUrl().$path; } public function getRelativeUriForPath($path) { if (!isset($path[0]) || '/' !== $path[0]) { return $path; } if ($path === $basePath = $this->getPathInfo()) { return ''; } $sourceDirs = explode('/', isset($basePath[0]) && '/' === $basePath[0] ? substr($basePath, 1) : $basePath); $targetDirs = explode('/', isset($path[0]) && '/' === $path[0] ? substr($path, 1) : $path); array_pop($sourceDirs); $targetFile = array_pop($targetDirs); foreach ($sourceDirs as $i => $dir) { if (isset($targetDirs[$i]) && $dir === $targetDirs[$i]) { unset($sourceDirs[$i], $targetDirs[$i]); } else { break; } } $targetDirs[] = $targetFile; $path = str_repeat('../', count($sourceDirs)).implode('/', $targetDirs); return !isset($path[0]) || '/' === $path[0] || false !== ($colonPos = strpos($path, ':')) && ($colonPos < ($slashPos = strpos($path, '/')) || false === $slashPos) ? "./$path" : $path; } public function getQueryString() { $qs = static::normalizeQueryString($this->server->get('QUERY_STRING')); return '' === $qs ? null : $qs; } public function isSecure() { if ($this->isFromTrustedProxy() && $proto = $this->getTrustedValues(self::HEADER_CLIENT_PROTO)) { return in_array(strtolower($proto[0]), array('https', 'on', 'ssl', '1'), true); } $https = $this->server->get('HTTPS'); return !empty($https) && 'off' !== strtolower($https); } public function getHost() { if ($this->isFromTrustedProxy() && $host = $this->getTrustedValues(self::HEADER_CLIENT_HOST)) { $host = $host[0]; } elseif (!$host = $this->headers->get('HOST')) { if (!$host = $this->server->get('SERVER_NAME')) { $host = $this->server->get('SERVER_ADDR', ''); } } $host = strtolower(preg_replace('/:\d+$/', '', trim($host))); if ($host && '' !== preg_replace('/(?:^\[)?[a-zA-Z0-9-:\]_]+\.?/', '', $host)) { if (!$this->isHostValid) { return ''; } $this->isHostValid = false; throw new SuspiciousOperationException(sprintf('Invalid Host "%s".', $host)); } if (count(self::$trustedHostPatterns) > 0) { if (in_array($host, self::$trustedHosts)) { return $host; } foreach (self::$trustedHostPatterns as $pattern) { if (preg_match($pattern, $host)) { self::$trustedHosts[] = $host; return $host; } } if (!$this->isHostValid) { return ''; } $this->isHostValid = false; throw new SuspiciousOperationException(sprintf('Untrusted Host "%s".', $host)); } return $host; } public function setMethod($method) { $this->method = null; $this->server->set('REQUEST_METHOD', $method); } public function getMethod() { if (null === $this->method) { $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET')); if ('POST' === $this->method) { if ($method = $this->headers->get('X-HTTP-METHOD-OVERRIDE')) { $this->method = strtoupper($method); } elseif (self::$httpMethodParameterOverride) { $this->method = strtoupper($this->request->get('_method', $this->query->get('_method', 'POST'))); } } } return $this->method; } public function getRealMethod() { return strtoupper($this->server->get('REQUEST_METHOD', 'GET')); } public function getMimeType($format) { if (null === static::$formats) { static::initializeFormats(); } return isset(static::$formats[$format]) ? static::$formats[$format][0] : null; } public static function getMimeTypes($format) { if (null === static::$formats) { static::initializeFormats(); } return isset(static::$formats[$format]) ? static::$formats[$format] : array(); } public function getFormat($mimeType) { $canonicalMimeType = null; if (false !== $pos = strpos($mimeType, ';')) { $canonicalMimeType = substr($mimeType, 0, $pos); } if (null === static::$formats) { static::initializeFormats(); } foreach (static::$formats as $format => $mimeTypes) { if (in_array($mimeType, (array) $mimeTypes)) { return $format; } if (null !== $canonicalMimeType && in_array($canonicalMimeType, (array) $mimeTypes)) { return $format; } } } public function setFormat($format, $mimeTypes) { if (null === static::$formats) { static::initializeFormats(); } static::$formats[$format] = is_array($mimeTypes) ? $mimeTypes : array($mimeTypes); } public function getRequestFormat($default = 'html') { if (null === $this->format) { $this->format = $this->attributes->get('_format'); } return null === $this->format ? $default : $this->format; } public function setRequestFormat($format) { $this->format = $format; } public function getContentType() { return $this->getFormat($this->headers->get('CONTENT_TYPE')); } public function setDefaultLocale($locale) { $this->defaultLocale = $locale; if (null === $this->locale) { $this->setPhpDefaultLocale($locale); } } public function getDefaultLocale() { return $this->defaultLocale; } public function setLocale($locale) { $this->setPhpDefaultLocale($this->locale = $locale); } public function getLocale() { return null === $this->locale ? $this->defaultLocale : $this->locale; } public function isMethod($method) { return $this->getMethod() === strtoupper($method); } public function isMethodSafe() { if (!func_num_args() || func_get_arg(0)) { @trigger_error('Checking only for cacheable HTTP methods with Symfony\Component\HttpFoundation\Request::isMethodSafe() is deprecated since Symfony 3.2 and will throw an exception in 4.0. Disable checking only for cacheable methods by calling the method with `false` as first argument or use the Request::isMethodCacheable() instead.', E_USER_DEPRECATED); return in_array($this->getMethod(), array('GET', 'HEAD')); } return in_array($this->getMethod(), array('GET', 'HEAD', 'OPTIONS', 'TRACE')); } public function isMethodIdempotent() { return in_array($this->getMethod(), array('HEAD', 'GET', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'PURGE')); } public function isMethodCacheable() { return in_array($this->getMethod(), array('GET', 'HEAD')); } public function getProtocolVersion() { if ($this->isFromTrustedProxy()) { preg_match('~^(HTTP/)?([1-9]\.[0-9]) ~', $this->headers->get('Via'), $matches); if ($matches) { return 'HTTP/'.$matches[2]; } } return $this->server->get('SERVER_PROTOCOL'); } public function getContent($asResource = false) { $currentContentIsResource = is_resource($this->content); if (\PHP_VERSION_ID < 50600 && false === $this->content) { throw new \LogicException('getContent() can only be called once when using the resource return type and PHP below 5.6.'); } if (true === $asResource) { if ($currentContentIsResource) { rewind($this->content); return $this->content; } if (is_string($this->content)) { $resource = fopen('php://temp', 'r+'); fwrite($resource, $this->content); rewind($resource); return $resource; } $this->content = false; return fopen('php://input', 'rb'); } if ($currentContentIsResource) { rewind($this->content); return stream_get_contents($this->content); } if (null === $this->content || false === $this->content) { $this->content = file_get_contents('php://input'); } return $this->content; } public function getETags() { return preg_split('/\s*,\s*/', $this->headers->get('if_none_match'), null, PREG_SPLIT_NO_EMPTY); } public function isNoCache() { return $this->headers->hasCacheControlDirective('no-cache') || 'no-cache' == $this->headers->get('Pragma'); } public function getPreferredLanguage(array $locales = null) { $preferredLanguages = $this->getLanguages(); if (empty($locales)) { return isset($preferredLanguages[0]) ? $preferredLanguages[0] : null; } if (!$preferredLanguages) { return $locales[0]; } $extendedPreferredLanguages = array(); foreach ($preferredLanguages as $language) { $extendedPreferredLanguages[] = $language; if (false !== $position = strpos($language, '_')) { $superLanguage = substr($language, 0, $position); if (!in_array($superLanguage, $preferredLanguages)) { $extendedPreferredLanguages[] = $superLanguage; } } } $preferredLanguages = array_values(array_intersect($extendedPreferredLanguages, $locales)); return isset($preferredLanguages[0]) ? $preferredLanguages[0] : $locales[0]; } public function getLanguages() { if (null !== $this->languages) { return $this->languages; } $languages = AcceptHeader::fromString($this->headers->get('Accept-Language'))->all(); $this->languages = array(); foreach ($languages as $lang => $acceptHeaderItem) { if (false !== strpos($lang, '-')) { $codes = explode('-', $lang); if ('i' === $codes[0]) { if (count($codes) > 1) { $lang = $codes[1]; } } else { for ($i = 0, $max = count($codes); $i < $max; ++$i) { if (0 === $i) { $lang = strtolower($codes[0]); } else { $lang .= '_'.strtoupper($codes[$i]); } } } } $this->languages[] = $lang; } return $this->languages; } public function getCharsets() { if (null !== $this->charsets) { return $this->charsets; } return $this->charsets = array_keys(AcceptHeader::fromString($this->headers->get('Accept-Charset'))->all()); } public function getEncodings() { if (null !== $this->encodings) { return $this->encodings; } return $this->encodings = array_keys(AcceptHeader::fromString($this->headers->get('Accept-Encoding'))->all()); } public function getAcceptableContentTypes() { if (null !== $this->acceptableContentTypes) { return $this->acceptableContentTypes; } return $this->acceptableContentTypes = array_keys(AcceptHeader::fromString($this->headers->get('Accept'))->all()); } public function isXmlHttpRequest() { return 'XMLHttpRequest' == $this->headers->get('X-Requested-With'); } protected function prepareRequestUri() { $requestUri = ''; if ($this->headers->has('X_ORIGINAL_URL')) { $requestUri = $this->headers->get('X_ORIGINAL_URL'); $this->headers->remove('X_ORIGINAL_URL'); $this->server->remove('HTTP_X_ORIGINAL_URL'); $this->server->remove('UNENCODED_URL'); $this->server->remove('IIS_WasUrlRewritten'); } elseif ($this->headers->has('X_REWRITE_URL')) { $requestUri = $this->headers->get('X_REWRITE_URL'); $this->headers->remove('X_REWRITE_URL'); } elseif ('1' == $this->server->get('IIS_WasUrlRewritten') && '' != $this->server->get('UNENCODED_URL')) { $requestUri = $this->server->get('UNENCODED_URL'); $this->server->remove('UNENCODED_URL'); $this->server->remove('IIS_WasUrlRewritten'); } elseif ($this->server->has('REQUEST_URI')) { $requestUri = $this->server->get('REQUEST_URI'); $schemeAndHttpHost = $this->getSchemeAndHttpHost(); if (0 === strpos($requestUri, $schemeAndHttpHost)) { $requestUri = substr($requestUri, strlen($schemeAndHttpHost)); } } elseif ($this->server->has('ORIG_PATH_INFO')) { $requestUri = $this->server->get('ORIG_PATH_INFO'); if ('' != $this->server->get('QUERY_STRING')) { $requestUri .= '?'.$this->server->get('QUERY_STRING'); } $this->server->remove('ORIG_PATH_INFO'); } $this->server->set('REQUEST_URI', $requestUri); return $requestUri; } protected function prepareBaseUrl() { $filename = basename($this->server->get('SCRIPT_FILENAME')); if (basename($this->server->get('SCRIPT_NAME')) === $filename) { $baseUrl = $this->server->get('SCRIPT_NAME'); } elseif (basename($this->server->get('PHP_SELF')) === $filename) { $baseUrl = $this->server->get('PHP_SELF'); } elseif (basename($this->server->get('ORIG_SCRIPT_NAME')) === $filename) { $baseUrl = $this->server->get('ORIG_SCRIPT_NAME'); } else { $path = $this->server->get('PHP_SELF', ''); $file = $this->server->get('SCRIPT_FILENAME', ''); $segs = explode('/', trim($file, '/')); $segs = array_reverse($segs); $index = 0; $last = count($segs); $baseUrl = ''; do { $seg = $segs[$index]; $baseUrl = '/'.$seg.$baseUrl; ++$index; } while ($last > $index && (false !== $pos = strpos($path, $baseUrl)) && 0 != $pos); } $requestUri = $this->getRequestUri(); if ('' !== $requestUri && '/' !== $requestUri[0]) { $requestUri = '/'.$requestUri; } if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl)) { return $prefix; } if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, rtrim(dirname($baseUrl), '/'.DIRECTORY_SEPARATOR).'/')) { return rtrim($prefix, '/'.DIRECTORY_SEPARATOR); } $truncatedRequestUri = $requestUri; if (false !== $pos = strpos($requestUri, '?')) { $truncatedRequestUri = substr($requestUri, 0, $pos); } $basename = basename($baseUrl); if (empty($basename) || !strpos(rawurldecode($truncatedRequestUri), $basename)) { return ''; } if (strlen($requestUri) >= strlen($baseUrl) && (false !== $pos = strpos($requestUri, $baseUrl)) && 0 !== $pos) { $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl)); } return rtrim($baseUrl, '/'.DIRECTORY_SEPARATOR); } protected function prepareBasePath() { $baseUrl = $this->getBaseUrl(); if (empty($baseUrl)) { return ''; } $filename = basename($this->server->get('SCRIPT_FILENAME')); if (basename($baseUrl) === $filename) { $basePath = dirname($baseUrl); } else { $basePath = $baseUrl; } if ('\\' === DIRECTORY_SEPARATOR) { $basePath = str_replace('\\', '/', $basePath); } return rtrim($basePath, '/'); } protected function preparePathInfo() { if (null === ($requestUri = $this->getRequestUri())) { return '/'; } if (false !== $pos = strpos($requestUri, '?')) { $requestUri = substr($requestUri, 0, $pos); } if ('' !== $requestUri && '/' !== $requestUri[0]) { $requestUri = '/'.$requestUri; } if (null === ($baseUrl = $this->getBaseUrl())) { return $requestUri; } $pathInfo = substr($requestUri, strlen($baseUrl)); if (false === $pathInfo || '' === $pathInfo) { return '/'; } return (string) $pathInfo; } protected static function initializeFormats() { static::$formats = array( 'html' => array('text/html', 'application/xhtml+xml'), 'txt' => array('text/plain'), 'js' => array('application/javascript', 'application/x-javascript', 'text/javascript'), 'css' => array('text/css'), 'json' => array('application/json', 'application/x-json'), 'jsonld' => array('application/ld+json'), 'xml' => array('text/xml', 'application/xml', 'application/x-xml'), 'rdf' => array('application/rdf+xml'), 'atom' => array('application/atom+xml'), 'rss' => array('application/rss+xml'), 'form' => array('application/x-www-form-urlencoded'), ); } private function setPhpDefaultLocale($locale) { try { if (class_exists('Locale', false)) { \Locale::setDefault($locale); } } catch (\Exception $e) { } } private function getUrlencodedPrefix($string, $prefix) { if (0 !== strpos(rawurldecode($string), $prefix)) { return false; } $len = strlen($prefix); if (preg_match(sprintf('#^(%%[[:xdigit:]]{2}|.){%d}#', $len), $string, $match)) { return $match[0]; } return false; } private static function createRequestFromFactory(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null) { if (self::$requestFactory) { $request = call_user_func(self::$requestFactory, $query, $request, $attributes, $cookies, $files, $server, $content); if (!$request instanceof self) { throw new \LogicException('The Request factory must return an instance of Symfony\Component\HttpFoundation\Request.'); } return $request; } return new static($query, $request, $attributes, $cookies, $files, $server, $content); } public function isFromTrustedProxy() { return self::$trustedProxies && IpUtils::checkIp($this->server->get('REMOTE_ADDR'), self::$trustedProxies); } private function getTrustedValues($type, $ip = null) { $clientValues = array(); $forwardedValues = array(); if (self::$trustedHeaders[$type] && $this->headers->has(self::$trustedHeaders[$type])) { foreach (explode(',', $this->headers->get(self::$trustedHeaders[$type])) as $v) { $clientValues[] = (self::HEADER_CLIENT_PORT === $type ? '0.0.0.0:' : '').trim($v); } } if (self::$trustedHeaders[self::HEADER_FORWARDED] && $this->headers->has(self::$trustedHeaders[self::HEADER_FORWARDED])) { $forwardedValues = $this->headers->get(self::$trustedHeaders[self::HEADER_FORWARDED]); $forwardedValues = preg_match_all(sprintf('{(?:%s)=(?:"?\[?)([a-zA-Z0-9\.:_\-/]*+)}', self::$forwardedParams[$type]), $forwardedValues, $matches) ? $matches[1] : array(); } if (null !== $ip) { $clientValues = $this->normalizeAndFilterClientIps($clientValues, $ip); $forwardedValues = $this->normalizeAndFilterClientIps($forwardedValues, $ip); } if ($forwardedValues === $clientValues || !$clientValues) { return $forwardedValues; } if (!$forwardedValues) { return $clientValues; } if (!$this->isForwardedValid) { return null !== $ip ? array('0.0.0.0', $ip) : array(); } $this->isForwardedValid = false; throw new ConflictingHeadersException(sprintf('The request has both a trusted "%s" header and a trusted "%s" header, conflicting with each other. You should either configure your proxy to remove one of them, or configure your project to distrust the offending one.', self::$trustedHeaders[self::HEADER_FORWARDED], self::$trustedHeaders[$type])); } private function normalizeAndFilterClientIps(array $clientIps, $ip) { if (!$clientIps) { return array(); } $clientIps[] = $ip; $firstTrustedIp = null; foreach ($clientIps as $key => $clientIp) { if (preg_match('{((?:\d+\.){3}\d+)\:\d+}', $clientIp, $match)) { $clientIps[$key] = $clientIp = $match[1]; } if (!filter_var($clientIp, FILTER_VALIDATE_IP)) { unset($clientIps[$key]); continue; } if (IpUtils::checkIp($clientIp, self::$trustedProxies)) { unset($clientIps[$key]); if (null === $firstTrustedIp) { $firstTrustedIp = $clientIp; } } } return $clientIps ? array_reverse($clientIps) : array($firstTrustedIp); } } 