import { getApps, initializeApp } from 'firebase/app';
import { getDatabase, ref, onValue } from 'firebase/database';

function getFirebaseConfig() {
    const apiKey = import.meta.env.VITE_FIREBASE_API_KEY;
    const authDomain = import.meta.env.VITE_FIREBASE_AUTH_DOMAIN;
    const projectId = import.meta.env.VITE_FIREBASE_PROJECT_ID;
    const databaseURL = import.meta.env.VITE_FIREBASE_DATABASE_URL;

    if (!apiKey || !authDomain || !projectId || !databaseURL) {
        return null;
    }

    return {
        apiKey,
        authDomain,
        projectId,
        databaseURL,
    };
}

export function initFirebaseEmergencyListener() {
    const root = document.querySelector('[data-firebase-emergency-listener]');
    if (!root) return;

    const config = getFirebaseConfig();
    if (!config) return;

    const app = getApps().length ? getApps()[0] : initializeApp(config);
    const db = getDatabase(app);

    const latestRef = ref(db, 'emergencies/latest');

    let lastSeenKey = null;

    onValue(latestRef, (snapshot) => {
        const val = snapshot.val();
        if (!val || typeof val !== 'object') return;

        const key = `${val.created_at ?? ''}|${val.room_number ?? ''}|${val.level ?? ''}`;
        if (key === lastSeenKey) return;
        lastSeenKey = key;

        const level = String(val.level || 'warning');
        const roomNumber = val.room_number ?? '—';
        const temp = val.temperature ?? 0;
        const gas = val.gas ?? 0;

        const type = level === 'urgent' ? 'error' : 'warning';
        const message = `Emergency (${level}) in Room #${roomNumber} — Temp: ${temp}°C, Gas: ${gas} m³`;

        window.dispatchEvent(new CustomEvent('showToast', { detail: { message, type } }));
        window.dispatchEvent(new CustomEvent('refreshRoomsRealtime'));
    });
}
