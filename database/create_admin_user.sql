-- ZerroCMS: Admin-Benutzer anlegen
-- Erst nach "php artisan migrate" ausführen!

USE zerrocms;

INSERT INTO users (name, email, email_verified_at, password, remember_token, created_at, updated_at)
VALUES (
  'Admin',
  'admin@drenor.de',
  NULL,
  '$2y$10$GWmmcOGlJRiPBOll9JPUuO2b60S36I95bURkQJRITaYMx.xFGuV6C',
  NULL,
  NOW(),
  NOW()
);
