<?php

class ControllerExtensionPaymentReepayCheckout extends Controller
{
    public function index()
    {
        $data = ['checkout_url' => $this->url->link('checkout/checkout', '', true) ];
        return $this->load->view('extension/payment/reepay_checkout', $data);
    }

   public function confirm()
    {
            $this->load->model('extension/payment/reepay_checkout');

            $charge_session_result = $this->model_extension_payment_reepay_checkout->getChargeSession();

            if('overlay' == $this->config->get('payment_reepay_checkout_checkout_type')) {

               $charge_session_result_arr = json_decode( $charge_session_result, true);

               $this->session->data['payment_method']['reepay']['charge_session_id'] =
                   isset( $charge_session_result_arr['body']['id'] ) ? $charge_session_result_arr['body']['id']: null;

               echo json_encode(['status' => $charge_session_result_arr['status'], 'body'=>
                                 ['url' => $this->url->link('extension/payment/reepay_checkout/initOverlay', '', true)]]);
            }else {

                echo $charge_session_result;
            }
   }

    /**
     * Return from payment window
     */
    public function accept() {

       $invoice_id_arr = explode('-', $this->request->get['invoice']);

       $order_id = isset($invoice_id_arr[0]) ? $invoice_id_arr[0] : null;

       if(!isset( $order_id )) {
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder( $order_id );

        if(empty($order_info)) {
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }

         $this->load->model('extension/payment/reepay_checkout');

         $result = $this->model_extension_payment_reepay_checkout->getInvoice( $this->request->get['invoice'] );

         if('success' == $result['status']) {
             if(!in_array($result['body']['state'], ['authorized', 'settled']) ) {
                 $this->response->redirect($this->url->link('checkout/checkout', '', true));
             }
         }else {
             $this->response->redirect($this->url->link('checkout/checkout', '', true));
         }

         // if it is unique invoice_id we save it to order table
        if(strstr($this->request->get['invoice'],'-')) {
            $data['invoice_id'] = $this->request->get['invoice'];
            $this->updatePaymentCustomField( json_encode($data),  $order_id );
        }

        $this->model_checkout_order->addOrderHistory($order_info['order_id'], $this->config->get('payment_reepay_checkout_order_status_id'));

        $this->response->redirect($this->url->link('checkout/success', '', true));
    }

    public function cancel() {
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
    }

    public function initOverlay() {
        if ('overlay' !== $this->config->get('payment_reepay_checkout_checkout_type')) {
            $this->response->redirect($this->url->link('checkout/cart', '', true));
        }

        $session_id = $this->session->data['payment_method']['reepay']['charge_session_id'];
        echo $this->load->view('extension/payment/reepay_checkout_overlay',
              ['charge_session_id' => $session_id,
               'accept_url' => $this->url->link('extension/payment/reepay_checkout/accept', '', true),
               'cancel_url' => $this->url->link('extension/payment/reepay_checkout/cancel', '', true)]
        );
    }

    protected function updatePaymentCustomField( $data, $order_id ) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET `payment_custom_field` = '" . $data . "' WHERE order_id = '". $order_id ."'");
    }
}