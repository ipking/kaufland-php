<?php

include '.config.php';
/**
 * @var array $options
 */

$api = new \Kaufland\Api\Orders();
$api->setClientId($options['client_id']);
$api->setClientSecret($options['client_secret']);

$order_arr = [];
$offset = 0;
$limit = 20;
while(1){
	$data = [
		'storefront'                => "de",
		'ts_created_from_iso'       => "2022-12-10T09:00:00",
		'ts_units_updated_from_iso' => "2022-12-10T09:00:00",
		'fulfillment_type'          => "fulfilled_by_merchant",
		'offset'                    => $offset,
		'limit'                     => $limit,
	];
	
	$rsp = $api->retrievingOrders($data);
	if(!$api->isSuccess()){
		print_r($rsp);
		die();
	}
	if(!$rsp['data']){
		break;
	}
	$offset += $limit;
	$order_arr = array_merge($order_arr,$rsp['data']);
}



$order_no  = "MB7UWLD";
$data = [
	'embedded' => "order_invoices",
];
$rsp = $api->retrievingOrder($order_no,$data);

print_r($rsp);
if(!$api->isSuccess()){
	die();
}


