<?php

namespace Database\Factories;

use Domain\Models\Todo;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Todo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'category' => fake()->randomElement(['Private', 'Work', 'Learning']),
            'priority' => fake()->randomElement(['Low', 'Medium', 'High']),
            'status' => fake()->randomElement(['Todo', 'In Progress', 'Done']),
            'jira_issue' => fake()->optional()->bothify('TODO-####'),
            'due_date' => fake()->optional(0.7)->dateTimeBetween('now', '+1 year')?->format('Y-m-d'),
        ];
    }
}
