<?php

namespace RulerZ\Executor\Elasticsearch;

use RulerZ\Context\ExecutionContext;
use RulerZ\Filter\FilterResult;

trait ElasticsearchFilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var array $searchQuery */
        $searchQuery = $this->execute($target, $operators, $parameters);

        /** @var \Elasticsearch\Client $target */
        $response = $target->search([
            'index' => $context['index'],
            'type'  => $context['type'],
            'body'  => ['query' => $searchQuery],
        ]);

        if (!isset($response['hits'])) {
            return new FilterResult(0, function () {});
        }

        $count = $response['hits']['total'];

        return new FilterResult($count, function () use ($response) {
            foreach ($response['hits']['hits'] as $hit) {
                yield $hit['_source'];
            }
        });
    }
}
