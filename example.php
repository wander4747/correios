<?php

require __DIR__ . '/vendor/autoload.php';

use Correios\Tracking;
use Correios\Zip;
use Correios\Sms;

//parameter: tracking code
$tracking = new Tracking('PW950125025BR');
echo $tracking->find();

//parameter: address or zip
$zip = new Zip('Avenida Paulista');
echo $zip->find();

//parameter: tracking code, cell sender, recipient cell
$sms = new Sms('PW950125025BR','(31) 99332-6095', '(31) 99332-6096');
$sms->send();

