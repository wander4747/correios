<?php

namespace Correios;

use Correios\Request\RequestCrawler;
use Correios\Utils\Util;

class Zip
{

    const ZIP_URL = 'http://www.buscacep.correios.com.br/sistemas/buscacep/resultadoBuscaCepEndereco.cfm';
    private $zip;

    public function __construct(string $zip)
    {
        if (empty($zip)) {
            throw new \Exception('The parameter(zip) in the constructor cannot be empty');
        }

        $this->setZip($zip);
    }

    private function setZip(string $value)
    {
        return $this->zip = $value;
    }

    private function getZip()
    {
        return $this->zip;
    }

    public function find(): string
    {
        $body = ["relaxation" => $this->getZip(), 'tipoCEP' => 'ALL'];

        try {
            $request = (new  RequestCrawler())->post(self::ZIP_URL, $body);
           
            $result = [];
            $request->filter('.tmptabela tr:not(:first-child)')->each(function ($node) use (&$result) {
                
                $address = $node->filter('td:nth-child(1)')->text();
                $neighborhood = $node->filter('td:nth-child(2)')->text();
                $zip = $node->filter('td:nth-child(4)')->text();
                $cityUf = explode('/', $node->filter('td:nth-child(3)')->text());

                $city = $cityUf[0] ?? '';
                $uf = $cityUf[1] ?? '';
                
                $result[] = [
                    'address' => Util::cleanAccent($address),
                    'neighborhood' => Util::cleanAccent($neighborhood),
                    'city' => Util::cleanAccent($city),
                    'uf' => Util::cleanAccent($uf),
                    'zip' => Util::cleanAccent($zip)
                ];
            });

            if (empty($result)) {
                throw new \Exception('ZIP/address not found');
            }
            return json_encode($result, JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}