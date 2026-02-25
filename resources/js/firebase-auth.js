import { getApps, initializeApp } from 'firebase/app';
import { getAuth, signInWithEmailAndPassword } from 'firebase/auth';

function getFirebaseConfig() {
    const apiKey = import.meta.env.VITE_FIREBASE_API_KEY;
    const authDomain = import.meta.env.VITE_FIREBASE_AUTH_DOMAIN;
    const projectId = import.meta.env.VITE_FIREBASE_PROJECT_ID;

    if (!apiKey || !authDomain || !projectId) {
        return null;
    }

    return {
        apiKey,
        authDomain,
        projectId,
    };
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
}

async function exchangeIdTokenForLaravelSession(idToken) {
    const res = await fetch('/auth/firebase/session', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
        },
        body: JSON.stringify({ id_token: idToken }),
        credentials: 'same-origin',
    });

    if (!res.ok) {
        const text = await res.text();
        throw new Error(text || 'Session exchange failed');
    }

    return await res.json();
}

function setError(message) {
    const el = document.getElementById('firebase-login-error');
    if (!el) return;
    el.textContent = message;
    el.classList.remove('hidden');
}

function firebaseAuthErrorToMessage(err) {
    let code = err?.code;

    if (typeof code !== 'string') {
        const msg = err?.message;
        if (typeof msg === 'string') {
            const match = msg.match(/\((auth\/[a-z0-9-]+)\)/i);
            if (match?.[1]) code = match[1];
        }
    }

    if (typeof code !== 'string') return null;

    switch (code) {
        case 'auth/invalid-credential':
        case 'auth/invalid-login-credentials':
        case 'auth/wrong-password':
        case 'auth/user-not-found':
            return 'Invalid email or password.';
        case 'auth/invalid-email':
            return 'Please enter a valid email address.';
        case 'auth/user-disabled':
            return 'This account has been disabled. Please contact support.';
        case 'auth/too-many-requests':
            return 'Too many failed attempts. Please try again later.';
        case 'auth/network-request-failed':
            return 'Network error. Please check your internet connection and try again.';
        default:
            return null;
    }
}

export function initFirebaseEmailPasswordLogin() {
    const form = document.getElementById('firebase-login-form');
    if (!form) return;

    const config = getFirebaseConfig();
    if (!config) {
        setError('Firebase web config is missing. Set VITE_FIREBASE_API_KEY, VITE_FIREBASE_AUTH_DOMAIN, VITE_FIREBASE_PROJECT_ID.');
        return;
    }

    const app = getApps().length ? getApps()[0] : initializeApp(config);
    const auth = getAuth(app);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = form.querySelector('#email')?.value;
        const password = form.querySelector('#password')?.value;

        try {
            const cred = await signInWithEmailAndPassword(auth, email, password);
            const idToken = await cred.user.getIdToken();
            const data = await exchangeIdTokenForLaravelSession(idToken);
            window.location.href = data.redirect || '/dashboard';
        } catch (err) {
            const friendly = firebaseAuthErrorToMessage(err);
            if (friendly) {
                setError(friendly);
                return;
            }

            const msg = err?.message;
            if (typeof msg === 'string' && msg.match(/\(auth\/[a-z0-9-]+\)/i)) {
                setError('Login failed. Please check your email and password and try again.');
                return;
            }

            setError(msg || 'Login failed');
        }
    });
}
