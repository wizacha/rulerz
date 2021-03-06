<?php

namespace spec\RulerZ\Executor\DoctrineQueryBuilder;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use PhpSpec\ObjectBehavior;

use RulerZ\Executor\DoctrineQueryBuilder\AutoJoin;

class AutoJoinSpec extends ObjectBehavior
{
    function let(QueryBuilder $target)
    {
        $this->beConstructedWith($target, [
            ['group']
        ]);
    }

    function it_joins_needed_tables(QueryBuilder $target)
    {
        $target->getRootAliases()->willReturn(['root_alias']);
        $target->getDQLPart('join')->willReturn([]);

        $target->join('root_alias.group', 'rulerz_group')->shouldBeCalled();

        $this->getJoinAlias('group')->shouldReturn(AutoJoin::ALIAS_PREFIX . 'group');
    }

    function it_uses_joined_tables(QueryBuilder $target, Join $join)
    {
        $target->getRootAliases()->willReturn(['root_alias']);
        $target->getDQLPart('join')->willReturn([
            'root_alias' => [$join]
        ]);

        $join->getJoin()->willReturn('root_alias.group');
        $join->getAlias()->willReturn('aliased_group');

        $target->join('root_alias.group', 'rulerz_aliased_group')->shouldNotBeCalled();

        $this->getJoinAlias('aliased_group')->shouldReturn('aliased_group');
    }
}
