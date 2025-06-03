<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
     public function index(Request $request)
    {
        try {
            $response = Http::get('https://jsonplaceholder.typicode.com/users');

            if ($response->successful()) {
                $users = $response->json();
                $search = $request->query('search');
                if ($search) {
                    $users = array_filter($users, fn($user) => stripos($user['name'], $search) !== false);
                }
                return view('users.index', ['users' => $users, 'search' => $search]);
            }

            return view('users.index', ['error' => 'Failed to fetch users from API']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return view('users.index', ['error' => 'An error occurred while fetching users.']);
        }
    }
}
