<?php

namespace App;

trait ErrorResponseTrait
{
    /**
     * Formate une réponse JSON d'erreur.
     *
     * @param string $message Le message d'erreur
     * @param int $statusCode Le code de statut HTTP
     * @param mixed $errors Les détails supplémentaires sur les erreurs
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($message = 'An error occurred', $statusCode = 500, $errors = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
