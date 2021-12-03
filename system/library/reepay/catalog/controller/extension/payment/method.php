<?php

namespace Reepay\Catalog\Controller\Extension\Payment;

trait Method {

    public function index() {
        return $this->load->view('extension/payment/reepay_checkout', []);
    }

}