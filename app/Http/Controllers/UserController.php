<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Show the form for editing the specified user resource.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
    }

    /**
     * Display the specified user resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified user resource in storage.
     */
    public function update(Request $request)
    {
        $user = $request->user();
    }

    /**
     * Remove the specified user resource from storage.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
    }
}
