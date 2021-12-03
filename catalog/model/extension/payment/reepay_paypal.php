<?php

class ModelExtensionPaymentReepayPaypal extends Model {

    use Reepay\Catalog\Model\Extension\Payment\Method;

    public $payment_method = 'reepay_paypal';

}