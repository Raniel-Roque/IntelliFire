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
    let isInitialized = false;

    try {
        lastSeenKey = sessionStorage.getItem('emergencyLastSeenKey');
    } catch (e) {
        // ignore
    }

    onValue(latestRef, (snapshot) => {
        const val = snapshot.val();
        if (!val || typeof val !== 'object') return;

        const key = `${val.created_at ?? ''}|${val.room_number ?? ''}|${val.level ?? ''}`;

        if (!isInitialized) {
            isInitialized = true;
            lastSeenKey = key;
            try {
                sessionStorage.setItem('emergencyLastSeenKey', lastSeenKey);
            } catch (e) {
                // ignore
            }
            return;
        }

        if (key === lastSeenKey) return;
        lastSeenKey = key;
        try {
            sessionStorage.setItem('emergencyLastSeenKey', lastSeenKey);
        } catch (e) {
            // ignore
        }

        const level = String(val.level || 'warning');
        const roomName = val.room_name ?? null;
        const roomNumber = val.room_number ?? '—';
        const temp = val.temperature ?? 0;
        const gas = val.gas ?? 0;

        const type = level === 'urgent' ? 'error' : 'warning';
        const levelLabel = String(level || 'warning').toUpperCase();
        const roomLabel = roomName ? String(roomName) : `Room ${roomNumber}`;
        const message = `${levelLabel}: ${roomLabel}\nTemp: ${temp}°C\nGas: ${gas} ppm`;

        window.dispatchEvent(new CustomEvent('showToast', { detail: { message, type, persistent: true } }));
        window.dispatchEvent(new CustomEvent('refreshRoomsRealtime'));
    });
}
