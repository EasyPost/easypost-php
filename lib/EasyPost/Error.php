<?php

class EasyPost_Error extends Exception {
  public function __construct($message=null, $httpStatus=null, $httpBody=null) {
    parent::__construct($message);
    $this->httpStatus = $httpStatus;
    $this->httpBody = $httpBody;
  }

  public function getHttpStatus() {
    return $this->httpStatus;
  }

  public function getHttpBody() {
    return $this->httpBody;
  }
}
