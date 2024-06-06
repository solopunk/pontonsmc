<?php

namespace App;

trait JsonResponseTrait
{
    /**
     * Formate une réponse JSON réussie.
     *
     * @param mixed $data Les données à retourner
     * @param string $message Le message de succès
     * @param int $statusCode Le code de statut HTTP
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data, $message = 'Request was successful', $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'return' => $data,
        ], $statusCode);
    }
}
