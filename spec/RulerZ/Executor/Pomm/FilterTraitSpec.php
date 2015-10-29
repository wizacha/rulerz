<?php

namespace spec\RulerZ\Executor\Pomm;

use PhpSpec\ObjectBehavior;
use PommProject\Foundation\Where;
use RulerZ\Context\ExecutionContext;
use RulerZ\Filter\FilterResult;
use RulerZ\Stub\Executor\PommExecutorStub;
use RulerZ\Stub\ModelStub;

class FilterTraitSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('RulerZ\Stub\Executor\PommExecutorStub');
    }

    function it_call_findWhere_on_the_target(ModelStub $modelStub, Where $whereClause)
    {
        $results = ['result'];

        PommExecutorStub::$executeReturn = $whereClause;
        $modelStub->findWhere($whereClause)->willReturn($results);

        $this->filter($modelStub, $parameters = [], $operators = [], new ExecutionContext())
            ->shouldReturnAnInstanceOf(FilterResult::class);
    }

    function it_call_acustom_method_if_specified_in_the_context(ModelStub $modelStub, Where $whereClause)
    {
        $results = ['result'];

        PommExecutorStub::$executeReturn = $whereClause;
        $modelStub->findCustom($whereClause)->willReturn($results);

        $this->filter($modelStub, $parameters = [], $operators = [], new ExecutionContext([
            'method' => 'findCustom'
        ]))->shouldReturnAnInstanceOf(FilterResult::class);
    }
}
