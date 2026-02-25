import './bootstrap';

import Alpine from 'alpinejs';

import { initFirebaseEmailPasswordLogin } from './firebase-auth';
import { initFirebaseChangePassword } from './firebase-change-password';

window.Alpine = Alpine;
Alpine.start();

initFirebaseEmailPasswordLogin();
initFirebaseChangePassword();

