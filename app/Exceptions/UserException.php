<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom user exception with factory methods
 */
class UserException extends Exception
{
    /**
     * @param string $message Error message
     * @param string $errorCode Error code for API consumers
     * @param int $statusCode HTTP status code
     */
    public function __construct(
        string $message,
        public readonly string $errorCode,
        public readonly int $statusCode = 400
    ) {
        parent::__construct($message);
    }

    /**
     * Email already exists exception
     */
    public static function emailAlreadyExists(): self
    {
        return new self(
            'Este endereço de e-mail já está em uso.',
            'EMAIL_JA_EXISTE',
            422
        );
    }

    /**
     * Invalid boolean value exception
     */
    public static function invalidBooleanValue(string $field): self
    {
        return new self(
            "O campo {$field} deve ser um valor booleano (true ou false).",
            'VALOR_BOOLEANO_INVALIDO',
            422
        );
    }

    /**
     * User not found exception
     */
    public static function notFound(): self
    {
        return new self(
            'Usuário não encontrado.',
            'USUARIO_NAO_ENCONTRADO',
            404
        );
    }

    /**
     * Invalid credentials exception
     */
    public static function invalidCredentials(): self
    {
        return new self(
            'Credenciais inválidas.',
            'CREDENCIAIS_INVALIDAS',
            401
        );
    }

    /**
     * Convert exception to JSON response
     */
    public function toJsonResponse(): array
    {
        return [
            'erro' => true,
            'mensagem' => $this->getMessage(),
            'codigo' => $this->errorCode,
            'status' => $this->statusCode
        ];
    }
}

