<?php
 namespace Illuminate\Console; use Closure; use Illuminate\Support\ProcessUtils; use Illuminate\Contracts\Events\Dispatcher; use Illuminate\Contracts\Container\Container; use Symfony\Component\Console\Input\ArgvInput; use Symfony\Component\Console\Input\ArrayInput; use Symfony\Component\Console\Input\InputOption; use Symfony\Component\Process\PhpExecutableFinder; use Symfony\Component\Console\Input\InputInterface; use Symfony\Component\Console\Output\ConsoleOutput; use Symfony\Component\Console\Output\BufferedOutput; use Symfony\Component\Console\Output\OutputInterface; use Symfony\Component\Console\Application as SymfonyApplication; use Symfony\Component\Console\Command\Command as SymfonyCommand; use Illuminate\Contracts\Console\Application as ApplicationContract; class Application extends SymfonyApplication implements ApplicationContract { protected $laravel; protected $lastOutput; protected static $bootstrappers = []; protected $events; public function __construct(Container $laravel, Dispatcher $events, $version) { parent::__construct('Laravel Framework', $version); $this->laravel = $laravel; $this->events = $events; $this->setAutoExit(false); $this->setCatchExceptions(false); $this->events->dispatch(new Events\ArtisanStarting($this)); $this->bootstrap(); } public function run(InputInterface $input = null, OutputInterface $output = null) { $commandName = $this->getCommandName( $input = $input ?: new ArgvInput ); $this->events->fire( new Events\CommandStarting( $commandName, $input, $output = $output ?: new ConsoleOutput ) ); $exitCode = parent::run($input, $output); $this->events->fire( new Events\CommandFinished($commandName, $input, $output, $exitCode) ); return $exitCode; } public static function phpBinary() { return ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false)); } public static function artisanBinary() { return defined('ARTISAN_BINARY') ? ProcessUtils::escapeArgument(ARTISAN_BINARY) : 'artisan'; } public static function formatCommandString($string) { return sprintf('%s %s %s', static::phpBinary(), static::artisanBinary(), $string); } public static function starting(Closure $callback) { static::$bootstrappers[] = $callback; } protected function bootstrap() { foreach (static::$bootstrappers as $bootstrapper) { $bootstrapper($this); } } public static function forgetBootstrappers() { static::$bootstrappers = []; } public function call($command, array $parameters = [], $outputBuffer = null) { $parameters = collect($parameters)->prepend($command); $this->lastOutput = $outputBuffer ?: new BufferedOutput; $this->setCatchExceptions(false); $result = $this->run(new ArrayInput($parameters->toArray()), $this->lastOutput); $this->setCatchExceptions(true); return $result; } public function output() { return $this->lastOutput ? $this->lastOutput->fetch() : ''; } public function add(SymfonyCommand $command) { if ($command instanceof Command) { $command->setLaravel($this->laravel); } return $this->addToParent($command); } protected function addToParent(SymfonyCommand $command) { return parent::add($command); } public function resolve($command) { return $this->add($this->laravel->make($command)); } public function resolveCommands($commands) { $commands = is_array($commands) ? $commands : func_get_args(); foreach ($commands as $command) { $this->resolve($command); } return $this; } protected function getDefaultInputDefinition() { return tap(parent::getDefaultInputDefinition(), function ($definition) { $definition->addOption($this->getEnvironmentOption()); }); } protected function getEnvironmentOption() { $message = 'The environment the command should run under'; return new InputOption('--env', null, InputOption::VALUE_OPTIONAL, $message); } public function getLaravel() { return $this->laravel; } } 