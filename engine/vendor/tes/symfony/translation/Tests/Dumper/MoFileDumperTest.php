<?php
 namespace Symfony\Component\Translation\Tests\Dumper; use PHPUnit\Framework\TestCase; use Symfony\Component\Translation\MessageCatalogue; use Symfony\Component\Translation\Dumper\MoFileDumper; class MoFileDumperTest extends TestCase { public function testFormatCatalogue() { $catalogue = new MessageCatalogue('en'); $catalogue->add(array('foo' => 'bar')); $dumper = new MoFileDumper(); $this->assertStringEqualsFile(__DIR__.'/../fixtures/resources.mo', $dumper->formatCatalogue($catalogue, 'messages')); } } 