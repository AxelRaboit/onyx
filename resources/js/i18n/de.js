export default {
    nav: {
        dashboard: 'Dashboard',
        profile: 'Mein Profil',
        logout: 'Abmelden',
        lightMode: 'Heller Modus',
        darkMode: 'Dunkler Modus',
        'dev-dashboard': 'Verwaltung',
    },

    common: {
        save: 'Speichern',
        loading: 'Laden…',
        saved: 'Gespeichert.',
        cancel: 'Abbrechen',
        create: 'Erstellen',
        edit: 'Bearbeiten',
        update: 'Aktualisieren',
        add: 'Hinzufügen',
        delete: 'Löschen',
        confirm: 'Bestätigen',
        or: 'oder',
        error: 'Ein Fehler ist aufgetreten.',
    },

    auth: {
        login: {
            title: 'Anmelden',
            email: 'E-Mail',
            password: 'Passwort',
            rememberMe: 'Angemeldet bleiben',
            forgot: 'Passwort vergessen?',
            submit: 'Anmelden',
            noAccount: 'Noch kein Konto? Registrieren',
        },
        register: {
            title: 'Registrieren',
            name: 'Name',
            email: 'E-Mail',
            password: 'Passwort',
            passwordConfirm: 'Passwort bestätigen',
            alreadyAccount: 'Bereits registriert?',
            submit: 'Registrieren',
        },
        forgotPassword: {
            title: 'Passwort vergessen',
            instructions:
                'Passwort vergessen? Kein Problem. Gib deine E-Mail-Adresse an und wir senden dir einen Link zum Zurücksetzen.',
            submit: 'Link senden',
        },
        resetPassword: {
            title: 'Passwort zurücksetzen',
            email: 'E-Mail',
            password: 'Neues Passwort',
            passwordConfirm: 'Passwort bestätigen',
            submit: 'Zurücksetzen',
        },
        verifyEmail: {
            title: 'E-Mail-Verifizierung',
            instructions:
                'Danke für deine Registrierung! Bitte verifiziere deine E-Mail-Adresse, indem du auf den Link klickst, den wir dir gerade gesendet haben.',
            resend: 'Verifizierungs-E-Mail erneut senden',
            logout: 'Abmelden',
            resent: 'Ein neuer Verifizierungslink wurde gesendet.',
        },
        confirmPassword: {
            title: 'Passwort bestätigen',
            instructions: 'Sicherheitsbereich. Bitte bestätige dein Passwort, bevor du fortfährst.',
            password: 'Passwort',
            submit: 'Bestätigen',
        },
    },

    dashboard: {
        title: 'Dashboard',
        welcome: 'Willkommen, {name}!',
    },

    profile: {
        title: 'Mein Profil',
        locale: {
            title: 'Sprache',
            subtitle: 'Wähle die Anzeigesprache der Anwendung.',
        },
        info: {
            title: 'Profilinformationen',
            subtitle: 'Aktualisiere deinen Namen und deine E-Mail-Adresse.',
            fieldName: 'Name',
            fieldEmail: 'E-Mail-Adresse',
            unverified: 'Deine E-Mail-Adresse ist nicht verifiziert.',
            resend: 'Klicke hier, um die Verifizierungs-E-Mail erneut zu senden.',
            verificationSent: 'Ein neuer Verifizierungslink wurde an deine E-Mail-Adresse gesendet.',
        },
        password: {
            title: 'Passwort ändern',
            subtitle: 'Verwende ein langes, zufälliges Passwort, um dein Konto zu sichern.',
            fieldCurrent: 'Aktuelles Passwort',
            fieldNew: 'Neues Passwort',
            fieldConfirm: 'Passwort bestätigen',
        },
        delete: {
            title: 'Konto löschen',
            subtitle: 'Sobald dein Konto gelöscht ist, werden alle zugehörigen Daten dauerhaft gelöscht.',
            openBtn: 'Mein Konto löschen',
            modalTitle: 'Konto löschen',
            modalSubtitle: 'Diese Aktion ist unwiderruflich. Bitte mit Passwort bestätigen.',
            fieldPassword: 'Dein Passwort',
            submitting: 'Löschen…',
            submitConfirm: 'Dauerhaft löschen',
        },
    },

    locales: {
        fr: 'Français',
        en: 'English',
        es: 'Español',
        de: 'Deutsch',
    },

    pagination: {
        results: '{from}–{to} von {total} Ergebnissen',
        previous: 'Zurück',
        next: 'Weiter',
    },
};
