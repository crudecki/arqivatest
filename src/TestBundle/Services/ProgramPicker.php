<?php

namespace TestBundle\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ProgramPicker 
{
    private $client;
    private $crawler;
    private $feed;
    private $nowFormatted;

    public function __construct()
    {
        $this->nowFormatted = date('Hi');
    }

    private function getProgramArray(): array
    {
        $crawler = (new Crawler($this->feed))->filter('item');
        $programArray = [];
        foreach ($crawler as $itemNode) {
            // No AC for handling missing titles, let the exception bubble up for now
            $titleNodeArray = explode(' : ', $itemNode->getElementsByTagName('title')[0]->nodeValue);

            $program['timeRaw'] = $titleNodeArray[0];
            $program['time'] = date('g:iA', strtotime(substr($titleNodeArray[0],0,2) . ':' . substr($titleNodeArray[0],2,2)));
            $program['title'] = $titleNodeArray[1];
            
            try {
                $program['description'] = $itemNode->getElementsByTagName('description')[0]->nodeValue;
            } catch (\ContextErrorException $e) {
                $program['description'] = 'No description available';
            }

            $programArray[] = $program;
        }

        return $programArray;
    }

    private function getShowFromNowByIndex(array $programArray, int $index = 0): array
    {
        for ($i = 0; $i < count($programArray ) - 1; $i++) {
            if ($this->nowFormatted >= $programArray[$i]['timeRaw'] && $this->nowFormatted <= $programArray[$i+1]['timeRaw']) {
                
                $programArray[$i + $index]['duration'] = 
                    (strtotime($programArray[$i + $index + 1]['timeRaw']) - strtotime($programArray[$i + $index]['timeRaw'])) / 60;

                return $programArray[$i + $index];
            }
        }
    }

    public function setupClient(array $options)
    {
        $this->client = new Client($options);

        return $this;
    }

    public function fetchFeed(string $uri)
    {
        $this->feed = (string) $this->client->get($uri)->getBody();

        return $this;
    }

    public function setFeedFromString(string $string)
    {
        $this->feed = $string;

        return $this;
    }

    public function setNowFormatted(string $formattedTimeString)
    {
        $this->nowFormatted = $formattedTimeString;

        return $this;
    }

    public function getCurrentShow()
    {
        $programArray = $this->getProgramArray();

        return $this->getShowFromNowByIndex($programArray, 0);
    }

    public function getNextShow()
    {
        $programArray = $this->getProgramArray();
        
        return $this->getShowFromNowByIndex($programArray, 1);
    }

    public function getPreviousShow()
    {
        $programArray = $this->getProgramArray();
        
        return $this->getShowFromNowByIndex($programArray, -1); 
    }
}