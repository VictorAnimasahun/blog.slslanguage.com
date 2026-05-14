<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title'        => $title,
            'slug'         => Str::slug($title) . '-' . Str::random(4),
            'excerpt'      => fake()->paragraph(),
            'content'      => '<p>' . implode('</p><p>', fake()->paragraphs(4)) . '</p>',
            'status'       => 'draft',
            'published_at' => null,
            'author_id'    => User::factory(),
            'category_id'  => null,
        ];
    }
}
