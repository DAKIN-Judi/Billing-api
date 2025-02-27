<?php

namespace App\Http\Controllers;

/**
 *  @OA\Info(
 *      version="1.0.0",
 *      title="Billing api documentation",
 *      description="Billing api documentation",
 *      @OA\Contact(
 *          email="d.j.bidossessi@mail.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licences/LICENCE-2.0.html"
 *      )
 *  ),
 *
 *  @OA\SecurityScheme(
 *     type="http",
 *     description="Use a Bearer token to access this API",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth",
 * ),
 *
 *
 *  @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 *  )
 */
abstract class Controller
{
    //
}
