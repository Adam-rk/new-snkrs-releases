<?php

namespace App\Service;

use SendGrid\Mail\Content;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class GetPageContent
{

    public function getPageContent($uri)
    {

        $client = HttpClient::create();
        $response = $client->request('GET', $uri);
        $htmlContent = $response->getContent();
        $crawler = new Crawler($htmlContent);

        $content = [];
        $cards = $crawler->filter('figure');

        foreach ($cards as $card) {
            $cardCrawler = new Crawler($card);
            $content[] = $cardCrawler->html();
        }
        return $content;
    }
}
