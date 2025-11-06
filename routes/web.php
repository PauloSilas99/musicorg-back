<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'MusicOrg API',
        'version' => '1.0',
        'endpoints' => [
            'docs' => 'Consulte o README.md para documentação completa',
        ],
    ]);
});
