<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom notification exception with factory methods
 */
class NotificationException extends Exception
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
     * Notification not found exception
     */
    public static function notFound(): self
    {
        return new self(
            'Notificação não encontrada.',
            'NOTIFICACAO_NAO_ENCONTRADA',
            404
        );
    }

    /**
     * Unauthorized access to notification exception
     */
    public static function unauthorized(): self
    {
        return new self(
            'Você não tem permissão para acessar esta notificação.',
            'SEM_PERMISSAO',
            403
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


