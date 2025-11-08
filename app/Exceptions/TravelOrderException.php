<?php

namespace App\Exceptions;

use Exception;

/**
 * Custom travel order exception with factory methods
 */
class TravelOrderException extends Exception
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
     * Travel order not found exception
     */
    public static function notFound(): self
    {
        return new self(
            'Pedido de viagem não encontrado.',
            'PEDIDO_NAO_ENCONTRADO',
            404
        );
    }

    /**
     * Unauthorized access to travel order exception
     */
    public static function unauthorized(): self
    {
        return new self(
            'Você não tem permissão para acessar este pedido de viagem.',
            'SEM_PERMISSAO',
            403
        );
    }

    /**
     * Cannot modify travel order exception
     */
    public static function cannotModify(): self
    {
        return new self(
            'Este pedido de viagem não pode ser modificado. Apenas pedidos com status "solicitado" podem ser alterados.',
            'NAO_PODE_MODIFICAR',
            400
        );
    }

    /**
     * Invalid status transition exception
     */
    public static function invalidStatus(string $currentStatus): self
    {
        return new self(
            "Transição de status inválida. O pedido está com status '{$currentStatus}' e não pode ser alterado.",
            'STATUS_INVALIDO',
            400
        );
    }

    /**
     * Invalid dates exception
     */
    public static function invalidDates(): self
    {
        return new self(
            'Data de retorno deve ser igual ou posterior à data de partida.',
            'DATAS_INVALIDAS',
            422
        );
    }

    /**
     * Admin only operation exception
     */
    public static function adminOnly(): self
    {
        return new self(
            'Esta operação só pode ser realizada por administradores.',
            'SOMENTE_ADMIN',
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

