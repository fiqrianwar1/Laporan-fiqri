<?php

test('halaman utama mengarahkan ke dashboard', function () {
    // Rute "/" memang redirect (tamu ke halaman login, user login ke dashboard),
    // jadi status yang benar adalah 302 — bukan 200.
    $this->get('/')->assertRedirect(route('dashboard'));
});
