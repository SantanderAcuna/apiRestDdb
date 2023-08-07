<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tarea>
 */
class TareaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enumOptions
            = [
                'pendiente',
                'en progreso',
                'completada'
            ];
        return [
            'titulo' => fake()->name(),
            'descripcion' => fake()->text(),
            'fechha_inicio' => fake()->date(),
            'fechha_finalizacion' => fake()->date(),
            'archivo_adjunto' => fake()->image(),
            'estado' => fake()->randomElement($enumOptions),
        ];
    }
}
