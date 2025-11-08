<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom authentication exception with factory methods
 */
class AuthenticationException extends Exception
{
    /**
     * @param string $message Error message
     * @param string $errorCode Error code for API consumers
     * @param int $statusCode HTTP status code
     */
    public function __construct(
        string $message,
        public readonly string $errorCode,
        public readonly int $statusCode = 401
    ) {
        parent::__construct($message);
    }

    /**
     * Invalid credentials exception
     */
    public static function invalidCredentials(): self
    {
        return new self(
            'Credenciais inválidas. Verifique seu email e senha.',
            'CREDENCIAIS_INVALIDAS'
        );
    }

    /**
     * Token expired exception
     */
    public static function tokenExpired(): self
    {
        return new self(
            'Seu token expirou. Por favor, faça login novamente.',
            'TOKEN_EXPIRADO'
        );
    }

    /**
     * Invalid token exception
     */
    public static function tokenInvalid(): self
    {
        return new self(
            'Token inválido. Por favor, faça login novamente.',
            'TOKEN_INVALIDO'
        );
    }

    /**
     * Unauthorized access exception
     */
    public static function unauthorized(): self
    {
        return new self(
            'Você não está autenticado. Por favor, faça login.',
            'NAO_AUTENTICADO'
        );
    }

    /**
     * Token not provided exception
     */
    public static function tokenNotProvided(): self
    {
        return new self(
            'Token não fornecido. Por favor, faça login.',
            'TOKEN_NAO_FORNECIDO'
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

