<?php

namespace Correios;

use Correios\Request\RequestCrawler;
use Correios\Utils\Util;
use Carbon\Carbon;

class Tracking
{

    private $trackingCode;
    const TRACKING_URL = 'https://www2.correios.com.br/sistemas/rastreamento/ctrl/ctrlRastreamento.cfm?';

    public function __construct(string $trackingCode)
    {
        if (empty($trackingCode)) {
            throw new \Exception('The parameter(tracking code) in the constructor cannot be empty');
        }

        $this->setTrackingCode($trackingCode);
    }

    private function setTrackingCode(string $trackingCode)
    {
        return $this->trackingCode = $trackingCode;
    }

    private function getTrackingCode()
    {
        return $this->trackingCode;
    }

    public function find()
    {
        try {

            $events = [];
            
            $body = ["objetos" => $this->getTrackingCode()];
            $request = (new  RequestCrawler())->post(self::TRACKING_URL, $body);
            
            $request->filter('.listEvent')->each(function ($node) use (&$events) {
                $dateEvent = $node->filter('td.sroDtEvent')->eq(0)->html();
                
                list($date, $hour, $locale) = explode("<br>", Util::cleanHtml($dateEvent));

                $date = $date ?? '';
                $hour = $hour ?? '';
                $locale = $locale ?? '';
                $dateFormat = $date.' '.$hour;

                $statusEvent = $node->filter('td.sroLbEvent')->eq(0)->html();
                list($status, $forwarded) = explode("<br>", Util::cleanHtml($statusEvent));

                $status = $status ?? '';
                $forwarded = $forwarded ?? '';

                $events[] = [
                    'date' => $date,
                    'hour' => $hour,
                    'date_format' => Carbon::createFromFormat('d/m/Y H:i', $dateFormat)->format('Y-m-d H:i:s'),
                    'timestamp' => Carbon::createFromFormat('d/m/Y H:i', $dateFormat)->timestamp,
                    'city' => strip_tags($locale),
                    'forwarded' => strip_tags($forwarded),
                    'status' => strip_tags($status)
                ];
            });
           
            if (!isset($events[0])) {
                throw new \Exception('Tracking code not found');
            }
            $lastEvent =  $events[0];
            
            $result = [
                'tracking_code' => $this->getTrackingCode(),
                'last_timestamp' => $lastEvent['timestamp'],
                'last_status' => $lastEvent['status'],
                'last_date' => $lastEvent['date'],
                'last_date_format' => $lastEvent['date_format'],
                'city' => $lastEvent['city'],
                'forwarded' => $lastEvent['forwarded'],
                'tracking' => $events
            ];

            return json_encode($result, JSON_PRETTY_PRINT);
           
        }  catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}