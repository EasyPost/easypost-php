<?php

namespace EasyPost\Exception\Api;

use EasyPost\Exception\General\EasyPostException;

/**
 * @package EasyPost
 * @property string $code
 * @property FieldError[] $errors
 */
class ApiException extends EasyPostException
{
    public $code;
    public $errors;
    protected $message;
    private $httpBody;
    private $httpStatus;
    private $jsonBody;

    /**
     * ApiException constructor.
     *
     * @param string $message
     * @param int|null $httpStatus
     * @param string|null $httpBody
     */
    public function __construct(string $message = '', ?int $httpStatus = null, ?string $httpBody = null)
    {
        parent::__construct($message);
        $this->httpStatus = $httpStatus;
        $this->httpBody = $httpBody;

        try {
            $this->jsonBody = isset($httpBody) ? json_decode($httpBody, true) : null;

            // Setup `errors` property
            if (isset($this->jsonBody) && !empty($this->jsonBody['error']['errors'])) {
                $this->errors = $this->jsonBody['error']['errors'];
            } else {
                $this->errors = null;
            }

            // Setup `code` property
            if (isset($this->jsonBody) && !empty($this->jsonBody['error']['code'])) {
                $this->code = $this->jsonBody['error']['code'];
            } else {
                $this->code = null;
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
    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    /**
     * Get the HTTP body.
     *
     * @return string
     */
    public function getHttpBody(): string
    {
        return $this->httpBody;
    }

    /**
     * Pretty print the error.
     *
     * @return void
     */
    public function prettyPrint(): void
    {
        print($this->code . ' (' . $this->getHttpStatus() . '): ' .
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
