<?php
 namespace Symfony\Component\Translation\Tests\Dumper; use PHPUnit\Framework\TestCase; use Symfony\Component\Translation\MessageCatalogue; use Symfony\Component\Translation\Dumper\CsvFileDumper; class CsvFileDumperTest extends TestCase { public function testFormatCatalogue() { $catalogue = new MessageCatalogue('en'); $catalogue->add(array('foo' => 'bar', 'bar' => 'foo
foo', 'foo;foo' => 'bar')); $dumper = new CsvFileDumper(); $this->assertStringEqualsFile(__DIR__.'/../fixtures/valid.csv', $dumper->formatCatalogue($catalogue, 'messages')); } } 