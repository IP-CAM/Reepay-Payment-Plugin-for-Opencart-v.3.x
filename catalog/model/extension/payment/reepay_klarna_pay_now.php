<?php

class ModelExtensionPaymentReepayKlarnaPayNow extends Model {

    public $payment_method = 'reepay_klarna_pay_now';

    use Reepay\Catalog\Model\Extension\Payment\Method;
}