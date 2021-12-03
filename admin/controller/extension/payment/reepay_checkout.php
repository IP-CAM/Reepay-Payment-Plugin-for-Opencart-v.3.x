<?php

class ControllerExtensionPaymentReepayCheckout extends Controller {

    public function index() {
        $this->load->language('extension/payment/reepay_checkout');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_reepay_checkout', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/reepay_checkout', 'user_token=' . $this->session->data['user_token'], true)
        );

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['payment_reepay_checkout_total'])) {
            $data['payment_reepay_checkout_total'] = $this->request->post['payment_reepay_checkout_total'];
        } else {
            $data['payment_reepay_checkout_total'] = $this->config->get('payment_reepay_checkout_total');
        }

        if (isset($this->request->post['payment_reepay_checkout_method_title'])) {
            $data['payment_reepay_checkout_method_title'] = $this->request->post['payment_reepay_checkout_method_title'];
        } else {
            $data['payment_reepay_checkout_method_title'] = $this->config->get('payment_reepay_checkout_method_title');
        }

        if (isset($this->request->post['payment_reepay_checkout_status'])) {
            $data['payment_reepay_checkout_status'] = $this->request->post['payment_reepay_checkout_status'];
        } else {
            $data['payment_reepay_checkout_status'] = $this->config->get('payment_reepay_checkout_status');
        }

        if (isset($this->request->post['payment_reepay_checkout_sort_order'])) {
            $data['payment_reepay_checkout_sort_order'] = $this->request->post['payment_reepay_checkout_sort_order'];
        } else {
            $data['payment_reepay_checkout_sort_order'] = $this->config->get('payment_reepay_checkout_sort_order');
        }

        if (isset($this->request->post['payment_reepay_checkout_geo_zone_id'])) {
            $data['payment_reepay_checkout_geo_zone_id'] = $this->request->post['payment_reepay_checkout_geo_zone_id'];
        } else {
            $data['payment_reepay_checkout_geo_zone_id'] = $this->config->get('payment_reepay_checkout_geo_zone_id');
        }

        if (isset($this->request->post['payment_reepay_checkout_order_status_id'])) {
            $data['payment_reepay_checkout_order_status_id'] = $this->request->post['payment_reepay_checkout_order_status_id'];
        } else {
            $data['payment_reepay_checkout_order_status_id'] = $this->config->get('payment_reepay_checkout_order_status_id');
        }

        if (isset($this->request->post['payment_reepay_checkout_private_key_live'])) {
            $data['payment_reepay_checkout_private_key_live'] = $this->request->post['payment_reepay_checkout_private_key_live'];
        } else {
            $data['payment_reepay_checkout_private_key_live'] = $this->config->get('payment_reepay_checkout_private_key_live');
        }

        if (isset($this->request->post['payment_reepay_checkout_private_key_test'])) {
            $data['payment_reepay_checkout_private_key_test'] = $this->request->post['payment_reepay_checkout_private_key_test'];
        } else {
            $data['payment_reepay_checkout_private_key_test'] = $this->config->get('payment_reepay_checkout_private_key_test');
        }

        if (isset($this->request->post['payment_reepay_checkout_checkout_type'])) {
            $data['payment_reepay_checkout_checkout_type'] = $this->request->post['payment_reepay_checkout_checkout_type'];
        } else {
            $data['payment_reepay_checkout_checkout_type'] = $this->config->get('payment_reepay_checkout_checkout_type');
        }

        if (isset($this->request->post['payment_reepay_checkout_order_lines'])) {
            $data['payment_reepay_checkout_order_lines'] = $this->request->post['payment_reepay_checkout_order_lines'];
        } else {
            $data['payment_reepay_checkout_order_lines'] = $this->config->get('payment_reepay_checkout_order_lines');
        }

        if (isset($this->request->post['payment_reepay_checkout_test'])) {
            $data['payment_reepay_checkout_test'] = $this->request->post['payment_reepay_checkout_test'];
        } else {
            $data['payment_reepay_checkout_test'] = $this->config->get('payment_reepay_checkout_test');
        }

        if (isset($this->request->post['payment_reepay_checkout_instant_settle'])) {
            $data['payment_reepay_checkout_instant_settle'] = $this->request->post['payment_reepay_checkout_instant_settle'];
        } else {
            $data['payment_reepay_checkout_instant_settle'] = $this->config->get('payment_reepay_checkout_instant_settle');
        }

        if (isset($this->request->post['payment_reepay_checkout_debug'])) {
            $data['payment_reepay_checkout_debug'] = $this->request->post['payment_reepay_checkout_debug'];
        } else {
            $data['payment_reepay_checkout_debug'] = $this->config->get('payment_reepay_checkout_debug');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['payment_types'] = [['id' => 'window', 'name' => 'Window' ],['id' => 'overlay', 'name' => 'Overlay']];
        $data['yes_no_options'] = [['option' => 1, 'name' => 'Yes' ],['option' => 0, 'name' => 'No']];

        $data['payment_methods'] =
            ['card'             => 'All available debit / credit cards',
             'dankort'          => 'Dankort',
             'visa'             => 'VISA',
             'visa_elec'        => 'VISA Electron',
             'mc'               => 'MasterCard',
             'mobilepay'        => 'American Express',
             'viabill'          => 'ViaBill',
             'swish'            => 'Swish',
             'vipps'            =>  'Vipps',
             'diners'           =>  'Diners Club',
             'maestro'          =>  'Maestro',
             'discover'         =>  'Discover',
             'jcb'              => 'JBC',
             'ffk'              => 'Forbrugsforeningen',
             'paypal'           => 'PayPal',
             'applepay'         => 'Apple Pay',
             'googlepay'        => 'Google Pay',
             'klarna_pay_later' => 'Klarna Pay Later',
             'klarna_pay_now'   => 'Klarna Pay Now',
             'klarna_slice_it'  => 'Klarna Slice It!'];

        $data['payment_methods_selected'] = $this->config->get('payment_reepay_checkout_payment_methods');
        $data['payment_logos_selected'] = $this->config->get('payment_reepay_checkout_payment_logos');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/payment/reepay_checkout', $data));
    }

    public function order() {
        $data['user_token'] = $this->session->data['user_token'];
        $data['order_id'] =  $this->request->get['order_id'];
        $res = $this->getOrderPaymentCustomField($this->request->get['order_id']);
        $result = isset($res->row['payment_custom_field']) ? json_decode($res->row['payment_custom_field'], true) : null;
        if( $result ) {
            if( isset( $result['invoice_id'] ) &&  $result['invoice_id'] ) {
                $data['order_id'] = $result['invoice_id'];
            }
        }
        return $this->load->view('extension/payment/reepay_checkout_order', $data);
    }

    /**
     * Preparing data for Order History [Reepay Checkout Tab]
     */
    public function control() {

        $this->load->language('extension/payment/reepay_checkout');

        $this->load->model('extension/payment/reepay_checkout');
        $result = $this->model_extension_payment_reepay_checkout->getInvoice($this->request->get['order_id']);
        $result_decoded = json_decode($result, true);

        if('success' == $result_decoded['status']) {

            $data = $result_decoded['body'];

            array_walk($data['transactions'], function(&$item){
                $item['amount'] = $this->formatAmount( $item['amount']);
            });

            $amount_authorized = isset($data['authorized_amount']) ? $data['authorized_amount'] : 0;

            if('cancelled'== $data['state']) {
                $zero = $this->formatAmount(0);
                $data['amount_to_capture'] = $zero;
                $data['amount_to_refund'] = $zero;

            } else {

                $amount_to_capture = $amount_authorized - $data['settled_amount'];
                if($amount_to_capture < 0 ) {
                    $amount_to_capture = 0;
                }

                $data['amount_to_capture'] = $this->formatAmount($amount_to_capture);
                $data['amount_to_refund']  = $this->formatAmount($data['settled_amount'] - $data['refunded_amount']);
            }

            $data['authorized_amount'] = $this->formatAmount($amount_authorized);
            $data['settled_amount'] = $this->formatAmount($data['settled_amount']);
            $data['refunded_amount'] = $this->formatAmount($data['refunded_amount']);
            $data['user_token'] = $this->session->data['user_token'];
            $data['order_id'] = $this->request->get['order_id'];

            $payment_type = $data['transactions'][0]['payment_type'];

            $data['payment_type'] = $payment_type;

            $card_type = isset($data['transactions'][0][$payment_type . '_transaction']['card_type'] ) ?
                $data['transactions'][0][$payment_type . '_transaction']['card_type']: null;

            $exp_date = isset($data['transactions'][0][$payment_type . '_transaction']['exp_date'] ) ?
                $data['transactions'][0][$payment_type . '_transaction']['exp_date']: null;

            $masked_card = isset($data['transactions'][0][$payment_type . '_transaction']['masked_card'] ) ?
                $data['transactions'][0][$payment_type . '_transaction']['masked_card']: null;

            $data['card_type'] = $card_type;
            $data['exp_date'] = $exp_date;
            $data['masked_card'] = $masked_card;

            echo $this->load->view('extension/payment/reepay_checkout_order_control', $data );
        }

    }

    public function charge_settle() {
        $this->load->model('extension/payment/reepay_checkout');
        echo $this->model_extension_payment_reepay_checkout->settleCharge($_POST['handle'], $_POST['amount']);
    }

    public function void_charge() {
        $this->load->model('extension/payment/reepay_checkout');
        echo $this->model_extension_payment_reepay_checkout->voidCharge($_POST['handle'], $_POST['amount']);
    }

    public function refund_charge() {
        $this->load->model('extension/payment/reepay_checkout');
        echo $this->model_extension_payment_reepay_checkout->refundCharge($_POST['handle'], $_POST['amount']);
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/reepay_checkout')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function getOrderPaymentCustomField($order_id) {
        return $this->db->query("SELECT `payment_custom_field` FROM  `" . DB_PREFIX . "order` WHERE order_id = '". $order_id ." ' ");
    }

    protected function formatAmount( $amount ) {
        return number_format( $amount / 100, 2,'.', '');
    }
}