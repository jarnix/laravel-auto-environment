<?php

namespace jarnix\LaravelAutoEnvironment;

class GroupMiddlewareTest extends TestCase
{
    public function testLocalHost()
    {
        $crawler = $this->call('GET', 'autoenv/get', [], [], [], []);
        $this->assertEquals(200, $crawler->getStatusCode());
        $this->assertEquals('localurl in local', $crawler->getContent());
    }

    public function testTestingHost()
    {
        $crawler = $this->call('GET', 'autoenv/get', [], [], [], []);
        $this->assertEquals(200, $crawler->getStatusCode());
        $this->assertEquals('testingurl in testing', $crawler->getContent());
    }

    public function testProductionHost()
    {
        $crawler = $this->call('GET', 'autoenv/get', [], [], [], []);
        $this->assertEquals(200, $crawler->getStatusCode());
        $this->assertEquals('productionurl in production', $crawler->getContent());
    }
}