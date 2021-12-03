<?php

namespace Reepay\Admin\Controller\Extension\Payment;

trait Method {

    public function index() {
        $this->load->language('extension/payment/' . $this->payment_method);

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->model_setting_setting->editSetting('payment_' . $this->payment_method, $this->request->post);

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
            'href' => $this->url->link('extension/payment/' . $this->payment_method, 'user_token=' . $this->session->data['user_token'], true)
        );

        if (isset($this->request->post[$this->method_title_name])) {
            $data[$this->method_title_name] = $this->request->post[$this->method_title_name];
        } else {
            $data['method_title'] = $this->config->get($this->method_title_name);
        }

        if (isset($this->request->post[ $this->method_status_name ])) {
            $data[$this->method_status_name] = $this->request->post[$this->method_status_name];
        } else {
            $data['status'] = $this->config->get($this->method_status_name);
        }

        $data['method_title_name'] = $this->method_title_name;
        $data['method_status_name'] = $this->method_status_name;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/reepay_method', $data));
    }

    public function order() {
        $data['user_token'] = $this->session->data['user_token'];
        $data['order_id'] = $this->request->get['order_id'];
        return $this->load->view('extension/payment/reepay_checkout_order', $data);
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/' . $this->payment_method)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

}