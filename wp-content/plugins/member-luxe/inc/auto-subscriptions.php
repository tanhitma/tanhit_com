<?php

if (!class_exists('MemberLuxAutoSubscriptions')) {
    class MemberLuxAutoSubscriptions
    {

        private $main_options;
        private $getresponse_apiurl = 'http://api2.getresponse.com';

        public function __construct()
        {
            $this->main_options = get_option('wpm_main_options');
            add_action('user_register', array($this, 'new_user'), 10, 1);


        }

        public function justclick_register($user_id, $level_subscriptions = array())
        {
            $level_subscriptions = $this->get_service_subscriptions ($level_subscriptions, 'justclick');

            if ($this->main_options['auto_subscriptions']['justclick']['active'] != 'on'  && empty($level_subscriptions)) {
                return;
            }

            if (!empty($level_subscriptions)) {
                $params = array(
                    'user_id' => $level_subscriptions['user_id'],
                    'user_rps_key' => $level_subscriptions['user_rps_key'],
                    'rid' => $level_subscriptions['rid'],
                    'doneurl2' => $level_subscriptions['doneurl2']
                );
            } else {
                $params = array(
                    'user_id' => $this->main_options['auto_subscriptions']['justclick']['user_id'],
                    'user_rps_key' => $this->main_options['auto_subscriptions']['justclick']['user_rps_key'],
                    'rid' => $this->main_options['auto_subscriptions']['justclick']['rid'],
                    'doneurl2' => $this->main_options['auto_subscriptions']['justclick']['doneurl2']
                );
            }

            $user_info = get_userdata($user_id);

            $response = array(
                'message' => '',
                'error'   => false
            );

            // Логин в системе Джастклик
            $user_rs['user_id'] = trim($params['user_id']);
            // Ключ для формирования подписи. см. "Магазин" - "Настройки" - "RussianPostService и API" - "Секретный ключ для подписи"
            $user_rs['user_rps_key'] = trim($params['user_rps_key']);


            // Формируем массив данных для передачи в API
            $rid = trim($params['rid']);
            $send_data = array(
                'rid[0]'     => $rid, // группа, в которую попадёт подписчик
                'lead_name'  => $user_info->first_name,
                'lead_email' => $user_info->user_email,
               // 'lead_phone' => $user_info->phone,
                'lead_city'  => '',
                'aff'        => '', // ID партнёра, за которым будет закреплён подписчик
                'tag'        => '', // произвольная метка
                'ad'         => '', // ID рекламной метки созданой в магазине
                'doneurl2'   =>  $params['doneurl2']// адрес после подтверждения подписки
            );

            // Формируем подпись к передаваемым данным
            $send_data['hash'] = $this->justclick_get_hash($send_data, $user_rs);
            // Вызываем функцию AddLeadToGroup в API и декодируем полученные данные
            $resp = json_decode($this->justclick_send('http://'.$user_rs['user_id'].'.justclick.ru/api/AddLeadToGroup', $send_data));

            // Проверяем ответ сервиса
            if (!$this->justclick_check_hash($resp, $user_rs)) {
                $response['message'] = "Ошибка! Подпись к ответу не верна!";
                $response['error'] = true;
                return $response;
            }

            if ($resp->error_code == 0) {
                $response['message'] = "Пользователь добавлен в группу {$send_data['rid[0]']}. Ответ сервиса: {$resp->error_code}";
                return $response;
            } else {
                $response['message'] = "Ошибка код:{$resp->error_code} - описание: {$resp->error_text}";
                $response['error'] = true;
                return $response;
            }

        }

        // Отправляем запрос в API сервиса
        public function justclick_send($url, $data)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // выводим ответ в переменную
            $res = curl_exec($ch);
            curl_close($ch);
            return $res;
        }

        // Формируем подпись к передаваемым в API данным
        public function justclick_get_hash($params, $user_rs)
        {
            $params = http_build_query($params);
            $user_id = trim($user_rs['user_id']);
            $secret = trim($user_rs['user_rps_key']);
            $params = "{$params}::{$user_id}::{$secret}";
            return md5($params);
        }

        // Проверяем полученную подпись к ответу
        public function justclick_check_hash($resp, $user_rs)
        {
            $secret = trim($user_rs['user_rps_key']);
            $code = $resp->error_code;
            $text = $resp->error_text;
            $hash = md5("$code::$text::$secret");
            if ($hash == $resp->hash)
                return true; // подпись верна
            else
                return false; // подпись не верна
        }

        public function smart_isSubscribed ($email, $api_key, $delivery_id)
        {
            $url = 'http://api.smartresponder.ru/subscribers.html?api_key='. $api_key . '&delivery_id=' . $delivery_id . '&action=check_email&email=' . $email;

            $response = file_get_contents($url);

            $xml = new SimpleXMLElement($response);

            $count = (int)$xml->list['count'];

            return ($count > 0) ? true : false;
        }

        // SmartResponder
        public function smart_register($user_id, $level_subscriptions = array())
        {
            $level_subscriptions = $this->get_service_subscriptions ($level_subscriptions, 'smartresponder');

            if ($this->main_options['auto_subscriptions']['smartresponder']['active'] != 'on' && empty($level_subscriptions)) {
                return;
            }

            if (!empty($level_subscriptions)) {
                $params = array(
                    'api_key'     => trim($level_subscriptions['api_key']),
                    'delivery_id' => trim($level_subscriptions['delivery_id']),
                    'track_id'    => trim($level_subscriptions['track_id']),
                    'group_id'    => trim($level_subscriptions['group_id'])
                );
            } else {
                $params = array(
                    'api_key'     => trim($this->main_options['auto_subscriptions']['smartresponder']['api_key']),
                    'delivery_id' => trim($this->main_options['auto_subscriptions']['smartresponder']['delivery_id']),
                    'track_id'    => trim($this->main_options['auto_subscriptions']['smartresponder']['track_id']),
                    'group_id'    => trim($this->main_options['auto_subscriptions']['smartresponder']['group_id'])
                );
            }

            $user_info = get_userdata($user_id);

            $param = "api_key=" . $params['api_key'];
            $param .= "&action=create";
            $param .= "&format=json";
            $param .= "&delivery_id=" . $params['delivery_id'];
            $param .= "&track_id=" . $params['track_id'];
            $param .= "&group_id=" . $params['group_id'];
            $param .= "&email=" . $user_info->user_email;
            $param .= "&first_name=" . $user_info->first_name;
            $param .= "&last_name=" . $user_info->last_name;
            //$param .= "&middle_name=".$user_info->surname;
            //$param .= "&phones=".$user_info->phone;

            $url = 'http://api.smartresponder.ru/subscribers.html?' . $param;

            $response = file_get_contents($url);

        }

        /**
         * @param $user_id
         */

        public function getresponse_register($user_id, $level_subscriptions = array())
        {
            $level_subscriptions = $this->get_service_subscriptions ($level_subscriptions, 'getresponse');

            if ($this->main_options['auto_subscriptions']['getresponse']['active'] != 'on' && empty($level_subscriptions)) {
                return;
            }

            if (!empty($level_subscriptions)) {
                $subscr_params = array(
                    'campaign_token' => trim($level_subscriptions['campaign_token']),
                    'api_key'        => trim($level_subscriptions['api_key'])
                );
            } else {
                $subscr_params = array(
                    'campaign_token' => trim($this->main_options['auto_subscriptions']['getresponse']['campaign_token']),
                    'api_key'        => trim($this->main_options['auto_subscriptions']['getresponse']['api_key'])
                );
            }

            $user_info = get_userdata($user_id);

            $campaign = $subscr_params['campaign_token'];
            $name = $user_info->last_name . ' ' . $user_info->first_name;
            $email = $user_info->user_email;
            $cycle_day = 0;
            $customs = array();

            $c = array();
            $params = array(
                'campaign' => trim($campaign),
                'name' => trim($name),
                'email' => trim($email),
                'cycle_day' => $cycle_day,
                'ip' => $_SERVER['REMOTE_ADDR']
            );

            // default ref
            $c[] = array('name' => 'ref', 'content' => 'wordpress');
            if ( !empty($customs))  {
                foreach($customs as $key => $val)  {
                    if (!empty($key) && !empty($val))
                        $c[] = array('name' => $key, 'content' => $val);
                }
            }
            $params['customs'] = $c;

            $request  = $this->getresponse_request('add_contact', $params, null, $subscr_params);
            $response = $this->getresponse_execute($request);

            // contact already added to campaign
            if ( !empty($customs) && !is_array($response) && isset($response->error) && preg_match('[Contact already added to target campaign]', $response->error->message)) {
                $contact_id = $this->getresponse_getContact($email, $campaign, $subscr_params);
                $contact_id = array_pop(array_keys((array)$contact_id));
                if ($contact_id && !empty($params['customs'])) {
                    $this->getresponse_setContactCustoms($contact_id, $params['customs'], $subscr_params);
                }
            }
            else {
                return $response->result;
            }
        }

        public function getresponse_getContact($email_address, $campaign_id, $subscr_params) {
            $request  = $this->getresponse_request('get_contacts', array ( 'email' => array( 'EQUALS' => $email_address), 'campaigns' => array($campaign_id) ), null, $subscr_params);
            $response = $this->getresponse_execute($request);
            if ( !is_array($response) && !$response->error) {
                return $response->result;
            }
        }

        public function getresponse_setContactCustoms($contact_id, $customs, $subscr_params) {
            $request  = $this->getresponse_request('set_contact_customs', array('contact' => $contact_id, 'customs' => $customs), null, $subscr_params);
            $response = $this->getresponse_execute($request);
            if ( !is_array($response) && !$response->error) {
                return $response->result;
            }
        }

        private function getresponse_request($method, $params = null, $id = null, $subscr_params) {

            $api_key = $this->main_options['auto_subscriptions']['getresponse']['api_key'];

            $array = array($api_key);
            if ( !is_null($params)) {
                $array[1] = $params;
            }

            $request = json_encode(array('method' => $method, 'params' => $array, 'id' => $id));
            return $request;
        }

        private function getresponse_execute($request) {
            $ch = curl_init($this->getresponse_apiurl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            curl_setopt($ch, CURLOPT_HEADER, 'Content-type: application/json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($ch));
            if (curl_error($ch)) {
                return array('type' => 'error', 'msg' => curl_error($ch));
            }
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ( !(($httpCode == '200') || ($httpCode == '204'))) {
                return array('type' => 'error', 'msg' => 'API call failed. Server returned status code ' . $httpCode);
            }

            curl_close($ch);
            return $response;
        }


        /**
         *  UniSender
         */
        public function unisender_register($user_id, $level_subscriptions = array())
        {
            $level_subscriptions = $this->get_service_subscriptions ($level_subscriptions, 'unisender');

            if ($this->main_options['auto_subscriptions']['unisender']['active'] != 'on' && empty($level_subscriptions)) {
                return;
            }

            if (!empty($level_subscriptions)) {
                $params = array(
                    'api_key' => trim($level_subscriptions['api_key']),
                    'lists'   => $level_subscriptions['lists'],
                    'tags'    => $level_subscriptions['tags']
                );
            } else {
                $params = array(
                    'api_key' => trim($this->main_options['auto_subscriptions']['unisender']['api_key']),
                    'lists'   => $this->main_options['auto_subscriptions']['unisender']['lists'],
                    'tags'    => $this->main_options['auto_subscriptions']['unisender']['tags']
                );
            }

            $user_info = get_userdata($user_id);

            // Данные о новом подписчике
            $user_email = $user_info->user_email;
            $user_name = $user_info->last_name .' '. $user_info->first_name;
            $user_ip = $_SERVER['REMOTE_ADDR'];

/*            // Создаём POST-запрос
            $POST = array(
                'api_key'       => $params['api_key'],
                'list_ids'      => $params['lists'],
                'fields[email]' => $user_email,
                'fields[Name]'  => $user_name,
                'request_ip'    => $user_ip,
                'tags'          => urlencode($params['tags'])
            );

			
			
			
		
            // Устанавливаем соединение
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_URL,
                'http://api.unisender.com/ru/api/subscribe?format=json');
            $result = curl_exec($ch);

            if ($result) {
                // Раскодируем ответ API-сервера
                $jsonObj = json_decode($result);

                if (null === $jsonObj) {
                    // Ошибка в полученном ответе
                    echo "Invalid JSON";

                } elseif
                (!empty($jsonObj->error)) {
                    // Ошибка добавления пользователя
                    return "An error occured: " . $jsonObj->error . "(code: " . $jsonObj->code . ")";

                } else {
                    // Новый пользователь успешно добавлен
                    return "Added. ID is " . $jsonObj->result->person_id;

                }
            } else {
                // Ошибка соединения с API-сервером
                return "API access error";
            }*/
			
/**************************************************************/			
/**************************************************************/			
/**************сомописный код для почты************************/		

	//POST variables
	$boolean = 'true';
	
	
	//Subscribe
	$postdata = http_build_query(
	    array(
	    'name' => $user_name,
	    'email' => $user_email,
	    'list' => $params['lists'],
/*		'request_ip'    => $user_ip,*/
	    'boolean' => 'true'
	    )
	);
	$opts = array('http' => array('method'  => 'POST', 'header'  => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
	$context  = stream_context_create($opts);
	$result = file_get_contents($params['api_key'].'/subscribe', false, $context);

	
/**************сомописный код для почты************************/		
/**************************************************************/			
/**************************************************************/			
						
        }

        /**
         * @param $user_id
         */

        public function new_user($user_id)
        {
            $user_info = get_userdata($user_id);
            if (in_array('customer', $user_info->roles)) {

                $level_subscriptions = $this->get_level_subscriptions($user_id);

                $this->justclick_register($user_id, $level_subscriptions);
                $this->smart_register($user_id, $level_subscriptions);
                $this->getresponse_register($user_id, $level_subscriptions);
                $this->unisender_register($user_id, $level_subscriptions);
            }
        }

        public function get_level_subscription ($service, $subscriptions)
        {
            return $subscriptions[$service]['active'] == 'on' ? $subscriptions[$service] : array();
        }

        public function get_service_subscriptions ($level_subscriptions, $service)
        {
            $subscriptions = array();

            if (!empty($level_subscriptions)) {
                foreach ($level_subscriptions as $subscription) {
                    if(!empty($subscription[$service])) {
                        $subscriptions[] = $subscription[$service];
                    }
                }
            }

            return $subscriptions;
        }

        public function get_level_subscription_by_key ($key)
        {
            $subscriptions = array(
                'justclick' => array(),
                'smartresponder' => array(),
                'getresponse' => array(),
                'unisender' => array()
            );

            $index = wpm_search_key_id($key);

            if($index !== null) {
                $term_meta = get_option( "taxonomy_term_" . $index['term_id'] );

                if (array_key_exists('auto_subscriptions', (array)$term_meta)) {
                    $subscriptions['justclick'] = $this->get_level_subscription('justclick', $term_meta['auto_subscriptions']);
                    $subscriptions['smartresponder'] = $this->get_level_subscription('smartresponder', $term_meta['auto_subscriptions']);
                    $subscriptions['getresponse'] = $this->get_level_subscription('getresponse', $term_meta['auto_subscriptions']);
                    $subscriptions['unisender'] = $this->get_level_subscription('unisender', $term_meta['auto_subscriptions']);
                }
            }

            return $subscriptions;
        }

        public function get_level_subscriptions ($user_id)
        {
            $subscriptions = array();

            $user_keys = get_user_meta($user_id, 'user_key', true);
            if (!empty($user_keys)) {
                foreach ($user_keys as $key) {
                    $subscriptions[$key] = $this->get_level_subscription_by_key($key);
                }
            }

            return $subscriptions;
        }

        public function subscribe_user_by_key ($user_id, $key)
        {
            $level_subscriptions = $this->get_level_subscription_by_key($key);

            if (count($level_subscriptions)) {
                $this->justclick_register($user_id, $level_subscriptions);
                $this->smart_register($user_id, $level_subscriptions);
                $this->getresponse_register($user_id, $level_subscriptions);
                $this->unisender_register($user_id, $level_subscriptions);
            }
        }

        public function get_unsubscribed_user_keys ($user_id)
        {
            $unsubscribed_user_keys = get_user_meta($user_id, 'unsubscribed_user_keys', true);

            if (empty($unsubscribed_user_keys)) {
                $unsubscribed_user_keys = array(
                    'justclick' => array(),
                    'smartresponder' => array(),
                    'getresponse' => array(),
                    'unisender' => array()
                );

                add_user_meta($user_id, 'unsubscribed_user_keys', $unsubscribed_user_keys);
            }

            return $unsubscribed_user_keys;
        }

        public function key_needs_unsubscription ($key, $unsubscribed_user_keys)
        {
            $index      = wpm_search_key_id($key);
            $date_end   = strtotime($index['key_info']['date_end']);
            $expired    = (($date_end - time()) < 0) ? true : false;

            return (!in_array($key, $unsubscribed_user_keys) && $expired) ? true : false;
        }

        public function auto_disable_is_enabled ($subscription)
        {
            return (!empty($subscription) && array_key_exists('auto_disable', $subscription) && $subscription['auto_disable'] == 'on')
                    ? true : false;
        }

        public function justclick_unsubscribe ($params, $user)
        {
            $user_rs['user_id']      = trim($params['user_id']);
            $user_rs['user_rps_key'] = trim($params['user_rps_key']);

            $send_data = array(
                'lead_email' => $user->user_email,
                'rass_name' => trim($params['rid'])

            );

            $send_data['hash'] = $this->justclick_get_hash($send_data, $user_rs);

            $resp = json_decode($this->justclick_send('http://'.$user_rs['user_id'].'.justclick.ru/api/DeleteSubscribe', $send_data));

            $response = array(
                'message' => '',
                'error'   => false
            );

            if (!$this->justclick_check_hash($resp, $user_rs)) {
                $response['message'] = "Ошибка! Подпись к ответу не верна!";
                $response['error']   = true;
                return $response;
            }

            if($resp->error_code == 0) {
                $response['message'] = "Пользователь удален из группы {$send_data['rass_name']}. Ответ сервиса: {$resp->error_code}";
            } else {
                $response['message'] = "Ошибка код:{$resp->error_code} - описание: {$resp->error_text}";
                $response['error']   = true;

            }

            return $response;
        }

        public function smartresponder_unsubscribe ($params, $user)
        {
            $param  = "api_key=" . trim($params['api_key']);
            $param .= "&action=unlink_with_delivery";
            $param .= "&format=xml";
            $param .= "&delivery_id=" . trim($params['delivery_id']);
            $param .= "&search[email]=" . $user->user_email;

            $url = 'http://api.smartresponder.ru/subscribers.html?' . $param;

            $response = file_get_contents($url);

            return $response;
        }


        public function getresponse_unsubscribe ($params, $user)
        {
            $request = json_encode(
                array(
                    'method' => 'get_contacts',
                    'params' => array(
                        $params['api_key']
                    ),
                    'id' => null
                )
            );

            $response = $this->getresponse_execute($request);

            if(count($response)) {
                foreach ($response->result as $contact_id => $contact) {
                    if($contact->email == $user->user_email) {

                        $delete_request = json_encode(
                            array(
                                'method' => 'delete_contact',
                                'params' => array(
                                    $params['api_key'],
                                    array(
                                        'contact' => $contact_id
                                    )
                                ),
                                'id' => null
                            )
                        );

                        $delete_response = $this->getresponse_execute($delete_request);

                        return $delete_response;
                    }
                }
            }

            return false;
        }

        public function unisender_unsubscribe ($params, $user)
        {
            $params = array(
                'api_key' => trim($params['api_key']),
                'lists'   => $params['lists']
            );

            $POST = array (
                'api_key'      => $params['api_key'],
                'list_ids'     => $params['lists'],
                'contact_type' => 'email',
                'contact'      => $user->user_email
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_URL, 'http://api.unisender.com/ru/api/exclude?format=json');

            $result = curl_exec($ch);

            if ($result) {
                $jsonObj = json_decode($result);
                if(null===$jsonObj) {
                    return "Invalid JSON";
                } elseif(!empty($jsonObj->error)) {
                    return "An error occured: " . $jsonObj->error . "(code: " . $jsonObj->code . ")";
                } else {
                    return "Excluded";
                }
            } else {
                return "API access error";
            }
        }

        public function renew_subscription_status ()
        {
            $users = get_users(array('role' => 'customer'));

            if (count($users)) {
                foreach ($users as $user) {
                    $user_keys = get_user_meta($user->ID, 'user_key', true);

                    $unsubscribed_user_keys = $this->get_unsubscribed_user_keys($user->ID);

                    if (!empty($user_keys)) {

                        foreach ($user_keys as $key) {

                            $subscriptions = $this->get_level_subscription_by_key($key);

                            if(!count($subscriptions)) {
                                $subscriptions = (array) $this->main_options['auto_subscriptions'];
                            }

                            if (count($subscriptions)) {

                                foreach ($subscriptions as $service => $subscription) {

                                    $key_needs_unsubscription = $this->key_needs_unsubscription($key, $unsubscribed_user_keys[$service]);

                                    if ($this->auto_disable_is_enabled($subscription) && !empty($subscription) && $key_needs_unsubscription) {

                                        switch ($service) {
                                            case 'justclick':
                                                $this->justclick_unsubscribe($subscription, $user);
                                                break;
                                            case 'smartresponder':
                                                $this->smartresponder_unsubscribe($subscription, $user);
                                                break;
                                            case 'getresponse':
                                                $this->getresponse_unsubscribe($subscription, $user);
                                                break;
                                            case 'unisender':
                                                $this->unisender_unsubscribe($subscription, $user);
                                                break;
                                        }

                                        $unsubscribed_user_keys[$service][] = $key;
                                    }
                                }
                            }
                        }
                    }

                    update_user_meta($user->ID, 'unsubscribed_user_keys', $unsubscribed_user_keys);
                }
            }
        }
    }

}
$wpm_auto_subscription = new MemberLuxAutoSubscriptions();


