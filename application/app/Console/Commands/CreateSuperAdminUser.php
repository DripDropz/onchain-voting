<?php

namespace App\Console\Commands;

use Throwable;
use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-super-admin-user {--username=} {--email=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new super admin user account';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {

            User::factory([
                'name' => $this->option('username'),
                'email' => $this->option('email'),
                'password' => Hash::make($this->option('password')),
            ])->hasAttached(Role::where('name', RoleEnum::SUPER_ADMIN)->first())
                ->create();

            $this->info(sprintf(
                'Super admin %s (%s) successfully created',
                $this->option('username'),
                $this->option('email'),
            ));

        } catch (Throwable $exception) {

            $this->error(sprintf(
                'Error: %s on file %s at line #%d',
                $exception->getMessage(),
                basename($exception->getFile()),
                $exception->getLine(),
            ));

        }
    }
}
