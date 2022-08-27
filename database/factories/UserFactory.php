<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/



$factory->define(User::class, function (Faker $faker) {
    $inputArray = [
        '1',
        '2'
    ];
    $roles = [
        '1',
        '2'
    ];
    $status = [
        '1',
        '0'
    ];
    return [
        'nombre' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'genero' => Arr::random($inputArray),
        'telefono' => $faker->phoneNumber,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
        'roles_id' => Arr::random($roles), //rol de superusuario
        'status' => Arr::random($status)
    ];
});