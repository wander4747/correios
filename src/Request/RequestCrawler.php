<?php

namespace Correios\Request;

use Goutte\Client;

class RequestCrawler implements RequestInterface{


    public function get(string $url, array $query = [], array $options = [])
    {
        $client = new Client();
        $crawler = $client->request('GET', $url, $query);
        return $crawler;
    }

    public function post(string $url, array $body = [], array $options = [])
    {
        $result = $this->request(new Client(), 'POST', $url, $body);
        return $result;
    }

    private function request(Client $client, $type, $url, $parameters)
    {
        $crawler = $client->request($type, $url, $parameters);
        return $crawler;
    }
}