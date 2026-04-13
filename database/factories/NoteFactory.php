<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'parent_id' => null,
            'title'     => $this->faker->sentence(3),
            'content'   => $this->faker->paragraphs(2, true),
            'tags'      => [],
            'position'  => 0,
        ];
    }

    public function withTags(array $tags): static
    {
        return $this->state(['tags' => $tags]);
    }

    public function child(Note $parent, int $position = 0): static
    {
        return $this->state([
            'user_id'   => $parent->user_id,
            'parent_id' => $parent->id,
            'position'  => $position,
        ]);
    }
}
