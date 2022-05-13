<?php
 namespace Symfony\Component\HttpKernel\Controller; use Psr\Container\ContainerInterface; use Psr\Log\LoggerInterface; use Symfony\Component\DependencyInjection\Container; use Symfony\Component\HttpFoundation\Request; class ContainerControllerResolver extends ControllerResolver { protected $container; public function __construct(ContainerInterface $container, LoggerInterface $logger = null) { $this->container = $container; parent::__construct($logger); } public function getController(Request $request) { $controller = parent::getController($request); if (is_array($controller) && isset($controller[0]) && is_string($controller[0]) && $this->container->has($controller[0])) { $controller[0] = $this->instantiateController($controller[0]); } return $controller; } protected function createController($controller) { if (false !== strpos($controller, '::')) { return parent::createController($controller); } $method = null; if (1 == substr_count($controller, ':')) { list($controller, $method) = explode(':', $controller, 2); } if (!$this->container->has($controller)) { $this->throwExceptionIfControllerWasRemoved($controller); throw new \LogicException(sprintf('Controller not found: service "%s" does not exist.', $controller)); } $service = $this->container->get($controller); if (null !== $method) { return array($service, $method); } if (!method_exists($service, '__invoke')) { throw new \LogicException(sprintf('Controller "%s" cannot be called without a method name. Did you forget an "__invoke" method?', $controller)); } return $service; } protected function instantiateController($class) { if ($this->container->has($class)) { return $this->container->get($class); } try { return parent::instantiateController($class); } catch (\ArgumentCountError $e) { } catch (\ErrorException $e) { } catch (\TypeError $e) { } $this->throwExceptionIfControllerWasRemoved($class, $e); throw $e; } private function throwExceptionIfControllerWasRemoved($controller, $previous = null) { if ($this->container instanceof Container && isset($this->container->getRemovedIds()[$controller])) { throw new \LogicException(sprintf('Controller "%s" cannot be fetched from the container because it is private. Did you forget to tag the service with "controller.service_arguments"?', $controller), 0, $previous); } } } 