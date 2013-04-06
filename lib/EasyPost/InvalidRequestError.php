<?php

class EasyPost_InvalidRequestError extends EasyPost_Error {
  public function __construct($message, $params=null, $httpStatus=null, $httpBody=null) {
    parent::__construct($message, $httpStatus, $httpBody);
    $this->params = $params;
  }
}
