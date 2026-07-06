<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiDocsController extends Controller
{
    /**
     * Display the REST API documentation page.
     */
    public function index()
    {
        return view('api.docs');
    }
}
