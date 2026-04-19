# Production Environment Template

Salin nilai ini ke `config/secrets.production.php` sesuai server production Anda.

```php
<?php

return [
    'APP_ENV' => 'production',
    'BASE_URL' => 'https://domain-anda.com',
    'LOG_VIEWER_TOKEN' => '',

    'DB_HOST' => 'localhost',
    'DB_HOSTS' => 'localhost',
    'DB_NAME' => 'nama_database_production',
    'DB_USER' => 'user_database_production',
    'DB_PASS' => 'password_database_production',
];
```

Catatan:
- `DB_HOSTS` opsional, isi lebih dari satu host dipisah koma jika butuh fallback.
- Jangan commit kredensial production ke repository publik.
