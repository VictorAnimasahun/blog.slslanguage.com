<?php

// Public registration is disabled — /register redirects to /login.
// User accounts are created by admins via the admin panel.

test('registration screen redirects to login', function () {
    $response = $this->get('/register');

    $response->assertRedirect('/login');
});
