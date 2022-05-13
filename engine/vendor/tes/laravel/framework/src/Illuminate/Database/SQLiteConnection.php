<?php
 namespace Illuminate\Database; use Illuminate\Database\Schema\SQLiteBuilder; use Illuminate\Database\Query\Processors\SQLiteProcessor; use Doctrine\DBAL\Driver\PDOSqlite\Driver as DoctrineDriver; use Illuminate\Database\Query\Grammars\SQLiteGrammar as QueryGrammar; use Illuminate\Database\Schema\Grammars\SQLiteGrammar as SchemaGrammar; class SQLiteConnection extends Connection { protected function getDefaultQueryGrammar() { return $this->withTablePrefix(new QueryGrammar); } public function getSchemaBuilder() { if (is_null($this->schemaGrammar)) { $this->useDefaultSchemaGrammar(); } return new SQLiteBuilder($this); } protected function getDefaultSchemaGrammar() { return $this->withTablePrefix(new SchemaGrammar); } protected function getDefaultPostProcessor() { return new SQLiteProcessor; } protected function getDoctrineDriver() { return new DoctrineDriver; } } 