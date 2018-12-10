<?php

namespace GraphAware\Neo4j\Client\Tests\Issues;

use GraphAware\Neo4j\Client\ClientBuilder;
use GraphAware\Neo4j\Client\Tests\Integration\IntegrationTestCase;

/**
 * Class Issue143Test
 * @package GraphAware\Neo4j\Client\Tests\Issues
 *
 * @group t143
 */
class Issue143Test extends IntegrationTestCase
{
    public function setUp()
    {
        $connections = array_merge($this->getConnections(), $this->getAdditionalConnections());

        $this->client = ClientBuilder::create()
            ->addConnection('a', $connections['non-exist'])
            ->addConnection('http', $connections['http'])
            ->addConnection('bolt', $connections['bolt'])
            ->setMaster('bolt')
            ->build();
    }

    protected function getAdditionalConnections()
    {
        return ['non-exist' => 'bolt://error:7687'];
    }


    public function testStackUsesMasterForWritesWhenOneisSet()
    {
        for ($x = 0; $x < 1000; $x++) {
            $stack = $this->client->stack();
            $stack->pushWrite('CREATE (n)');
            $this->client->runStack($stack);
        }
    }
}