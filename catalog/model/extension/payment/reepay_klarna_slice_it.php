<?php

class ModelExtensionPaymentReepayKlarnaSliceIt extends Model {

    public $payment_method = 'reepay_klarna_slice_it';

    use Reepay\Catalog\Model\Extension\Payment\Method;

}