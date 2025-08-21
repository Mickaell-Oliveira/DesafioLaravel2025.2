<?php

// database/factories/AddressFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cep' => fake()->numerify('#####-###'),
            'logradouro' => fake()->streetName(),
            'numero' => fake()->buildingNumber(),
            'bairro' => fake()->randomElement(['Centro', 'Jardim Aurora', 'Vila Ideal', 'Bairro Novo', 'São Pedro','São Mateus']), // Faker não tem um provider de bairro brasileiro
            'cidade' => fake()->city(),
            'estado' => fake()->stateAbbr(),
            'complemento' => fake()->secondaryAddress(),
        ];
    }
}