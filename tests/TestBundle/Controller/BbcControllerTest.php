<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BbcControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();

        // Setup test environment
        $programpicker = $this->client->getContainer()->get('test.programpicker');
        $programpicker->setNowFormatted('1615');
        $programpicker->setFeedFromString(file_get_contents(__DIR__ . '/../../data/test_rss_feed_bbc'));
    }

	/**
	 * @test
	 */
    public function now_action_should_display_currently_airing_show()
    {
        $crawler = $this->client->request('GET', '/bbcone/now');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('The Instant Gardener', $crawler->filter('body')->text());
        $this->assertContains('4:00PM', $crawler->filter('body')->text());
        $this->assertContains('45 min', $crawler->filter('body')->text());
        $this->assertContains('Garden transformation show.', $crawler->filter('body')->text());
    }

	/**
	 * @test
	 */
    public function previous_action_should_display_the_show_before_the_current_one()
    {
        $crawler = $this->client->request('GET', '/bbcone/previous');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Escape to the Country', $crawler->filter('body')->text());
        $this->assertContains('3:00PM', $crawler->filter('body')->text());
        $this->assertContains('60 min', $crawler->filter('body')->text());
        $this->assertContains('Property series. Jules Hudson and', $crawler->filter('body')->text());
    }

    /**
	 * @test
	 */
    public function next_action_should_display_the_next_airing_show()
    {
        $crawler = $this->client->request('GET', '/bbcone/next');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Flog It!', $crawler->filter('body')->text());
        $this->assertContains('4:45PM', $crawler->filter('body')->text());
        $this->assertContains('60 min', $crawler->filter('body')->text());
        $this->assertContains('Paul Martin is joined by experts Catherine Southon', $crawler->filter('body')->text());
    }
}
