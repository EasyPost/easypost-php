<?php

namespace EasyPost;

class Error extends \Exception
{
    /**
     * constructor
     *
     * @param string $message
     * @param int    $httpStatus
     * @param string $httpBody
     */
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

            if (isset($this->jsonBody) && !empty($this->jsonBody['error']['errors'])) {
                $this->errors = $this->jsonBody['error']['errors'];
            } else {
                $this->errors = null;
            }

            if (isset($this->jsonBody) && !empty($this->jsonBody['error']['code'])) {
                $this->ecode = $this->jsonBody['error']['code'];
            } else {
                $this->ecode = null;
            }
        } catch (\Exception $e) {
            $this->jsonBody = null;
        }
    }

    /**
     * get the HTTP status code
     *
     * @return int
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * get the HTTP body
     *
     * @return string
     */
    public function getHttpBody()
    {
        return $this->httpBody;
    }

    /**
     * print out the error
     *
     * @return void
     */
    public function prettyPrint()
    {
        print($this->ecode . " (" . $this->getHttpStatus() . "): " .
            $this->getMessage() . "\n");
        if (!empty($this->errors)) {
            print("Field errors:\n");
            foreach($this->errors as $field_error) {
                foreach($field_error as $k => $v) {
                    print("  " . $k . ": " . $v . "\n");
                }
                print("\n");
            }
        }
    }
}
