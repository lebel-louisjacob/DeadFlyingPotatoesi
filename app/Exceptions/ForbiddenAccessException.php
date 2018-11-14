<?php
/**
 * Created by PhpStorm.
 * User: cedto
 * Date: 2018-02-14
 * Time: 12:00 PM
 */

namespace App\Exceptions;

use RuntimeException;

class ForbiddenAccessException extends RuntimeException
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json([
            'error' => 'Forbidden Access'
        ], 403);
    }
}