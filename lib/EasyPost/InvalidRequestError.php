<?php

class EasyPost_InvalidRequestError extends EasyPost_Error {
  public function __construct($message, $params=null, $http_status=null, $http_body=null) {
    parent::__construct($message, $http_status, $http_body);
    $this->params = $params;
  }
}
