<?php

class orders extends cdek_integrator implements exchange {
    protected $method = 'new_orders.php';
    private $correct_direction = '+';
    private $correct_time = '00:00';
    private $number;
    private $xml;

    public function setNumber($number) {
        $this->number = $number;
    }

    public function setOrders($data) {
        $this->xml = $this->createXML($data);
    }

    public function setCorrrectTime($time) {
        $this->correct_time = $time;
    }

    public function setCorrrectdirection($direction) {
        $this->correct_direction = $direction;
    }

    public function getData() {
        return [
            'xml_request' => $this->xml
        ];
    }

    public function prepareResponse($data, &$error) {

        if (isset($data->Order)) {

            foreach ($data->Order as $order) {

                $attributes = $order->attributes();

                if (isset($attributes->Number)) {
                    $error[(int)$attributes->Number][(string)$attributes->ErrorCode] = mb_convert_encoding((string)($attributes->Msg), 'UTF-8', 'auto');
                }
            }
        } elseif (isset($data->DeliveryRequest)) {

            $attributes = $data->DeliveryRequest->attributes();

            if (isset($attributes->ErrorCode)) {

                $error[][(string)$attributes->ErrorCode] = mb_convert_encoding((string)($attributes->Msg), 'UTF-8', 'auto');
            }
        } elseif (is_scalar($data)) {
            $error[]['error_response'] = 'Ошибка сервера СДЭК: неверный формат ответа!';
        }

        return $data;
    }

    private function createXML($data = []) {

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';

        if (!empty($data)) {

            $cdek_info = $this->loadComponent('info');

            $xml .= '<DeliveryRequest Number="' . $this->xml_entities($this->number) . '" Date="' . $this->xml_entities($this->date) . '" Account="' . $this->xml_entities($this->account) . '" Secure="' . $this->xml_entities($this->getSecure()) . '" OrderCount="' . $this->xml_entities(count($data)) . '">';

            foreach ($data as $order_info) {

                $attribute = [];
                $attribute[] = 'Number="' . $this->xml_entities($order_info['order_id']) . '"';
                $attribute[] = 'SendCityCode="' . $this->xml_entities($order_info['city_id']) . '"';
                $attribute[] = 'RecCityCode="' . $this->xml_entities($order_info['recipient_city_id']) . '"';
                $attribute[] = 'RecipientName="' . $this->xml_entities($order_info['recipient_name']) . '"';

                if ($order_info['foreign_delivery'] != '') {
                    $attribute[] = 'ForeignDelivery="' . $this->xml_entities($order_info['foreign_delivery']) . '"';
                }

                if ($order_info['recipient_email'] != '') {
                    $attribute[] = 'RecipientEmail="' . $this->xml_entities($order_info['recipient_email']) . '"';
                }

                if ($order_info['seller_name'] != '') {
                    $attribute[] = 'SellerName="' . $this->xml_entities($order_info['seller_name']) . '"';
                }

                if ($order_info['delivery_recipient_cost'] != '') {
                    $attribute[] = 'DeliveryRecipientCost="' . $this->xml_entities($order_info['delivery_recipient_cost']) . '"';
                }

                $attribute[] = 'Phone="' . $this->xml_entities(preg_replace('/[^\d]+/isu', '', $order_info['recipient_telephone'])) . '"';
                $attribute[] = 'TariffTypeCode="' . $this->xml_entities($order_info['tariff_id']) . '"';

                if ($order_info['cdek_comment'] != '') {
                    $attribute[] = 'Comment="' . $this->xml_entities($order_info['cdek_comment']) . '"';
                }

                if (!empty($order_info['currency'])) {
                    $attribute[] = 'ItemsCurrency="' . $this->xml_entities($order_info['currency']) . '"';
                }

                if (!empty($order_info['cod'])) {
                    $attribute[] = 'RecipientCurrency="' . $this->xml_entities($order_info['currency_cod']) . '"';
                }

                $xml .= '<Order ' . implode(' ', $attribute) . ' >';

                $tariff_info = $cdek_info->getTariffInfo($order_info['tariff_id']);

                $attribute = [];

                //$attribute[] = 'Address="' . $order_info['address']['address'] . '"';

				if (!empty($order_info['address']['street'])) {
                    $attribute[] = 'Street="' . $this->xml_entities($order_info['address']['street']) . '"';
                }
				
				if (!empty($order_info['address']['house'])) {
                    $attribute[] = 'House="' . $this->xml_entities($order_info['address']['house']) . '"';
                }
				
                if (!empty($order_info['address']['flat'])) {
                    $attribute[] = 'Flat="' . $this->xml_entities($order_info['address']['flat']) . '"';
                }

                if (!empty($order_info['address']['pvz_code'])) {
                    $attribute[] = 'PvzCode="' . $this->xml_entities($order_info['address']['pvz_code']) . '"';
                }

                $xml .= '<Address ' . implode(' ', $attribute) . ' />';

                foreach ($order_info['package'] as $package_id => $package_info) {

                    $attribute = [];
                    $attribute[] = 'Number="' . $this->xml_entities($package_id) . '"';
                    $attribute[] = 'BarCode="' . $this->xml_entities($package_info['brcode'] != '' ? $package_info['brcode'] : $package_id) . '"';

                    if ($package_info['pack']) {

                        $attribute[] = 'SizeA="' . $this->xml_entities($package_info['size_a']) . '"';
                        $attribute[] = 'SizeB="' . $this->xml_entities($package_info['size_b']) . '"';
                        $attribute[] = 'SizeC="' . $this->xml_entities($package_info['size_c']) . '"';
                    }

                    $attribute[] = 'Weight="' . $this->xml_entities($package_info['weight']) . '"';

                    $xml .= '<Package  ' . implode(' ', $attribute) . '>';

                    foreach ($package_info['item'] as $item) {

                        $item['comment'] = trim(strip_tags(html_entity_decode($item['comment'], ENT_QUOTES, 'UTF-8')));
                        $xml .= '<Item WareKey="' . $this->xml_entities($item['ware_key']) . '" Cost="' . $this->xml_entities($this->normalizePrice($item['cost'])) . '" Payment="' . $this->xml_entities($this->normalizePrice($item['payment'])) . '" Weight="' . $this->xml_entities((float)$item['weight']) . '" Amount="' . $this->xml_entities((int)$item['amount']) . '" Comment="' . $this->xml_entities(htmlspecialchars($item['comment'])) . '" />';
                    }

                    $xml .= '</Package>';
                }

                if (!empty($order_info['add_service'])) {

                    foreach ($order_info['add_service'] as $code => $info) {
                        $xml .= '<AddService ServiceCode="' . $this->xml_entities((int)$code) . '" />';
                    }
                }

                if (!empty($order_info['schedule'])) {

                    $xml .= '<Schedule>';

                    foreach ($order_info['schedule'] as $attempt_id => $attempt_info) {

                        $attribute = [];
                        $attribute[] = 'ID="' . $this->xml_entities($order_info['order_id'] . $attempt_id) . '"';
                        $attribute[] = 'Date="' . $this->xml_entities($attempt_info['date']) . '"';
                        $attribute[] = 'TimeBeg="' . $this->xml_entities($attempt_info['time_beg']) . '"';
                        $attribute[] = 'TimeEnd="' . $this->xml_entities($attempt_info['time_end']) . '"';
                        $attribute[] = 'Comment="' . $this->xml_entities(htmlspecialchars($attempt_info['comment'], ENT_QUOTES, 'UTF-8')) . '"';

                        if ($attempt_info['recipient_name'] != '') {
                            $attribute[] = 'RecipientName="' . $this->xml_entities($attempt_info['recipient_name']) . '"';
                        }

                        if ($attempt_info['phone'] != '') {
                            $attribute[] = 'Phone="' . $this->xml_entities($attempt_info['phone']) . '"';
                        }

                        $xml .= '<Attempt ' . implode(' ', $attribute) . '>';

                        if ($attempt_info['new_address']) {

                            $attribute = [];

                            $attribute[] = 'Street="' . $this->xml_entities($attempt_info['street']) . '"';
                            $attribute[] = 'House="' . $this->xml_entities($attempt_info['house']) . '"';
                            $attribute[] = 'Flat="' . $this->xml_entities($attempt_info['flat']) . '"';

                            if (in_array($tariff_info['mode_id'], [2, 4])) {
                                $attribute[] = 'PvzCode="' . $this->xml_entities($attempt_info['pvz_code']) . '"';
                            }

                            $xml .= '<Address ' . implode(' ', $attribute) . '/>';
                        }

                        $xml .= '</Attempt>';
                    }

                    $xml .= '</Schedule>';
                }

                if ($order_info['courier']['call']) {

                    $xml .= '<CallCourier>';

                    $attribute = [];
                    $attribute[] = 'Date="' . $this->xml_entities($order_info['courier']['date']) . '"';
                    $attribute[] = 'TimeBeg="' . $this->xml_entities($order_info['courier']['time_beg']) . '"';
                    $attribute[] = 'TimeEnd="' . $this->xml_entities($order_info['courier']['time_end']) . '"';
                    $attribute[] = 'SendCityCode="' . $this->xml_entities($order_info['courier']['city_id']) . '"';

                    if ($order_info['courier']['lunch_beg'] != '' && $order_info['courier']['lunch_end'] != '') {
                        $attribute[] = 'LunchBeg="' . $this->xml_entities($order_info['courier']['lunch_beg']) . '"';
                        $attribute[] = 'LunchEnd="' . $this->xml_entities($order_info['courier']['lunch_end']) . '"';
                    }

                    $xml .= '<Call ' . implode(' ', $attribute) . '>';

                    $attribute = [];
                    $attribute[] = 'Street="' . $this->xml_entities($order_info['courier']['street']) . '"';
                    $attribute[] = 'House="' . $this->xml_entities($order_info['courier']['house']) . '"';
                    $attribute[] = 'Flat="' . $this->xml_entities($order_info['courier']['flat']) . '"';
                    $attribute[] = 'SendPhone="' . $this->xml_entities($order_info['courier']['send_phone']) . '"';
                    $attribute[] = 'SenderName="' . $this->xml_entities($order_info['courier']['sender_name']) . '"';

                    if (trim($order_info['courier']['comment']) != '') {
                        $attribute[] = 'Comment="' . $this->xml_entities(trim($order_info['courier']['comment'])) . '"';
                    }

                    $xml .= '<SendAddress  ' . implode(' ', $attribute) . '/>';

                    $xml .= '</Call>';
                    $xml .= '</CallCourier>';
                }

                $xml .= '</Order>';
            }

            $xml .= '</DeliveryRequest>';
        }

        /*
        echo $xml;
        exit;
	*/

        return $xml;
    }

    private function normalizePrice($price) {
        return (float)round(str_replace(',', '.', $price), 4);
    }
}