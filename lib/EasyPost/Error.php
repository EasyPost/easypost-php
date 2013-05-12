<?php

namespace EasyPost;

class Error extends \Exception
{
    public function __construct($message = null, $httpStatus = null, $httpBody = null)
    {
        parent::__construct($message);
        $this->httpStatus = $httpStatus;
        $this->httpBody = $httpBody;

        try {
            $this->jsonBody = json_decode($httpBody, true);
            if (isset($this->jsonBody) && !empty($this->jsonBody['error']['param'])) {
                $this->param = $this->jsonBody['error']['param'];
            } else {
                $this->param = null;
            }
        } catch (\Exception $e) {
            $this->jsonBody = null;
        }
    }

    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    public function getHttpBody()
    {
        return $this->httpBody;
    }
}
