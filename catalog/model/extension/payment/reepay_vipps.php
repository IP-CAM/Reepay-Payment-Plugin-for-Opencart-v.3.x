<?php

class ModelExtensionPaymentReepayVipps extends Model {

    public $payment_method = 'reepay_vipps';

    use Reepay\Catalog\Model\Extension\Payment\Method;

}