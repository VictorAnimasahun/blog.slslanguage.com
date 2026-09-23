-- ============================================================
-- Recovery: re-insert "Latest Scoop" and "Update on the latest scoop"
--
-- Both posts were deleted by the "Save Changes actually deletes the post"
-- bug (nested <form> in admin/posts/edit.blade.php, fixed 2026-09-23,
-- commit a26e65e). Reconstructed from a full cached copy of each rendered
-- page, fetched shortly before they were deleted -- title, excerpt,
-- category, author, publish date, and the exact post content (including
-- both inline <img> tags) are all real, not placeholders.
--
-- This is plain SQL, not a Laravel artisan migration -- run it directly
-- against the live slslanguage_blog MySQL database (phpMyAdmin's Import,
-- or the mysql CLI). Idempotent: skips a post whose slug already exists,
-- so it's safe to run more than once.
--
-- Two things this file can't recover, because deleting a post also
-- deletes its featured image file from disk (PostController::destroy):
--   1. Both posts' FEATURED images are almost certainly gone from
--      storage/app/public/posts/ -- their filenames are included below only
--      so the row looks right; you'll likely need to re-upload each one
--      from the edit screen after this runs.
--   2. The INLINE images inside each post's content (the ones the <img>
--      tags below point at, under storage/app/public/posts/inline/) are
--      NOT touched by destroy() -- nothing deletes them when a post is
--      deleted -- so they were probably never removed and should still
--      work now that storage:link has been run. If any still show broken,
--      they need re-uploading through the editor same as the featured
--      images.
--
-- Run on LIVE only -- these two posts never existed locally.
-- ============================================================

-- 1. "Latest Scoop" (category: Announcement, published Sept 22 2026)
INSERT INTO posts (title, slug, excerpt, content, featured_image, status, category_id, author_id, published_at, created_at, updated_at)
SELECT
    'Latest Scoop',
    'latest-scoop',
    'Coming soon...Go check it out',
    '<h2><strong>Hey there...</strong></h2><p>The elephant in the room is ready to make a sound. </p><p><img src="/storage/posts/inline/bN2SHbAzH3LvSAkZ7xtxiZL397c6G5YmchAegYje.webp"></p><p>Wanna hear what he has to say?</p><p><img src="/storage/posts/inline/mXcWNXI7Kl5rAH0AWP6zD2MNc4LiS8Ikw397QQS0.png"></p>',
    'posts/uIhKSzKw4XS53d0dZy2MU1RF1m2n0kzancREpDUq.webp',
    'published',
    (SELECT id FROM categories WHERE slug = 'announcement' LIMIT 1),
    (SELECT id FROM users WHERE display_name = 'SLS Admin' LIMIT 1),
    '2026-09-22 12:00:00',
    '2026-09-22 12:00:00',
    '2026-09-22 12:00:00'
FROM (SELECT 1) AS _dummy
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE slug = 'latest-scoop');

-- 2. "Update on the latest scoop" (category: News, published Sept 22 2026)
INSERT INTO posts (title, slug, excerpt, content, featured_image, status, category_id, author_id, published_at, created_at, updated_at)
SELECT
    'Update on the latest scoop',
    'update-on-the-latest-scoop',
    'If you missed the first one, read on!',
    '<h1><strong>After an hour''s long press conference, the elephant has spoken. (Heading 1)</strong></h1><p><br></p><p>More details as we receive them. <a href="https://blog.slslanguage.com/blog/latest-scoop" rel="noopener noreferrer" target="_blank">Check this link out</a></p><p><br></p><ol><li>One (numbers/lists)</li><li>Two </li><li>Three</li></ol><blockquote>You and your economy</blockquote><blockquote>Testing how quote icon works here (")</blockquote><pre class="ql-syntax" spellcheck="false">Check this out - (arrow icons)
</pre><h2><br></h2><h2><strong>Are you ready? (Heading 2)</strong></h2><h3><strong>And Finally... (Heading 3)</strong></h3><p><br></p><p>Image still not loading.</p><p><br></p><p><img src="/storage/posts/inline/4wF35A7kFdaLnW8B1VRtGuiiKyZi0oIfZMUebYMI.webp"></p>',
    'posts/33QpQTCucrLjj2Z6JTUJ13hhmwjuK3ZmxBSuqVgK.png',
    'published',
    (SELECT id FROM categories WHERE slug = 'news' LIMIT 1),
    (SELECT id FROM users WHERE display_name = 'SLS Admin' LIMIT 1),
    '2026-09-22 12:05:00',
    '2026-09-22 12:05:00',
    '2026-09-22 12:05:00'
FROM (SELECT 1) AS _dummy
WHERE NOT EXISTS (SELECT 1 FROM posts WHERE slug = 'update-on-the-latest-scoop');

-- Verify: SELECT id, title, slug, category_id, author_id, status FROM posts WHERE slug IN ('latest-scoop', 'update-on-the-latest-scoop');
--   Both category_id and author_id should be non-NULL. If either is NULL, the
--   category slug or the admin's display_name on live doesn't match what's
--   assumed above -- check with:
--     SELECT id, name, slug FROM categories;
--     SELECT id, display_name FROM users;
--   and re-run with the right values (delete the inserted row(s) first, or
--   just UPDATE posts SET category_id = ?, author_id = ? WHERE slug = ...).
