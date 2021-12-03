<?php

class ControllerExtensionPaymentReepayViabill extends Controller {

    public $payment_method = 'reepay_viabill';

    public $method_title_name = 'payment_reepay_viabill_method_title';

    public $method_status_name = 'payment_reepay_viabill_status';

    use \Reepay\Admin\Controller\Extension\Payment\Method;

}