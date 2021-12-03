<?php

class ModelExtensionPaymentReepayViabill extends Model {

    public $payment_method = 'reepay_viabill';

    use Reepay\Catalog\Model\Extension\Payment\Method;

}