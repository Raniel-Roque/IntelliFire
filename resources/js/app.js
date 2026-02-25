import './bootstrap';

import { initFirebaseEmailPasswordLogin } from './firebase-auth';
import { initFirebaseChangePassword } from './firebase-change-password';
import { initFirebaseEmergencyListener } from './firebase-emergency-listener';
import { initFirebaseNotificationsFeed } from './firebase-notifications-feed';

initFirebaseEmailPasswordLogin();
initFirebaseChangePassword();
initFirebaseEmergencyListener();
initFirebaseNotificationsFeed();

window.addEventListener('refreshRoomsRealtime', () => {
    const lw = window.Livewire;
    if (!lw || typeof lw.dispatch !== 'function') return;
    lw.dispatch('refreshRooms');
});

