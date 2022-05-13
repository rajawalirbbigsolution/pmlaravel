<?php
 namespace Symfony\Component\Process\Tests; use PHPUnit\Framework\TestCase; use Symfony\Component\Process\PhpExecutableFinder; class PhpExecutableFinderTest extends TestCase { public function testFind() { if (defined('HHVM_VERSION')) { $this->markTestSkipped('Should not be executed in HHVM context.'); } $f = new PhpExecutableFinder(); $current = PHP_BINARY; $args = 'phpdbg' === PHP_SAPI ? ' -qrr' : ''; $this->assertEquals($current.$args, $f->find(), '::find() returns the executable PHP'); $this->assertEquals($current, $f->find(false), '::find() returns the executable PHP'); } public function testFindWithHHVM() { if (!defined('HHVM_VERSION')) { $this->markTestSkipped('Should be executed in HHVM context.'); } $f = new PhpExecutableFinder(); $current = getenv('PHP_BINARY') ?: PHP_BINARY; $this->assertEquals($current.' --php', $f->find(), '::find() returns the executable PHP'); $this->assertEquals($current, $f->find(false), '::find() returns the executable PHP'); } public function testFindArguments() { $f = new PhpExecutableFinder(); if (defined('HHVM_VERSION')) { $this->assertEquals($f->findArguments(), array('--php'), '::findArguments() returns HHVM arguments'); } elseif ('phpdbg' === PHP_SAPI) { $this->assertEquals($f->findArguments(), array('-qrr'), '::findArguments() returns phpdbg arguments'); } else { $this->assertEquals($f->findArguments(), array(), '::findArguments() returns no arguments'); } } } 