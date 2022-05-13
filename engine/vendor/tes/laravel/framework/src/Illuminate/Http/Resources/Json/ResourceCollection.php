<?php
 namespace Illuminate\Http\Resources\Json; use IteratorAggregate; use Illuminate\Pagination\AbstractPaginator; use Illuminate\Http\Resources\CollectsResources; class ResourceCollection extends Resource implements IteratorAggregate { use CollectsResources; public $collects; public $collection; public function __construct($resource) { parent::__construct($resource); $this->resource = $this->collectResource($resource); } public function toArray($request) { return $this->collection->map->toArray($request)->all(); } public function toResponse($request) { return $this->resource instanceof AbstractPaginator ? (new PaginatedResourceResponse($this))->toResponse($request) : parent::toResponse($request); } } 