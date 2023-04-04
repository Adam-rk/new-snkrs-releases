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
        
        $categories = $crawler->filter('div.copy-container h3.headline-5')->each(function( Crawler $node, $i){
            return $node->text();
        });
        $names = $crawler->filter('div.copy-container h6.headline-3')->each(function( Crawler $node, $i){
            return $node->text();
        });
        $links = $crawler->filter('.card-link')->each(function( Crawler $node, $i){
            return $node->attr('href');
        });
        
        for ($i = 0; $i < count($categories); $i++) {
            $content[] = [
                "categorie" => $categories[$i],
                "name" => $names[$i],
                "link" => "https://www.nike.com" . $links[$i]
            ];
        }
        
        return $content;
    }
}
