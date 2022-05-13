<?php
 namespace Symfony\Component\HttpKernel\EventListener; use Psr\Log\LoggerInterface; use Symfony\Component\HttpFoundation\Response; use Symfony\Component\HttpKernel\Event\GetResponseEvent; use Symfony\Component\HttpKernel\Event\FinishRequestEvent; use Symfony\Component\HttpKernel\Kernel; use Symfony\Component\HttpKernel\KernelEvents; use Symfony\Component\HttpKernel\Exception\BadRequestHttpException; use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException; use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; use Symfony\Component\HttpFoundation\RequestStack; use Symfony\Component\Routing\Exception\MethodNotAllowedException; use Symfony\Component\Routing\Exception\NoConfigurationException; use Symfony\Component\Routing\Exception\ResourceNotFoundException; use Symfony\Component\Routing\Matcher\UrlMatcherInterface; use Symfony\Component\Routing\Matcher\RequestMatcherInterface; use Symfony\Component\Routing\RequestContext; use Symfony\Component\Routing\RequestContextAwareInterface; use Symfony\Component\EventDispatcher\EventSubscriberInterface; use Symfony\Component\HttpFoundation\Request; class RouterListener implements EventSubscriberInterface { private $matcher; private $context; private $logger; private $requestStack; private $projectDir; private $debug; public function __construct($matcher, RequestStack $requestStack, RequestContext $context = null, LoggerInterface $logger = null, $projectDir = null, $debug = true) { if (!$matcher instanceof UrlMatcherInterface && !$matcher instanceof RequestMatcherInterface) { throw new \InvalidArgumentException('Matcher must either implement UrlMatcherInterface or RequestMatcherInterface.'); } if (null === $context && !$matcher instanceof RequestContextAwareInterface) { throw new \InvalidArgumentException('You must either pass a RequestContext or the matcher must implement RequestContextAwareInterface.'); } $this->matcher = $matcher; $this->context = $context ?: $matcher->getContext(); $this->requestStack = $requestStack; $this->logger = $logger; $this->projectDir = $projectDir; $this->debug = $debug; } private function setCurrentRequest(Request $request = null) { if (null !== $request) { try { $this->context->fromRequest($request); } catch (\UnexpectedValueException $e) { throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode()); } } } public function onKernelFinishRequest(FinishRequestEvent $event) { $this->setCurrentRequest($this->requestStack->getParentRequest()); } public function onKernelRequest(GetResponseEvent $event) { $request = $event->getRequest(); $this->setCurrentRequest($request); if ($request->attributes->has('_controller')) { return; } try { if ($this->matcher instanceof RequestMatcherInterface) { $parameters = $this->matcher->matchRequest($request); } else { $parameters = $this->matcher->match($request->getPathInfo()); } if (null !== $this->logger) { $this->logger->info('Matched route "{route}".', array( 'route' => isset($parameters['_route']) ? $parameters['_route'] : 'n/a', 'route_parameters' => $parameters, 'request_uri' => $request->getUri(), 'method' => $request->getMethod(), )); } $request->attributes->add($parameters); unset($parameters['_route'], $parameters['_controller']); $request->attributes->set('_route_params', $parameters); } catch (ResourceNotFoundException $e) { if ($this->debug && $e instanceof NoConfigurationException) { $event->setResponse($this->createWelcomeResponse()); return; } $message = sprintf('No route found for "%s %s"', $request->getMethod(), $request->getPathInfo()); if ($referer = $request->headers->get('referer')) { $message .= sprintf(' (from "%s")', $referer); } throw new NotFoundHttpException($message, $e); } catch (MethodNotAllowedException $e) { $message = sprintf('No route found for "%s %s": Method Not Allowed (Allow: %s)', $request->getMethod(), $request->getPathInfo(), implode(', ', $e->getAllowedMethods())); throw new MethodNotAllowedHttpException($e->getAllowedMethods(), $message, $e); } } public static function getSubscribedEvents() { return array( KernelEvents::REQUEST => array(array('onKernelRequest', 32)), KernelEvents::FINISH_REQUEST => array(array('onKernelFinishRequest', 0)), ); } private function createWelcomeResponse() { $version = Kernel::VERSION; $baseDir = realpath($this->projectDir).DIRECTORY_SEPARATOR; $docVersion = substr(Kernel::VERSION, 0, 3); ob_start(); include __DIR__.'/../Resources/welcome.html.php'; return new Response(ob_get_clean(), Response::HTTP_NOT_FOUND); } } 