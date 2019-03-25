<?php  
class cdek_integrator {
	
	protected $base_url = 'https://int.cdek.ru/'; // Базовый URL API
	protected $account; // Учетная запись
	protected $secure_password; // Cекретный код
	protected $date;
	
	private $version = "1.0"; // Версия модуля
	private static $ext_dir; 
	
	public $error; // Ошибки при выполнении метода
	
	public function __construct($account = '', $secure_password = '', $date = '') {
		
		if (!empty($account) &&  !empty($secure_password)) {
			$this->setAuth($account, $secure_password);
		}
		
		if (!$date) {
			
			$default_timezone = date_default_timezone_get();
			
			date_default_timezone_set('UTC');
			
			$date = date('Y-m-d', time() + 10800);
			
			date_default_timezone_set($default_timezone);
			
		}
		
		$this->setDate($date);
		
		$this->init();
	}
	
	/**
	 * Установка планируемой даты отправки
	 * 
	 * @param string $date дата планируемой отправки, например '2014-06-25'
	 */
	public function setDate($date) {
		$this->date = $date;
	}
	
	/**
	 * Авторизация ИМ
	 * 
	 * @param string $account логин
	 * @param string $secure_password пароль
	 */
	public function setAuth($account, $secure_password) {
		$this->account = $account;
		$this->secure_password = $secure_password;
	}
	
	/**
	 * Защифрованный пароль для передачи на сервер
	 * 
	 * @return string
	 */
	protected function getSecure() {
		return md5($this->date . '&' . $this->secure_password);
	}
	
	public function loadComponent($component) {
		return new $component($this->account, $this->secure_password, $this->date);
	}
	
	public function sendData(exchange $component) {
		
		$action = $this->base_url . $component->getMethod();
		$parser = method_exists($component, 'getParser') ? $component->getParser() : new parser_xml();
		
		$response = $this->getURL($action, $parser, $component->getData());

		// Обнуление массива ошибок
		$this->error = array();
		
		return method_exists($component, 'prepareResponse') ? $component->prepareResponse($response, $this->error) : $response;
	}
	
	protected function getURL($url, response_parser $parser, $data = array()) {
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	
		if (!empty($data)) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		
		$out = curl_exec($ch);
		curl_close($ch);

		$parser->setData($out);

		//Инициализация лога
		//$hits_log = new Log('hits_module.log');
		return $parser->getData();
	}
	
	public function getMethod() {
		return $this->method;
	}
	
	private function init() {
		
		spl_autoload_register(array($this, 'autoloader'));
		spl_autoload_extensions('.php');
		
		self::$ext_dir = dirname(__FILE__);
	}
	
	static public function autoloader($class_name) {
		
		if (class_exists($class_name)) return;
		
		foreach (array('', DIRECTORY_SEPARATOR . 'components') as $folder) {
			
			foreach (array('class', 'interface') as $type) {
				
				$file_name = self::$ext_dir . $folder . DIRECTORY_SEPARATOR . $type . '.' . $class_name . '.php';
				
				if (file_exists($file_name)) {
					return require_once $file_name;
				}
			}
			
		}
		
		throw new Exception($file_name  . ' not found.');
	}
	
	protected function xml_entities($string) {
		return strtr(
			$string, 
			array(
				"<" => "&lt;",
				">" => "&gt;",
				'"' => "&quot;",
				"'" => "&apos;",
				"&" => "&amp;",
			)
		);
	}
}

?>