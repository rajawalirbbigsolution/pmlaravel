<?php
 namespace Symfony\Component\HttpKernel\Tests\Exception; use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException; class UnprocessableEntityHttpExceptionTest extends HttpExceptionTest { public function testHeadersSetter($headers) { $exception = new UnprocessableEntityHttpException(10); $exception->setHeaders($headers); $this->assertSame($headers, $exception->getHeaders()); } protected function createException() { return new UnprocessableEntityHttpException(); } } 