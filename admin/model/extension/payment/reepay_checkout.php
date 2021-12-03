<?php

class ModelExtensionPaymentReepayCheckout extends Model {

    /**
     * @para
     * m $invoiceId
     */
    public function getInvoice($invoice_handle) {
        return $this->sendCurl("https://api.reepay.com/v1/invoice/{$invoice_handle}");
    }

    /**
     * @param $invoice_handle
     * @param $amount
     * @return false|string
     */
    public function settleCharge($invoice_handle, $amount) {
        $amount = $this->prepareAmount( $amount );
        return $this->sendCurl("https://api.reepay.com/v1/charge/{$invoice_handle}/settle", ['amount' => $amount] );
    }

    /**
     * @param $invoice_handle
     * @param $amount
     * @return false|string
     */
    public function voidCharge($invoice_handle, $amount) {
        $amount = $this->prepareAmount( $amount );
        return $this->sendCurl("https://api.reepay.com/v1/charge/{$invoice_handle}/cancel", ['amount' => $amount] );
    }

    /**
     * @param $invoice_handle
     * @param $amount
     * @return false|string
     */
    public function refundCharge($invoice_handle, $amount) {
        $amount = $this->prepareAmount($amount);
        return $this->sendCurl('https://api.reepay.com/v1/refund', ['invoice' => $invoice_handle, 'amount' => $amount]);
    }

    /**
     * @param $url
     * @param $params
     * @param bool $is_post
     * @return false|string
     */
    public function sendCurl($url, $params = [], $is_post = true) {
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
            $data = json_encode($params, JSON_PRETTY_PRINT);
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
             $result = json_encode(['status' => 'success', 'body' => $response_arr]);

        }else {
            $curl_error = curl_error( $ch );
            $result = json_encode(['status' => 'failure', 'error' => $response . $curl_error]);
        }

        return $result;
    }

    /**
     * @param $amount
     * @return int
     */
    protected function prepareAmount($amount) {
        return (int)(string)($amount * 100);
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