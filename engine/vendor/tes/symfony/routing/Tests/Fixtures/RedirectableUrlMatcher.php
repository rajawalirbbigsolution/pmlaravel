<?php
 namespace Symfony\Component\Routing\Tests\Fixtures; use Symfony\Component\Routing\Matcher\UrlMatcher; use Symfony\Component\Routing\Matcher\RedirectableUrlMatcherInterface; class RedirectableUrlMatcher extends UrlMatcher implements RedirectableUrlMatcherInterface { public function redirect($path, $route, $scheme = null) { return array( '_controller' => 'Some controller reference...', 'path' => $path, 'scheme' => $scheme, ); } } 