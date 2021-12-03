<?php

class ControllerExtensionPaymentReepaySwish extends Controller {

    public $payment_method = 'reepay_swish';

    public $method_title_name = 'payment_reepay_swish_method_title';

    public $method_status_name = 'payment_reepay_swish_status';

    use \Reepay\Admin\Controller\Extension\Payment\Method;

}