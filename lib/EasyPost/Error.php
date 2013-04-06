<?php

class EasyPost_Error extends Exception {
  public function __construct($message=null, $http_status=null, $http_body=null) {
    parent::__construct($message);
    $this->http_status = $http_status;
    $this->http_body = $http_body;
  }

  public function getHttpStatus() {
    return $this->http_status;
  }

  public function getHttpBody() {
    return $this->http_body;
  }
}
