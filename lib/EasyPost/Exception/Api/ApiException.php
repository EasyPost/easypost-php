<?php

namespace EasyPost\Exception\Api;

use EasyPost\Exception\General\EasyPostException;
use EasyPost\FieldError;
use Exception;

/**
 * @package EasyPost
 * @property string|null $code
 * @property array<FieldError|array<string>> $errors
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
        $this->errors = [];
        $this->code = null;

        $this->jsonBody = isset($httpBody) ? json_decode($httpBody, true) : null;

        // Set `errors` property
        if (isset($this->jsonBody) && !empty($this->jsonBody['error']['errors'])) {
            $this->errors = $this->jsonBody['error']['errors'];
        }

        // Set `code` property
        if (isset($this->jsonBody) && !empty($this->jsonBody['error']['code'])) {
            $this->code = $this->jsonBody['error']['code'];
        }
    }

    /**
     * Get the HTTP status code.
     *
     * @return int|null
     */
    public function getHttpStatus(): ?int
    {
        return $this->httpStatus;
    }

    /**
     * Get the HTTP body.
     *
     * @return string|null
     */
    public function getHttpBody(): ?string
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
        if (!empty($this->errors) && is_array($this->errors[0])) {
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
