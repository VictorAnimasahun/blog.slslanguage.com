<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_id'     => Post::factory(),
            'user_id'     => null,
            'guest_name'  => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'content'     => fake()->paragraph(),
            'status'      => 'pending',
            'ip_address'  => fake()->ipv4(),
        ];
    }
}
