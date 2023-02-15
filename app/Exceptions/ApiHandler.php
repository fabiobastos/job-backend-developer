<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use Throwable;

trait ApiHandler {

    /**
     * Tratamento de erros personalizados
     *
     * @param Throwable $exception
     * @return \Illuminate\Http\Response Response
     */
    public function tratarErros(Throwable $exception): Response|false
    {
        if($exception instanceof ModelNotFoundException){
            return $this->modelNotFoundException();
        }
        if($exception instanceof ValidationException){
            return $this->validationException($exception);
        }

        return false;
    }

    /**
     * Retorna erro quando produto não é encontrado
     *
     * @return Response
     */
    public function modelNotFoundException(): Response
    {
        return $this->respostaPadrao(
            'product-not-found',
            "The system could not find the product that you're searching",
            404
        );
    }

    /**
     * Retorna erros de validação dos dados
     *
     * @param ValidationException $e
     * @return Response
     */
    public function validationException(ValidationException $e): Response
    {
        return $this->respostaPadrao(
            'validation-error',
            "The data you sent was invalid",
            400,
            $e->errors()
        );
    }

    /**
     * Padroniza a resposta de erros da API
     *
     * @param string $code
     * @param string $message
     * @param integer $status
     * @param array|null $errors
     * @return Response
     */
    public function respostaPadrao(
        string $code,
        string $message,
        int $status,
        array $errors = null
    ): Response
    {
        $dadosResposta = [
            'code' => $code,
            'message' => $message,
            'status' => $status
        ];

        if($errors){
            $dadosResposta = $dadosResposta + ['errors' => $errors];
        }

        return response($dadosResposta,$status);
    }
}
