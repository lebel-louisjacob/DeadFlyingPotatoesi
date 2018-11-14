<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
/**
 * @SWG\Swagger(
 *     schemes={"http","https"},
 *     host="165.227.43.142:8000",
 *     basePath="/api",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="RevolvAir API",
 *         description="Improve air quality for the comunity",
 *         termsOfService="",
 *         @SWG\Contact(
 *             email="gsimard@revolvair.tk"
 *         ),
 *         @SWG\License(
 *             name="MIT",
 *             url="http://atomrace.tk"
 *         )
 *     ),
 *     @SWG\ExternalDocumentation(
 *         description="Find out more about Air analysis for health",
 *         url="http://atomrace.tk"
 *     ),
 *   @SWG\SecurityScheme(
 *      securityDefinition="revolvair_auth",
 *      type="oauth2",
 *      tokenUrl="/oauth/token",
 *      flow="password",
 *      scopes={
 *          "admin": "admin scope",
 *          "station-owner": "station-owner scope",
 *     }
 *     ),
 *   )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
