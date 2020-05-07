<?php

namespace Correios;

use Correios\Request\RequestCrawler;

class Sms
{

    private $trackingCode;
    private $cellSender;
    private $recipientCell;

    const TRACKING_URL = 'https://www2.correios.com.br/sistemas/rastreamento/resultado.cfm';


    public function __construct(string $trackingCode, $cellSender, $recipientCell)
    {
        if (empty($trackingCode)) {
            throw new \Exception('The parameter(tracking code) in the constructor cannot be empty');
        }

        $this->setTrackingCode($trackingCode);
        $this->setCellSender($cellSender);
        $this->setRecipientCell($recipientCell);
    }

    public function send() {

        try {
            $body = [
                "objetos" => $this->getTrackingCode(),
                "etiqueta" => $this->getTrackingCode(),
                "celularum" => $this->getCellSender(),
                "celulardois" => $this->getRecipientCell(),
                "termo" => "on",
                "botao" => "OK"
            ];
            $request = (new  RequestCrawler())->post(self::TRACKING_URL, $body);
        }  catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
       
    }

    private function getTrackingCode()
    {
        return $this->trackingCode;
    }

    public function setTrackingCode(string $trackingCode)
    {
        return $this->trackingCode = $trackingCode;
    }

    private function getCellSender()
    {
        return $this->cellSender;
    }

    
    public function setCellSender(string $cellSender)
    {
        return $this->cellSender = $cellSender;
    }

    private function getRecipientCell()
    {
        return $this->recipientCell;
    }

    public function setRecipientCell(string $recipientCell)
    {
        return $this->recipientCell = $recipientCell;
    }
}