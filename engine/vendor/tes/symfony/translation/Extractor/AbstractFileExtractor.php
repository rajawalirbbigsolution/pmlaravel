<?php
 namespace Symfony\Component\Translation\Extractor; use Symfony\Component\Translation\Exception\InvalidArgumentException; abstract class AbstractFileExtractor { protected function extractFiles($resource) { if (is_array($resource) || $resource instanceof \Traversable) { $files = array(); foreach ($resource as $file) { if ($this->canBeExtracted($file)) { $files[] = $this->toSplFileInfo($file); } } } elseif (is_file($resource)) { $files = $this->canBeExtracted($resource) ? array($this->toSplFileInfo($resource)) : array(); } else { $files = $this->extractFromDirectory($resource); } return $files; } private function toSplFileInfo($file) { return ($file instanceof \SplFileInfo) ? $file : new \SplFileInfo($file); } protected function isFile($file) { if (!is_file($file)) { throw new InvalidArgumentException(sprintf('The "%s" file does not exist.', $file)); } return true; } abstract protected function canBeExtracted($file); abstract protected function extractFromDirectory($resource); } 