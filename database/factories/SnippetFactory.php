<?php

namespace Database\Factories;

use Domain\Models\Snippet;
use Illuminate\Database\Eloquent\Factories\Factory;

class SnippetFactory extends Factory
{
    protected $model = Snippet::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'code' => $this->faker->text(200),
            'language' => $this->faker->randomElement(['php', 'javascript', 'python', 'go', 'typescript']),
            'tags' => $this->faker->randomElements(['laravel', 'react', 'hooks', 'api', 'eloquent', 'testing'], $this->faker->numberBetween(1, 3)),
            'jira_issue' => $this->faker->optional()->bothify('JIRA-####'),
        ];
    }
}
