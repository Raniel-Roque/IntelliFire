# IntelliFire

IntelliFire is a fire detection notification system.

An Arduino-based device sends sensor events to this Laravel application. The backend persists events to MySQL and publishes a realtime status to Firebase Realtime Database so an MIT App Inventor mobile app can update instantly.

## Stack

- Laravel + Livewire + Vite
- MySQL (system of record)
- Firebase Auth (email/password)
- Firebase Realtime Database (realtime sync channel)

## High-level data flow

- Arduino -> Laravel webhook/API
- Laravel -> MySQL (store event history and current state)
- Laravel -> Firebase Realtime Database (publish latest status for realtime UI)
- MIT App Inventor -> Firebase Realtime Database listener (no polling)

## Authentication

- Mobile app and web UI use Firebase Auth.
- The Laravel web app signs in in the browser with Firebase Auth, then exchanges the Firebase ID token for a Laravel session.

## Environment configuration

Set these in your local `.env`:

- MySQL
  - `DB_CONNECTION=mysql`
  - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Firebase Admin (server)
  - `FIREBASE_PROJECT=app`
  - `FIREBASE_CREDENTIALS=storage/app/firebase/<service-account>.json`
  - `FIREBASE_DATABASE_URL=https://<your-project>-default-rtdb.<region>.firebasedatabase.app`
- Firebase Web config (Vite)
  - `VITE_FIREBASE_API_KEY=...`
  - `VITE_FIREBASE_AUTH_DOMAIN=...`
  - `VITE_FIREBASE_PROJECT_ID=...`

## Local setup

```bash
composer install
npm install

php artisan migrate

npm run dev
php artisan serve
```

## Notes

- Do not commit the Firebase service account JSON.
- Firebase API keys are not secrets, but database access must be enforced with Firebase Auth + Realtime Database rules.
