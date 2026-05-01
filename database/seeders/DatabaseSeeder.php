<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;

class WelcomePostSeeder extends Seeder
{
    public function run()
    {
        // Create user with correct fields
        $user = User::firstOrCreate(
            ['email' => 'team@slsblog.com'],
            [
                'first_name' => 'SLS',
                'last_name' => 'Team',
                'display_name' => 'SLS Team',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now()
            ]
        );

        // Create category
        $category = Category::firstOrCreate(
            ['slug' => 'announcements'],
            [
                'name' => 'Announcements',
                'description' => 'Official blog announcements and updates'
            ]
        );

        // Build the content using heredoc syntax (cleaner for HTML)
        $content = <<<'HTML'
<h1>Welcome to the SLS Blog: Crafting Brighter Futures Together</h1>

<p>We are thrilled to welcome you to the official Scholarly Language Services (SLS) Blog – your new destination for insights, resources, and inspiration on all things related to academic excellence, language mastery, and educational advancement.</p>

<h2>Who We Are</h2>

<p>Scholarly Language Services is dedicated to empowering students, educators, and lifelong learners through high-quality language support and educational resources. Our mission is simple yet profound: to craft brighter futures by breaking down language barriers and making academic success accessible to everyone.</p>

<h2>What You'll Find Here</h2>

<p>Our blog is designed to be your go-to resource for:</p>

<h3>Academic Writing Tips</h3>
<p>From crafting compelling thesis statements to mastering citation styles, we'll share practical strategies to elevate your academic writing game.</p>

<h3>Language Learning Insights</h3>
<p>Whether you're learning English as a second language or refining your professional communication skills, our expert tips will guide your journey.</p>

<h3>Student Success Stories</h3>
<p>Real stories from real students who have overcome challenges and achieved their academic goals. Let their journeys inspire yours.</p>

<h3>Educational Resources</h3>
<p>Curated lists of tools, books, courses, and materials that can support your learning objectives.</p>

<h3>Industry Trends</h3>
<p>Stay informed about the latest developments in education, language services, and academic publishing.</p>

<h2>Why We Started This Blog</h2>

<p>Education transforms lives. We've witnessed countless students struggle not because they lack intelligence or dedication, but because language barriers stand in their way. Through this blog, we aim to:</p>

<ul>
<li><strong>Democratize knowledge</strong> by sharing free, accessible educational content</li>
<li><strong>Build community</strong> among learners, educators, and language enthusiasts</li>
<li><strong>Share expertise</strong> accumulated through years of helping students succeed</li>
<li><strong>Inspire action</strong> by showcasing what's possible when barriers are removed</li>
</ul>

<h2>Join Our Community</h2>

<p>This blog is more than a one-way conversation. We encourage you to leave comments, suggest topics, and engage with fellow readers.</p>

<p>Your voice matters here. Whether you're a student navigating your first research paper, an educator seeking new teaching strategies, or a professional looking to enhance your communication skills – you belong in this community.</p>

<h2>Looking Ahead</h2>

<p>In the coming weeks and months, expect regular posts covering step-by-step guides for common academic challenges, interviews with successful students and educators, and deep dives into specific language learning techniques.</p>

<p>Thank you for being here at the beginning of this journey. Here's to brighter futures, one word at a time.</p>

<p><strong>Welcome to the SLS Blog!</strong></p>
HTML;

        // Create the welcome post
        $post = Post::create([
            'title' => 'Welcome to the SLS Blog: Crafting Brighter Futures Together',
            'slug' => 'welcome-to-sls-blog',
            'excerpt' => 'We are thrilled to welcome you to the official Scholarly Language Services (SLS) Blog – your new destination for insights, resources, and inspiration.',
            'content' => $content,
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->command->info('✅ Welcome post created successfully!');
        $this->command->info("📝 Post ID: {$post->id}");
        $this->command->info("👤 User: {$user->display_name} ({$user->email})");
        $this->command->info("📂 Category: {$category->name}");
    }
}