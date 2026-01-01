<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresScope;

#[RequiresScope(['example:read'])]
class ExampleScopedController
{
    public function index(): JsonResponse
    {
        return response()->json('This is a scoped example response.');
    }

    public function show(): JsonResponse
    {
        return response()->json('This is another scoped example response.');
    }

    public function store(): JsonResponse
    {
        return response()->json('This is a created scoped example response.', 201);
    }

    #[RequiresScope(['example:update'])]
    public function update(): JsonResponse
    {
        return response()->json('This is an updated scoped example response.', 202);
    }

    #[RequiresScope(['example:delete'])]
    public function destroy(): JsonResponse
    {
        return response()->json('', 204);
    }
}