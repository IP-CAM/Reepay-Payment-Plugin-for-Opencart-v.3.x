<?php

class ModelExtensionPaymentReepayMobilepay extends Model {

    public $payment_method = 'reepay_mobilepay';

    use Reepay\Catalog\Model\Extension\Payment\Method;
}