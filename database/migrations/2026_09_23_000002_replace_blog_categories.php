<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

// Replaces the old ad-hoc category list with the fixed 5-category set the
// instructor asked for. Any post sitting under a category that's being
// scrapped gets moved to "News/SLS Updates" first -- CategoryController::destroy()
// already refuses to delete a category with posts attached, so a category
// can only actually disappear here once nothing points at it anymore.
return new class extends Migration
{
    private const KEEP = [
        'News/SLS Updates',
        'IELTS & CELPIP',
        'Language for Impact',
        'Professional Development',
        'Language & Communication',
    ];

    public function up(): void
    {
        foreach (self::KEEP as $name) {
            if (! Category::where('name', $name)->exists()) {
                Category::create([
                    'name' => $name,
                    'slug' => $this->uniqueSlug($name),
                ]);
            }
        }

        $fallback = Category::where('name', 'News/SLS Updates')->first();

        $obsolete = Category::whereNotIn('name', self::KEEP)->get();
        foreach ($obsolete as $category) {
            Post::where('category_id', $category->id)->update(['category_id' => $fallback->id]);
            $category->delete();
        }
    }

    public function down(): void
    {
        // The old category list isn't recoverable (names weren't recorded
        // anywhere but the rows themselves), so this only removes the new
        // fixed set -- posts stay pointed at "News/SLS Updates" rather than
        // being silently uncategorized.
        Category::whereIn('name', self::KEEP)->delete();
    }

    private function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
};
