<?php

namespace RetailExpress\SkyLink\Sales\Payments;

interface PaymentRepository
{
    public function add(OrderId $orderId, Payment $payment);
}
