<?php

class ControllerExtensionPaymentReepayKlarnaSliceIt extends Controller {

    public $payment_method = 'reepay_klarna_slice_it';

    public $method_title_name = 'payment_reepay_klarna_slice_it_method_title';

    public $method_status_name = 'payment_reepay_klarna_slice_it_status';

    use \Reepay\Admin\Controller\Extension\Payment\Method;
}