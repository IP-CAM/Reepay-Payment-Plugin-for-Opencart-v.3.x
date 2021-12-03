<?php

class ModelExtensionPaymentReepayCheckout extends Model {

    const CHARGE_SESSION_URL = 'https://checkout-api.reepay.com/v1/session/charge';
    const GET_INVOICE_URL    = 'https://api.reepay.com/v1/invoice/';

    public function getMethod($address, $total) {

        $this->load->language('extension/payment/reepay_checkout');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_reepay_checkout_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('payment_reepay_checkout_total') > 0 && $this->config->get('payment_reepay_checkout_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('payment_reepay_checkout_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        $method_title_settings = $this->config->get('payment_reepay_checkout_method_title');

        $method_title = strlen($method_title_settings) > 3 ? $method_title_settings : $this->language->get('text_title');

        $logos = '<br/>';

        $style = 'height: 30px;
                  width: auto;
                  border-style: solid;
                  border-width: 1px;
                  border-radius: 4px;
                  border-color: #f8f8f8;
                  margin: 2px;';

        $i = 0;

        foreach( $this->config->get('payment_reepay_checkout_payment_logos') as $logo ) {
            $i++;

            $logos .= "<img src=\"catalog/view/theme/default/image/reepay/reepay_{$logo}.png\" height=\"32px\" style = \"". $style ."\"  width=\"93px\"  />";

            if($i % 4 == 0) {
                $logos .= "<br/>";
            }
        }

        if ($status) {
            $method_data = array(
                'code'       => 'reepay_checkout',
                'title'      => $method_title,
                'terms'      => $logos,
                'sort_order' => $this->config->get('payment_reepay_checkout_sort_order')
            );
        }

        return $method_data;
    }

    /**
     *
     * @return false|string
     */
    public function getChargeSession() {

        $result_string = $this->createChargeSession();

        $result_array = json_decode($result_string, true);

        if('success' != $result_array['status']) {
            $error = json_decode($result_array['error'], true);

            // try to generate another invoice
            // possible collision of invoice handles
            if(400 == $error['http_status'] && in_array( $error['code'], [105, 79, 29, 99, 72])) {

                // try to create charge session with unique handle
                $result_string = $this->createChargeSession(true);
                $result_array = json_decode($result_string, true);
            }
        }

        if('success' != $result_array['status']) {
            $this->session->data['error'] = 'Something went wrong during 
                                             the payment choose another payment method';
        }

        return $result_string;
    }

    /**
     * Generate post parameters for
     * creating charge session with Reepay api
     *
     * @param bool $unique_invoice_handle
     * @return array
     */
    protected function generateRequestParams($unique_invoice_handle = false) {

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder( $this->session->data['order_id'] );

        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

        $payment_methods = [];

        if('reepay_checkout' == $this->session->data['payment_method']['code']) {
            $payment_methods = $this->config->get('payment_reepay_checkout_payment_methods');
        }else {
            $payment_method = substr($this->session->data['payment_method']['code'], 7);
            $payment_methods[] = $payment_method;
        }

        $order_info['customer_id'];

        $params = [
            'order' => [
                'handle' => true == $unique_invoice_handle ? $order_info['order_id'] . '-' . time() : $order_info['order_id'],
                'amount' => false == $this->config->get('payment_reepay_checkout_order_lines') ? $this->prepareAmount($amount) : null,
                'order_lines' => $this->config->get('payment_reepay_checkout_order_lines') ? $this->getOrderLines() : null,
                'currency' => $order_info['currency_code'],
                'customer' => [
                    'test' => $this->config->get('payment_reepay_checkout_test') ? true : false,
                    'email' => $order_info['email'],
                    'address' => $order_info['payment_address_1'],
                    'address2' => $order_info['payment_address_2'],
                    'city' => $order_info['payment_city'],
                    'country' => $order_info['payment_iso_code_2'],
                    'phone' => $order_info['telephone'],
                    'company' => $order_info['payment_company'],
                    'vat' => '',
                    'first_name' => $order_info['payment_firstname'],
                    'last_name' => $order_info['payment_lastname'],
                    'postal_code' => $order_info['payment_postcode'],
                ],
                'billing_address' => [
                    'attention' => '',
                    'email' => $order_info['email'],
                    'address' => $order_info['payment_address_1'],
                    'address2' => $order_info['payment_address_2'],
                    'city' => $order_info['payment_city'],
                    'country' => $order_info['payment_iso_code_2'],
                    'phone' => $order_info['telephone'],
                    'company' => $order_info['payment_company'],
                    'vat' => '',
                    'first_name' => $order_info['payment_firstname'],
                    'last_name' => $order_info['payment_lastname'],
                    'postal_code' => $order_info['payment_postcode'],
                    'state_or_province' => $order_info['payment_zone_id']
                ],
            ],
            'settle' => $this->config->get('payment_reepay_checkout_instant_settle') ? true : false,
            'payment_methods' => $payment_methods,
            'accept_url' => $this->url->link('extension/payment/reepay_checkout/accept', '', true),
            'cancel_url' => $this->url->link('extension/payment/reepay_checkout/cancel', '', true)
        ];

        // customer is logged in
        if($order_info['customer_id']) {
            $params['order']['customer']['handle'] = $order_info['customer_id'];
        } else { // or generate handle for anonymous customer
            $params['order']['customer']['generate_handle'] = true;
        }

        $order_lines = $this->getOrderLines();

        $calculated_amount = 0;

        foreach ($order_lines as $order_line ) {
            if(isset($order_line['quantity'])) {
                $calculated_amount += $order_line['amount'] * $order_line['quantity'];
            } else {
                $calculated_amount += $order_line['amount'];
            }
        }

        $amount = $this->prepareAmount($amount);

        // skip order lines in case of rounding calculation error
         if($amount != $calculated_amount) {
            unset($params['order']['order_lines']);
            $params['order']['amount']  = $amount;
        }

        return $params;
    }

    /**
     * @param $url
     * @param $params
     * @param bool $is_post
     * @return false|string
     */
    public function sendCurl($url, $params, $is_post = true) {

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 60
        ]);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        $this->log( 'Request to server: '. $url);
        $this->log($params);

        $key = $this->config->get('payment_reepay_checkout_test') ?
            trim($this->config->get('payment_reepay_checkout_private_key_test'))
            : trim($this->config->get('payment_reepay_checkout_private_key_live'));

        curl_setopt_array($ch, [
            CURLOPT_USERAGENT     => 'curl',
            CURLOPT_HTTPHEADER    => $headers,
            CURLOPT_URL           => $url,
            CURLOPT_USERPWD => "$key:"
        ]);

        if (count($params) > 0) {
            $data      = json_encode($params, JSON_PRETTY_PRINT);
            $headers[] = 'Content-Length: ' . strlen($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        $response_arr = json_decode( $response, true );

        $this->log( "Response from server: ");
        $this->log($response_arr);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if( 2 == intval($http_code / 100) ) {
            $result = json_encode( ['status' => 'success', 'body' => $response_arr]  );
        }else {

            $curl_error = curl_error( $ch );
            $result = json_encode( ['status' => 'failure', 'error' => $response . $curl_error] );
        }

        return $result;
    }

    /**
     * @param $value
     * @return int
     */
    protected function prepareAmount( $value ) {
        return (int)(string)($value * 100);
    }

    /**
     * Generate order items for request to Reepay api
     * @return array
     */
    public function getOrderLines() {

        $items = [];

        foreach ( $this->cart->getProducts() as $product ) {
                $items[] = [
                 'ordertext' => $product['name'],
                 'amount' => (string) ($this->formatPrice($this->tax->calculate($product['price'], $product['tax_class_id'])) * 100),
                 'quantity' => $product['quantity'],
                ];
         }

        // add shipping item
        if (!empty( $this->session->data['shipping_method']['cost'] )) {
            $shipping_cost = $this->session->data['shipping_method']['cost'];
            $shipping_tax_class_id = $this->session->data['shipping_method']['tax_class_id'];
            $amount = ($this->formatPrice($this->tax->calculate($shipping_cost, $shipping_tax_class_id)));
            $amount *= 100;
            settype($amount, 'string');
            $items[] = [
                'ordertext' => $this->session->data['shipping_method']['title'],
                'amount' => $amount,
            ];
        }

        $this->load->model( 'checkout/order' );
        $totals = $this->model_checkout_order->getOrderTotals( $this->session->data['order_id'] );

        $coupon_data = [];
        if ( $totals ) {
            foreach ( $totals as $total ) {
                if ( $total['code'] == 'coupon' ) {
                    if ( !empty( $total['value'] ) ) {
                        $item_discount = abs($total['value']);
                        $coupon_data = $total;
                    }
                }
            }
        }

        // add coupon item if exists
        if ( $coupon_data ) {
            $items[] = [
                'ordertext'  => $coupon_data['title'],
                'amount' =>  $this->formatPrice( (float) $coupon_data['value'] ) * 100,
            ];
        }

        return $items;
    }

    /**
     * @param false $unique_invoice_handle
     * @return false|string
     */
    protected function createChargeSession($unique_invoice_handle = false) {
        $payload = $this->generateRequestParams($unique_invoice_handle);
        return $this->sendCurl(self::CHARGE_SESSION_URL, $payload);
    }

    /**
     * get invoice from reepay api
     * @param $invoiceId
     * @return false|string
     */
    public function getInvoice( $invoiceId ) {
        $url = self::GET_INVOICE_URL . $invoiceId;
        return json_decode($this->sendCurl($url, [], false ), true);
    }

    /**
     * @param $value
     * @return float
     */
    private function formatPrice( $value ) {
        return $this->format($value, $this->session->data['currency'], '', false);
    }

    /**
     * @param $number
     * @param $currency
     * @param string $value
     * @param bool $format
     * @return float
     */
    private function format($number, $currency, $value = '', $format = true) {
        $decimal_place = $this->currency->getDecimalPlace($currency);
        if (empty($decimal_place)) {
            $decimal_place = 0;
        }

        if (!$value) {
            $value = $this->currency->getValue($currency);
        }
        $amount = $value ? (float) $number * $value : (float) $number;
        $result = round($amount, (int) $decimal_place);

        return $result;
    }

    /**
     * @param $data
     */
    public function log($data) {
        if ($this->config->get('payment_reepay_checkout_debug')) {
                $log = new Log('reepay_checkout.log');
                $log->write($data);
        }
    }
}