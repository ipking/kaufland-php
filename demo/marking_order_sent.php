<?php

include '.config.php';
/**
 * @var array $options
 */

$api = new \Kaufland\Api\Orders();
$api->setClientId($options['client_id']);
$api->setClientSecret($options['client_secret']);

$order_unit_id = '314567898654242';
$body = [
	"carrier_code"=> "DHL",
	"tracking_numbers"=> "0034123456789",
];

$rsp = $api->markingOrderSent($order_unit_id,$body);

print_r($rsp);
if(!$api->isSuccess()){
	die();
}

