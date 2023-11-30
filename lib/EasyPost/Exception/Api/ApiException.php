<?php

namespace EasyPost\Exception\Api;

use EasyPost\Exception\General\EasyPostException;
use EasyPost\FieldError;
use Exception;

/**
 * @package EasyPost
 * @property string|null $code
 * @property FieldError[]|null $errors
 * @property string $message
 * @property string|null $httpBody
 * @property int|null $httpStatus
 * @property mixed $jsonBody
 */
class ApiException extends EasyPostException
{
    public $code; // @phpstan-ignore-line
    public $errors; // @phpstan-ignore-line
    protected $message; // @phpstan-ignore-line
    private ?string $httpBody;
    private ?int $httpStatus;
    private mixed $jsonBody;

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
        $this->errors = null;
        $this->code = null;

        try {
            $this->jsonBody = isset($httpBody) ? json_decode($httpBody, true) : null;

            // Set `errors` property
            if (isset($this->jsonBody) && !empty($this->jsonBody['error']['errors'])) {
                $this->errors = $this->jsonBody['error']['errors'];
            }

            // Set `code` property
            if (isset($this->jsonBody) && !empty($this->jsonBody['error']['code'])) {
                $this->code = $this->jsonBody['error']['code'];
            }
        } catch (Exception $e) { // @phpstan-ignore-line
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
