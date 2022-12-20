<?php

namespace Kaufland\Api;

use Kaufland\Core\Client;

class Orders extends Client {
	
	/**
	 * Operation retrievingOrder
	 * @param string $order_no
	 * @param array $query
	 */
	public function retrievingOrder($order_no,$query = [])
	{
		return $this->send("/v2/orders/".$order_no, [
			'method' => 'GET',
			'query'  => $query,
		]);
	}
	
	/**
	* Operation retrievingOrders
	 * @param array $query
	*/
	public function retrievingOrders($query = [])
	{
		return $this->send("/v2/orders", [
		  'method' => 'GET',
		  'query'  => $query,
		]);
	}
	
	/**
	 * Operation retrievingOrderUnits
	 * @param array $query
	 */
	public function retrievingOrderUnits($query = [])
	{
		return $this->send("/v2/order-units", [
			'method' => 'GET',
			'query'  => $query,
		]);
	}
	
	/**
	 * Operation markingOrderSent
	 * @param string $order_unit_id
	 * @param array $body
	 */
	public function markingOrderSent($order_unit_id,$body = [])
	{
		return $this->send("/v2/order-units/".$order_unit_id."/send", [
			'method' => 'PATCH',
			'json'   => $body,
		]);
	}
	
}
