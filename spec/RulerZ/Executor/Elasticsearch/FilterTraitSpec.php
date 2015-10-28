<?php

namespace spec\RulerZ\Executor\Elasticsearch;

use Elasticsearch\Client;
use PhpSpec\ObjectBehavior;

use RulerZ\Context\ExecutionContext;
use RulerZ\Filter\FilterResult;
use RulerZ\Stub\Executor\ElasticsearchExecutorStub;

class FilterTraitSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('RulerZ\Stub\Executor\ElasticsearchExecutorStub');
    }

    function it_calls_search_on_the_target(Client $target)
    {
        $esQuery = ['array with the ES query'];
        $esResponse = [
            "took" => 3,
            "timed_out" => false,
            "hits" => [
                "total" => 0,
                "max_score" => null,
                "hits" => [],
            ],
        ];

        ElasticsearchExecutorStub::$executeReturn = $esQuery;
        $target->search([
            'index' => 'es_index',
            'type'  => 'es_type',
            'body'  => ['query' => $esQuery],
        ])->willReturn($esResponse);

        $this->filter($target, $parameters = [], $operators = [], new ExecutionContext([
            'index' => 'es_index',
            'type'  => 'es_type',
        ]))->shouldReturnAnInstanceOf(FilterResult::class);
    }

    function it_throws_an_exception_when_the_execution_context_is_incomplete(Client $target)
    {
        $this
            ->shouldThrow('RuntimeException')
            ->duringFilter($target, $parameters = [], $operators = [], new ExecutionContext());
    }
}
