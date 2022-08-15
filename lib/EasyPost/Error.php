<?php

namespace EasyPost;

/**
 * @package EasyPost
 * @property string $code
 * @property string $message
 * @property FieldError[] $errors
 */
class Error extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $message
     * @param int $httpStatus
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
     * Get the HTTP status code.
     *
     * @return int
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * Get the HTTP body.
     *
     * @return string
     */
    public function getHttpBody()
    {
        return $this->httpBody;
    }

    /**
     * Pretty print the error.
     *
     * @return void
     */
    public function prettyPrint()
    {
        print($this->ecode . ' (' . $this->getHttpStatus() . '): ' .
            $this->getMessage() . "\n");
        if (!empty($this->errors)) {
            print("Field errors:\n");
            foreach ($this->errors as $fieldError) {
                foreach ($fieldError as $k => $v) {
                    print('  ' . $k . ': ' . $v . "\n");
                }
                print("\n");
            }
        }
    }
}
