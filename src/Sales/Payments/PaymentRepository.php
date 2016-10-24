<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Payments;

interface PaymentRepository
{
    public function add(OrderId $orderId, Payment $payment);
}
