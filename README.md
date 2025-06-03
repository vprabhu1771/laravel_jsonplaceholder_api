# laravel_jsonplaceholder_api

# Laravel External User API Integration
## task4

This project integrates a third-party API (https://jsonplaceholder.typicode.com/users) to fetch and manage external users in a Laravel app.

## Features

- Fetch user data from JSONPlaceholder API
- Display user list with name, email, and address
- Search users by name
- Handle API fetch errors
- Store users in database
- Sync periodically using Laravel Scheduler


## Installation

Follow these steps to install and run the Laravel project locally:

1. Clone the repository and navigate into it:
  ``` git clone https://github.com/balaji-11-udayasuriyan/LaravelApi```
   ```cd LaravelApi```

2. Install PHP dependencies using Composer:
   ```composer install```

3. Setup environment configuration:
   ```cp .env.example .env```
   Then open the .env file and update your database credentials and other settings as needed.
4. store the api into the database
   ```php artisan fetch:api-users```


6. Generate application key:
   ```php artisan key:generate```

7. Run database migrations:
   ```php artisan migrate```

8. Start the development server:
  ``` php artisan serve```

9. Access the app:
   ```Open your browser and visit http://localhost:8000```


To complete your scheduled command `fetch:api-users`, you’ll need to create a custom Artisan command class. Here's how to do it step by step:

---

## ✅ Step 1: Create the Artisan Command

In your terminal, run:

```bash
php artisan make:command FetchApiUsers
```

---

## ✅ Step 2: Update the Command Logic

Edit the generated file at `app/Console/Commands/FetchApiUsers.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Userapi;

class FetchApiUsers extends Command
{
    protected $signature = 'fetch:api-users';
    protected $description = 'Fetch users from JSONPlaceholder API and store/update them in DB';

    public function handle()
    {
        $this->info('Fetching users from API...');

        try {
            $response = Http::get('https://jsonplaceholder.typicode.com/users');

            if ($response->successful()) {
                foreach ($response->json() as $apiUser) {
                    UserApi::updateOrCreate(
                        ['email' => $apiUser['email']],
                        [
                            'name' => $apiUser['name'],
                            'password' => bcrypt('default123'), // placeholder
                            'role' => 'api_user',
                        ]
                    );
                }

                $this->info('Users updated successfully!');
            } else {
                $this->error('Failed to fetch users. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('Error fetching users: ' . $e->getMessage());
        }

        return 0;
    }
}
```

---

## ✅ Step 3: Register the Command

In `app/Console/Kernel.php`, the `schedule()` part you've already done is correct:

```php
protected function schedule(Schedule $schedule): void
{
    $schedule->command('fetch:api-users')->hourly();
}
```

Make sure your `commands()` method loads the command (already done with `$this->load(__DIR__.'/Commands');`).

---

## ✅ Step 4: Test the Command

To manually run the command:

```bash
php artisan fetch:api-users
```

To test the scheduler (after configuring the cron job on your server):

```bash
php artisan schedule:run
```

---

## ✅ Bonus: Add Cron Entry to Server

Run this to open the crontab:

```bash
crontab -e
```

Add the following line to run Laravel's scheduler every minute:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

Let me know if you'd like to include a log system, email alerts on failure, or a test for this command.


Your command logic is looking great! You’re correctly fetching user data from the API and storing/updating it using `UserApi::updateOrCreate`. To make this fully functional, let’s ensure a few more pieces are in place.

---

## ✅ 1. Migration for `user_apis` Table

Run this to create a migration:

```bash
php artisan make:migration create_user_apis_table
```

Then, update the migration file:

```php
// database/migrations/xxxx_xx_xx_create_user_apis_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserApisTable extends Migration
{
    public function up()
    {
        Schema::create('user_apis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_apis');
    }
}
```

Then run:

```bash
php artisan migrate
```

---

## ✅ 2. Model: `UserApi.php`

Create the model if it doesn’t exist:

```bash
php artisan make:model UserApi
```

Then define the fillable properties:

```php
// app/Models/UserApi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApi extends Model
{
    protected $fillable = ['name', 'email', 'street', 'city'];
}
```

---

## ✅ 3. Final Recap of the Flow

You already have:

* The **command** to fetch and store users ✅
* The **scheduler** in `Kernel.php` to run `fetch:api-users` hourly ✅
* The **database table and model** now set up for `UserApi` ✅

You can now run manually:

```bash
php artisan fetch:api-users
```

Or let it run hourly via:

```bash
php artisan schedule:run
```

---

Let me know if you'd like to **display this user data in a Blade view**, or write a controller for it too!
