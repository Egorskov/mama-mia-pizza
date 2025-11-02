<?php


namespace App\Swagger\Paths;

use OpenApi\Annotations as OA;

/**
 * @OA\PathItem(
 *   path="/api/ping"
 * )
 *
 * @OA\Get(
 *   path="/api/ping",
 *   tags={"System"},
 *   summary="Ping API",
 *   @OA\Response(
 *     response=200,
 *     description="OK",
 *     @OA\JsonContent(
 *       @OA\Property(property="status", type="string", example="ok")
 *     )
 *   )
 * )
 */
final class PingPath
{
    // Класс-держатель аннотаций
}
