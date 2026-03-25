<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="Mama Mia Pizza API",
 *     version="1.0.0",
 *     description="Документация REST API проекта"
 *   ),
 *   @OA\Server(
 *     url="http://localhost:81",
 *     description="Local environment"
 *   )
 * )
 *
 * @OA\PathItem(
 *   path="/api/ping",
 *   summary="Healthcheck endpoint"
 * )
 */
final class OpenApi
{
    // Только контейнер для корневых аннотаций
}
