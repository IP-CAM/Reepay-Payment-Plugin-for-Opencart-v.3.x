<?php

class ControllerExtensionPaymentReepayKlarnaPayLater extends Controller {

    public $payment_method = 'reepay_klarna_pay_later';

    public $method_title_name = 'payment_reepay_klarna_pay_later_method_title';

    public $method_status_name = 'payment_reepay_klarna_pay_later_status';

    use \Reepay\Admin\Controller\Extension\Payment\Method;

}