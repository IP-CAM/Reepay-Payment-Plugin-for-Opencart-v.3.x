<?php

namespace Reepay\Catalog\Model\Extension\Payment;

trait Method {

    public function getMethod($address, $total) {

        $this->load->language('extension/payment/reepay_checkout');

        $method_title_settings = $this->config->get('payment_'. $this->payment_method .'_method_title');

        $method_title = strlen($method_title_settings) > 3 ?

        $method_title_settings : $this->language->get('text_title_' . $this->payment_method);

        $style = 'height: 30px;
                  width: auto;
                  border-style: solid;
                  border-width: 1px;
                  border-radius: 4px;
                  border-color: #f8f8f8;
                  margin: 2px;';

        $logos = "<br /><img src= \"catalog/view/theme/default/image/reepay/{$this->payment_method}.png\" height=\"32px\" style =\"". $style ."\" width=\"93px\" />";

        return array(
            'code'       => $this->payment_method,
            'title'      => $method_title,
            'terms'      => $logos,
            'sort_order' => $this->config->get('payment_reepay_checkout_sort_order')
        );
    }
}