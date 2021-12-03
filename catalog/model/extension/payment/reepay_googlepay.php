<?php

class ModelExtensionPaymentReepayGooglepay extends Model {

    public $payment_method = 'reepay_googlepay';

    use Reepay\Catalog\Model\Extension\Payment\Method;
}