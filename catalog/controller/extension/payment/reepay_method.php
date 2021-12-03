<?php

class ControllerExtensionPaymentReepayMethod extends Controller
{
    public function index() {
        return $this->load->view('extension/payment/reepay_checkout', []);
    }
}