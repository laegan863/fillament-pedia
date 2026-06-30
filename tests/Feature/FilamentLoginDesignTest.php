<?php

test('administrator login page renders the custom branded design', function (): void {
    $response = $this->get('/administrator/login');

    $response
        ->assertSuccessful()
        ->assertSee('custom-login-shell', false)
        ->assertSee('HBCR PEDIA')
        ->assertSee('Hospital-based childhood cancer registry')
        ->assertSee('Secure administrator access')
        ->assertDontSee('fi-simple-header', false);
});
