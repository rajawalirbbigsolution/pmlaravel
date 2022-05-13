<?php
 namespace Symfony\Component\HttpKernel\DataCollector; use Symfony\Component\HttpFoundation\Request; use Symfony\Component\HttpFoundation\Response; use Symfony\Component\EventDispatcher\EventDispatcherInterface; use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface; class EventDataCollector extends DataCollector implements LateDataCollectorInterface { protected $dispatcher; public function __construct(EventDispatcherInterface $dispatcher = null) { if ($dispatcher instanceof TraceableEventDispatcherInterface && !method_exists($dispatcher, 'reset')) { @trigger_error(sprintf('Implementing "%s" without the "reset()" method is deprecated since Symfony 3.4 and will be unsupported in 4.0 for class "%s".', TraceableEventDispatcherInterface::class, \get_class($dispatcher)), E_USER_DEPRECATED); } $this->dispatcher = $dispatcher; } public function collect(Request $request, Response $response, \Exception $exception = null) { $this->data = array( 'called_listeners' => array(), 'not_called_listeners' => array(), ); } public function reset() { $this->data = array(); if ($this->dispatcher instanceof TraceableEventDispatcherInterface) { if (!method_exists($this->dispatcher, 'reset')) { return; } $this->dispatcher->reset(); } } public function lateCollect() { if ($this->dispatcher instanceof TraceableEventDispatcherInterface) { $this->setCalledListeners($this->dispatcher->getCalledListeners()); $this->setNotCalledListeners($this->dispatcher->getNotCalledListeners()); } $this->data = $this->cloneVar($this->data); } public function setCalledListeners(array $listeners) { $this->data['called_listeners'] = $listeners; } public function getCalledListeners() { return $this->data['called_listeners']; } public function setNotCalledListeners(array $listeners) { $this->data['not_called_listeners'] = $listeners; } public function getNotCalledListeners() { return $this->data['not_called_listeners']; } public function getName() { return 'events'; } } 