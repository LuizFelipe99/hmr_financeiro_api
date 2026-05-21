<?php

namespace App\Http\Controllers;

use App\Models\ImportedUser;
use Illuminate\Http\JsonResponse;

class ImportedUserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(ImportedUser::all());
    }
}