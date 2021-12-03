<?php

class ControllerExtensionPaymentReepayVipps extends Controller {

    public $payment_method = 'reepay_vipps';

    public $method_title_name = 'payment_reepay_vipps_method_title';

    public $method_status_name = 'payment_reepay_vipps_status';

    use \Reepay\Admin\Controller\Extension\Payment\Method;

}