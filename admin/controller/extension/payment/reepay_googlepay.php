<?php

class ControllerExtensionPaymentReepayGooglepay extends Controller {

    public $payment_method = 'reepay_googlepay';

    public $method_title_name = 'payment_reepay_googlepay_method_title';

    public $method_status_name = 'payment_reepay_googlepay_status';

    use \Reepay\Admin\Controller\Extension\Payment\Method;

}