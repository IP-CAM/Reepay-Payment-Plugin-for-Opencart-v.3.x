<?php

class ModelExtensionPaymentReepayKlarnaPayLater extends Model {

    public $payment_method = 'reepay_klarna_pay_later';

    use Reepay\Catalog\Model\Extension\Payment\Method;
}
