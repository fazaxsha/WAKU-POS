<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    // Root redirects to dashboard (which is protected by auth middleware).
    $response->assertRedirect('/dashboard');
});
