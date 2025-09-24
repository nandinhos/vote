<?php

namespace App\Exceptions;

use Exception;

class VotingException extends Exception
{
    /**
     * Create a new voting exception instance.
     */
    public function __construct(string $message = 'Erro na votação', int $code = 422, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render()
    {
        return back()->withErrors(['error' => $this->getMessage()]);
    }

    /**
     * Exception for when user already voted for a photo.
     */
    public static function alreadyVoted(): self
    {
        return new self('Você já votou nesta foto.');
    }

    /**
     * Exception for when user has reached vote limit.
     */
    public static function voteLimit(): self
    {
        return new self('Você já atingiu o limite máximo de 10 votos.');
    }

    /**
     * Exception for when project is inactive.
     */
    public static function inactiveProject(): self
    {
        return new self('Este projeto não está mais ativo para votação.');
    }

    /**
     * Exception for when user hasn't voted for a photo.
     */
    public static function notVoted(): self
    {
        return new self('Você não votou nesta foto.');
    }

    /**
     * Exception for when photo doesn't exist.
     */
    public static function photoNotFound(): self
    {
        return new self('A foto selecionada não existe.');
    }
}
