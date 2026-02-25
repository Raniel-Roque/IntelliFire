import { getApps, initializeApp } from 'firebase/app';
import { getAuth, EmailAuthProvider, onAuthStateChanged, reauthenticateWithCredential, updatePassword } from 'firebase/auth';

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

function setText(id, message) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = message;
}

function show(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
}

function hide(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
}

export function initFirebaseChangePassword() {
    const form = document.getElementById('firebase-change-password-form');
    if (!form) return;

    const config = getFirebaseConfig();
    if (!config) {
        setText('firebase-change-password-error', 'Firebase web config is missing. Set VITE_FIREBASE_API_KEY, VITE_FIREBASE_AUTH_DOMAIN, VITE_FIREBASE_PROJECT_ID.');
        show('firebase-change-password-error');
        return;
    }

    const app = getApps().length ? getApps()[0] : initializeApp(config);
    const auth = getAuth(app);

    async function getCurrentUser() {
        if (auth.currentUser) return auth.currentUser;
        return await new Promise((resolve) => {
            const unsubscribe = onAuthStateChanged(auth, (user) => {
                unsubscribe();
                resolve(user);
            });
        });
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        hide('firebase-change-password-error');
        hide('firebase-change-password-success');

        const currentPassword = form.querySelector('#current_password')?.value;
        const newPassword = form.querySelector('#new_password')?.value;
        const confirm = form.querySelector('#new_password_confirmation')?.value;

        if (!newPassword || newPassword.length < 8) {
            setText('firebase-change-password-error', 'New password must be at least 8 characters.');
            show('firebase-change-password-error');
            return;
        }

        if (newPassword !== confirm) {
            setText('firebase-change-password-error', 'Password confirmation does not match.');
            show('firebase-change-password-error');
            return;
        }

        const user = await getCurrentUser();
        if (!user || !user.email) {
            setText('firebase-change-password-error', 'You are not authenticated in Firebase. Please log out and log in again.');
            show('firebase-change-password-error');
            return;
        }

        try {
            const credential = EmailAuthProvider.credential(user.email, currentPassword);
            await reauthenticateWithCredential(user, credential);
            await updatePassword(user, newPassword);

            setText('firebase-change-password-success', 'Your password has been changed successfully.');
            show('firebase-change-password-success');

            form.reset();
        } catch (err) {
            setText('firebase-change-password-error', err?.message || 'Failed to change password.');
            show('firebase-change-password-error');
        }
    });
}
