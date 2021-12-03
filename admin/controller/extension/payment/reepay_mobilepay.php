<?php

class ControllerExtensionPaymentReepayMobilepay extends Controller {

    public $payment_method = 'reepay_mobilepay';

    public $method_title_name = 'payment_reepay_mobilepay_method_title';

    public $method_status_name = 'payment_reepay_mobilepay_status';

    use \Reepay\Admin\Controller\Extension\Payment\Method;

}