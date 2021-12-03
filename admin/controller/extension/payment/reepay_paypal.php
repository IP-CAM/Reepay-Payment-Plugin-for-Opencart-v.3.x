<?php

class ControllerExtensionPaymentReepayPaypal extends Controller {

    public $payment_method = 'reepay_paypal';

    public $method_title_name = 'payment_reepay_paypal_method_title';

    public $method_status_name = 'payment_reepay_paypal_status';

    use \Reepay\Admin\Controller\Extension\Payment\Method;
}