<?php
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
Artisan::command('kobcode:admin', function () {
    $email = $this->ask('Adres e-mail administratora');
    if (! filter_var($email, FILTER_VALIDATE_EMAIL) || User::where('email', $email)->exists()) {
        $this->error('Niepoprawny e-mail lub konto już istnieje.'); return 1;
    }
    $name = $this->ask('Imię');
    $password = $this->secret('Hasło (minimum 12 znaków)');
    if (! $name || strlen($password ?? '') < 12) { $this->error('Podaj imię i hasło minimum 12 znaków.'); return 1; }
    $user = new User(['name'=>$name, 'email'=>$email, 'password'=>$password]);
    $user->is_admin = true; $user->save();
    $this->info('Konto utworzone. Zaloguj się na /admin.');
})->purpose('Utwórz administratora KOBCODE bez domyślnego hasła');

Artisan::command('kobcode:portfolio-drafts', function () {
    $this->call('db:seed', ['--class' => \Database\Seeders\PortfolioDraftSeeder::class]);
    $this->info('Szkice przygotowane. Istniejące opisy nie zostały nadpisane.');
})->purpose('Dodaj brakujące szkice Walbud i Olaya bez publikowania');
