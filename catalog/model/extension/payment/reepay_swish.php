<?php

class ModelExtensionPaymentReepaySwish  extends Model {

    public $payment_method = 'reepay_swish';

    use Reepay\Catalog\Model\Extension\Payment\Method;

}