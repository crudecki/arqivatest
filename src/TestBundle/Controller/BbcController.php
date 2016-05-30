<?php

namespace TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class BbcController extends Controller
{
    /**
     * @Route("/bbcone/now", name="bbc.current")
     * @Template
     */
    public function nowAction()
    {
		$programPicker = $this->get('test.programpicker');

		$program = $programPicker->setupClient([
			    'base_uri' => 'http://bleb.org',
			    'timeout'  => 2.0,
			])
		    ->fetchFeed('/tv/data/rss.php?ch=bbc1&day=0')
		    ->getCurrentShow();

    	return ['program' => $program];
    }

    /**
     * @Route("/bbcone/previous", name="bbc.previous")
     * @Template
     */
    public function previousAction()
    {
        $programPicker = $this->get('test.programpicker');

		$program = $programPicker->setupClient([
			    'base_uri' => 'http://bleb.org',
			    'timeout'  => 2.0,
			])
		    ->fetchFeed('/tv/data/rss.php?ch=bbc1&day=0')
		    ->getPreviousShow();

    	return ['program' => $program];
    }

    /**
     * @Route("/bbcone/next", name="bbc.next")
     * @Template
     */
    public function nextAction()
    {
        $programPicker = $this->get('test.programpicker');

		$program = $programPicker->setupClient([
			    'base_uri' => 'http://bleb.org',
			    'timeout'  => 2.0,
			])
		    ->fetchFeed('/tv/data/rss.php?ch=bbc1&day=0')
		    ->getNextShow();

    	return ['program' => $program];
    }
}
