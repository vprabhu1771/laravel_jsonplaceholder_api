<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserApi;
use Illuminate\Support\Facades\Http;

class FetchApiUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:api-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store users from JSONPlaceholder API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::get('https://jsonplaceholder.typicode.com/users');

        if ($response->successful()) {
            $users = $response->json();
            foreach ($users as $user) {
                UserApi::updateOrCreate(
                    ['email' => $user['email']],
                    [
                        'name' => $user['name'],
                        'street' => $user['address']['street'],
                        'city' => $user['address']['city'],
                    ]
                );
            }
            $this->info('Users fetched and stored successfully!');
        } 
        else 
        {
            $this->error('Failed to fetch users from API');
        }
    }
}
