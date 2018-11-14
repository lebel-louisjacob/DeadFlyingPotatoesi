<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidCredentialException extends RuntimeException
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
            'error' => 'Invalid credential'
        ], 404);
    }
}
