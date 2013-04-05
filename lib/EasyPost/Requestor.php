<?php

class EasyPost_Requestor {
  public $apiKey;

  public function __construct($apiKey=null) {
    $this->_apiKey = $apiKey;
  }

  public static function apiUrl($url='') {
    $apiBase = EasyPost::$apiBase;
    return "$apiBase$url";
  }

  public static function utf8($value) {
    if (is_string($value) && mb_detect_encoding($value, "UTF-8", TRUE) != "UTF-8")
      return utf8_encode($value);
    else
      return $value;
  }

  private static function _encodeObjects($d) {
    if ($d instanceof EasyPost_Resource) {
      return self::utf8($d->id);
    } else if ($d === true) {
      return 'true';
    } else if ($d === false) {
      return 'false';
    } else if (is_array($d)) {
      $res = array();
      foreach ($d as $k => $v)
      	$res[$k] = self::_encodeObjects($v);
      return $res;
    } else {
      return self::utf8($d);
    }
  }

  public static function encode($arr, $prefix=null) {
    if (!is_array($arr)) {
      return $arr;
    }

    $r = array();
    foreach ($arr as $k => $v) {
      if (is_null($v)) {
        continue;
      }

      if ($prefix && $k && !is_int($k)) {
        $k = $prefix."[".$k."]";
      } else if ($prefix) {
        $k = $prefix."[]";
      }

      if (is_array($v)) {
        $r[] = self::encode($v, $k, true);
      } else {
        $r[] = urlencode($k)."=".urlencode($v);
      }
    }

    return implode("&", $r);
  }

  public function request($method, $url, $params=null) {
    if (!$params) {
      $params = array();
    }
    list($rbody, $rcode, $myApiKey) = $this->_requestRaw($method, $url, $params);
    $response = $this->_interpretResponse($rbody, $rcode);
    return array($response, $myApiKey);
  }

  public function handleApiError($rbody, $rcode, $response) {
    if(!is_array($response) || !isset($response['error'])) {
      throw new EasyPost_ApiError("Invalid response object from API: {$rbody} (HTTP response code was {$rcode})", $rcode, $rbody, $response);
    }
    $error = $response['error'];
    switch ($rcode) {
    case 400:
    case 404:
      throw new EasyPost_InvalidRequestError(isset($error['message']) ? $error['message'] : null,
                                            isset($error['param']) ? $error['param'] : null,
                                            $rcode, $rbody, $response);
    case 401:
      throw new EasyPost_AuthenticationError(isset($error['message']) ? $error['message'] : null, $rcode, $rbody, $response);
    default:
      throw new EasyPost_ApiError(isset($error['message']) ? $error['message'] : null, $rcode, $rbody, $response);
    }
  }

  private function _requestRaw($method, $url, $params) {
    $myApiKey = $this->_apiKey;
    if (!$myApiKey) {
      $myApiKey = EasyPost::$apiKey;
    }
    if (!$myApiKey) {
      throw new EasyPost_AuthenticationError('No API key provided. Set your API key using "EasyPost::setApiKey(<API-KEY>)". See https://geteasypost.com/docs for details, or email contact@geteasypost.com if you have any questions.');
    }
    $absUrl = $this->apiUrl($url);
    $params = self::_encodeObjects($params);
    $langVersion = phpversion();
    $uname = php_uname();
    $ua = array('bindings_version' => EasyPost::VERSION,
		'lang' => 'php',
		'lang_version' => $langVersion,
		'publisher' => 'easypost',
		'uname' => $uname);
    $headers = array('X-EasyPost-Client-User-Agent: ' . json_encode($ua),
		  'User-Agent: EasyPost/v1 PhpBindings/' . EasyPost::VERSION,
      "Authorization: Bearer {$myApiKey}");
    if (EasyPost::$apiVersion) {
      $headers[] = 'EasyPost-Version: ' . EasyPost::$apiVersion;
    }
    list($rbody, $rcode) = $this->_curlRequest($method, $absUrl, $headers, $params, $myApiKey);

    // TODO: remove this
    //echo "Raw Response:"."\n";
    //print_r(array($rbody, $rcode, $myApiKey));

    return array($rbody, $rcode, $myApiKey);
  }

  private function _interpretResponse($rbody, $rcode) {
    try {
      $response = json_decode($rbody, true);
    } catch (Exception $e) {
      throw new EasyPost_ApiError("Invalid response body from API: {$rbody} (HTTP response code was {$rcode})", $rcode, $rbody);
    }

    if ($rcode < 200 || $rcode >= 300) {
      $this->handleApiError($rbody, $rcode, $response);
    }
    return $response;
  }

  private function _curlRequest($method, $absUrl, $headers, $params, $myApiKey) {
    $curl = curl_init();
    $method = strtolower($method);
    $opts = array();

    // method
    if ($method == 'get') {
      $opts[CURLOPT_HTTPGET] = 1;
      if (count($params) > 0) {
      	$encoded = self::encode($params);
      	$absUrl = "$absUrl?$encoded";
      }
    } else if ($method == 'post') {
      $opts[CURLOPT_POST] = 1;
      $opts[CURLOPT_POSTFIELDS] = self::encode($params);
    } else if ($method == 'delete')  {
      $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
      if (count($params) > 0) {
      	$encoded = self::encode($params);
      	$absUrl = "{$absUrl}?{$encoded}";
      }
    } else {
      throw new EasyPost_ApiError("Unrecognized method {$method}");
    }

    // url
    $absUrl = self::utf8($absUrl);

    // TODO: remove this
    //echo($absUrl . "\n\n");

    $opts[CURLOPT_URL] = $absUrl;

    $opts[CURLOPT_RETURNTRANSFER] = true;
    $opts[CURLOPT_CONNECTTIMEOUT] = 30;
    $opts[CURLOPT_TIMEOUT] = 80;
    $opts[CURLOPT_HTTPHEADER] = $headers;
    
    curl_setopt_array($curl, $opts);
    $rbody = curl_exec($curl);

    $errno = curl_errno($curl);

    if ($rbody === false) {
      $errno = curl_errno($curl);
      $message = curl_error($curl);
      curl_close($curl);
      $this->handleCurlError($errno, $message);
    }

    $rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    return array($rbody, $rcode);
  }

  public function handleCurlError($errno, $message) {
    $apiBase = EasyPost::$apiBase;
    switch ($errno) {
      case CURLE_COULDNT_CONNECT:
      case CURLE_COULDNT_RESOLVE_HOST:
      case CURLE_OPERATION_TIMEOUTED:
        $msg = "Could not connect to EasyPost ({$apiBase}).  Please check your internet connection and try again.  If this problem persists please let us know at contact@geteasypost.com.";
        break;
      case CURLE_SSL_CACERT:
      case CURLE_SSL_PEER_CERTIFICATE:
        $msg = "Could not verify EasyPost's SSL certificate.  Please make sure that your network is not intercepting certificates.  (Try going to {$apiBase} in your browser.)  If this problem persists, let us know at contact@geteasypost.com.";
        break;
      default:
        $msg = "Unexpected error communicating with EasyPost.  If this problem persists, let us know at contact@easypost.com.";
    }

    $msg .= "\n\n(Network error [errno {$errno}]: {$message})";
    throw new EasyPost_ApiConnectionError($msg);
  }
}
