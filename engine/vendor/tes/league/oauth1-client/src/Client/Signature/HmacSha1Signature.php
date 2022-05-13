<?php
 namespace League\OAuth1\Client\Signature; use GuzzleHttp\Psr7; use GuzzleHttp\Psr7\Uri; class HmacSha1Signature extends Signature implements SignatureInterface { public function method() { return 'HMAC-SHA1'; } public function sign($uri, array $parameters = array(), $method = 'POST') { $url = $this->createUrl($uri); $baseString = $this->baseString($url, $method, $parameters); return base64_encode($this->hash($baseString)); } protected function createUrl($uri) { return Psr7\uri_for($uri); } protected function baseString(Uri $url, $method = 'POST', array $parameters = array()) { $baseString = rawurlencode($method).'&'; $schemeHostPath = Uri::fromParts(array( 'scheme' => $url->getScheme(), 'host' => $url->getHost(), 'path' => $url->getPath(), )); $baseString .= rawurlencode($schemeHostPath).'&'; $data = array(); parse_str($url->getQuery(), $query); $data = array_merge($query, $parameters); array_walk_recursive($data, function (&$key, &$value) { $key = rawurlencode(rawurldecode($key)); $value = rawurlencode(rawurldecode($value)); }); ksort($data); $baseString .= $this->queryStringFromData($data); return $baseString; } protected function queryStringFromData($data, $queryParams = false, $prevKey = '') { if ($initial = (false === $queryParams)) { $queryParams = array(); } foreach ($data as $key => $value) { if ($prevKey) { $key = $prevKey.'['.$key.']'; } if (is_array($value)) { $queryParams = $this->queryStringFromData($value, $queryParams, $key); } else { $queryParams[] = rawurlencode($key.'='.$value); } } if ($initial) { return implode('%26', $queryParams); } return $queryParams; } protected function hash($string) { return hash_hmac('sha1', $string, $this->key(), true); } } 