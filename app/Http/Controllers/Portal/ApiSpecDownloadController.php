<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiSpecDownloadController extends Controller
{
    public function openapi(): StreamedResponse
    {
        return Storage::disk('local')->download(
            'api-docs/openapi/openapi.json',
            'hfresh-openapi.json'
        );
    }

    public function postman(): StreamedResponse
    {
        return Storage::disk('local')->download(
            'api-docs/postman/collection.json',
            'hfresh-postman-collection.json'
        );
    }
}
