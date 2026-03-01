import { getApps, initializeApp } from 'firebase/app';
import { getDatabase, ref, query, limitToLast, onValue } from 'firebase/database';

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

function escapeHtml(str) {
    return String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function formatLevel(level) {
    return String(level || 'warning').toUpperCase();
}

function levelStyles(level) {
    const lvl = String(level || 'warning').toLowerCase();
    if (lvl === 'urgent') {
        return {
            badge: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            border: 'border-red-200 dark:border-red-900/40',
        };
    }

    return {
        badge: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
        border: 'border-yellow-200 dark:border-yellow-900/40',
    };
}

function buildCardHtml(item) {
    const level = formatLevel(item.level);
    const styles = levelStyles(item.level);
    const rawLevel = String(item.level || 'warning').toLowerCase();

    const title = item.title || `${level}: ${item.room_name || `Room ${item.room_number ?? '—'}`}`;

    const createdAt = item.created_at ? new Date(item.created_at).toLocaleString() : '';

    const temp = item.temperature ?? 0;
    const gas = item.gas ?? 0;

    const roomName = item.room_name || (item.room_number != null ? `Room ${item.room_number}` : '');

    return `
        <button type="button" data-notif-card data-room-name="${escapeHtml(roomName)}" data-level="${escapeHtml(rawLevel)}" class="text-left w-full rounded-xl border ${styles.border} bg-white dark:bg-gray-800 p-4 shadow-sm transition-transform duration-300 hover:bg-gray-50 dark:hover:bg-gray-700/40 cursor-pointer">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">${escapeHtml(title)}</p>
                    ${createdAt ? `<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">${escapeHtml(createdAt)}</p>` : ''}
                </div>
                <span class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${styles.badge}">${escapeHtml(level)}</span>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-3">
                <div class="rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-2">
                    <p class="text-xs text-gray-600 dark:text-gray-300">Temp</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">${escapeHtml(temp)} <span class="text-xs font-medium text-gray-500 dark:text-gray-400">°C</span></p>
                </div>
                <div class="rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-2">
                    <p class="text-xs text-gray-600 dark:text-gray-300">Gas</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">${escapeHtml(gas)} <span class="text-xs font-medium text-gray-500 dark:text-gray-400">ppm</span></p>
                </div>
            </div>
        </button>
    `;
}

export function initFirebaseNotificationsFeed() {
    const container = document.querySelector('[data-firebase-notifications-feed]');
    if (!container) return;

    const statusEl = document.getElementById('landing-notifs-status');

    const config = getFirebaseConfig();
    if (!config) {
        if (statusEl) statusEl.textContent = 'Offline';
        return;
    }

    const app = getApps().length ? getApps()[0] : initializeApp(config);
    const db = getDatabase(app);

    if (statusEl) statusEl.textContent = 'Live';

    const q = query(ref(db, 'emergencies/log'), limitToLast(5));

    let lastIds = new Set();
    let lastLatestId = null;

    function normalizeRoomName(roomName) {
        if (!roomName) return null;
        const safe = String(roomName).trim();
        if (!safe) return null;
        return safe;
    }

    function scrollToMaps() {
        const isDesktop = window.matchMedia && window.matchMedia('(min-width: 1024px)').matches;
        const targetId = isDesktop ? 'fire-exit-maps-desktop' : 'fire-exit-maps-mobile';
        const el = document.getElementById(targetId);
        if (!el) return;
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    if (!container.dataset.clickHandlerAttached) {
        container.dataset.clickHandlerAttached = '1';
        container.addEventListener('click', (e) => {
            const card = e.target.closest('[data-notif-card]');
            if (!card) return;

            const roomName = card.getAttribute('data-room-name');
            const normalizedRoomName = normalizeRoomName(roomName);
            if (!normalizedRoomName) {
                scrollToMaps();
                return;
            }

            const level = String(card.getAttribute('data-level') || 'warning').toLowerCase();
            window.dispatchEvent(new CustomEvent('selected-emergency-level', { detail: { level } }));

            scrollToMaps();
            window.dispatchEvent(new CustomEvent('select-fire-exit-map', { detail: { roomName: normalizedRoomName } }));
        });
    }

    onValue(q, (snapshot) => {
        const raw = snapshot.val() || {};
        const items = Object.entries(raw)
            .filter(([, v]) => v && typeof v === 'object')
            .map(([id, v]) => ({ id, ...v }))
            .sort((a, b) => String(b.created_at || '').localeCompare(String(a.created_at || '')))
            .slice(0, 5);

        if (!items.length) {
            container.innerHTML = `
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                    <p class="text-sm text-gray-600 dark:text-gray-300">No notifications yet.</p>
                </div>
            `;

            window.dispatchEvent(new CustomEvent('latest-emergency-level', { detail: { level: 'warning' } }));
            return;
        }

        window.dispatchEvent(new CustomEvent('latest-emergency-level', { detail: { level: items[0]?.level || 'warning' } }));

        const latestId = items[0]?.id ?? null;
        if (latestId && latestId !== lastLatestId) {
            lastLatestId = latestId;
            const latestRoomName = normalizeRoomName(items[0]?.room_name || (items[0]?.room_number != null ? `Room ${items[0].room_number}` : null));
            if (latestRoomName) {
                window.dispatchEvent(new CustomEvent('clear-selected-emergency-level'));
                window.dispatchEvent(new CustomEvent('select-fire-exit-map', { detail: { roomName: latestRoomName } }));
            }
        }

        const incomingIds = new Set(items.map((i) => i.id));
        const newIds = items.filter((i) => !lastIds.has(i.id)).map((i) => i.id);
        lastIds = incomingIds;

        container.innerHTML = items
            .map((i) => `<div data-notif-id="${escapeHtml(i.id)}">${buildCardHtml(i)}</div>`)
            .join('');

        if (newIds.length) {
            for (const id of newIds) {
                const wrapper = container.querySelector(`[data-notif-id="${CSS.escape(String(id))}"]`);
                const card = wrapper?.firstElementChild;
                if (!card) continue;
                card.classList.add('ring-2', 'ring-orange-400', 'dark:ring-orange-500', 'scale-[1.01]');
                setTimeout(() => {
                    card.classList.remove('ring-2', 'ring-orange-400', 'dark:ring-orange-500', 'scale-[1.01]');
                }, 1200);
            }
        }
    }, (err) => {
        if (statusEl) statusEl.textContent = 'Error';
        // Keep existing UI; no console spam in prod.
    });
}
