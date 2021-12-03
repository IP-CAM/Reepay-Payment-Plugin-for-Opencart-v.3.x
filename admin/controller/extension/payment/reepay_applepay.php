<?php

class ControllerExtensionPaymentReepayApplepay extends Controller {

    public $payment_method = 'reepay_applepay';

    public $method_title_name = 'payment_reepay_applepay_method_title';

    public $method_status_name = 'payment_reepay_applepay_status';

    use \Reepay\Admin\Controller\Extension\Payment\Method;
}