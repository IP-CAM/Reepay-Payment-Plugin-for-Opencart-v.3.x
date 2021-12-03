<?php

class ModelExtensionPaymentReepayApplepay extends Model {

    public $payment_method = 'reepay_applepay';

    use Reepay\Catalog\Model\Extension\Payment\Method;
}