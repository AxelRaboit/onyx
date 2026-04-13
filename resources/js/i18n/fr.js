export default {
    nav: {
        dashboard: 'Tableau de bord',
        profile: 'Mon profil',
        logout: 'Déconnexion',
        lightMode: 'Mode clair',
        darkMode: 'Mode sombre',
        'dev-dashboard': 'Administration',
    },

    common: {
        save: 'Enregistrer',
        loading: 'Chargement…',
        saved: 'Enregistré.',
        cancel: 'Annuler',
        create: 'Créer',
        edit: 'Modifier',
        update: 'Mettre à jour',
        add: 'Ajouter',
        delete: 'Supprimer',
        confirm: 'Confirmer',
        or: 'ou',
        error: 'Une erreur est survenue.',
    },

    auth: {
        login: {
            title: 'Connexion',
            email: 'E-mail',
            password: 'Mot de passe',
            rememberMe: 'Se souvenir de moi',
            forgot: 'Mot de passe oublié ?',
            submit: 'Se connecter',
            noAccount: "Pas encore de compte ? S'inscrire",
        },
        register: {
            title: 'Inscription',
            name: 'Nom',
            email: 'E-mail',
            password: 'Mot de passe',
            passwordConfirm: 'Confirmer le mot de passe',
            alreadyAccount: 'Déjà inscrit ?',
            submit: "S'inscrire",
        },
        forgotPassword: {
            title: 'Mot de passe oublié',
            instructions:
                'Mot de passe oublié ? Pas de problème. Indiquez votre e-mail et nous vous enverrons un lien de réinitialisation.',
            submit: 'Envoyer le lien',
        },
        resetPassword: {
            title: 'Réinitialiser le mot de passe',
            email: 'E-mail',
            password: 'Nouveau mot de passe',
            passwordConfirm: 'Confirmer le mot de passe',
            submit: 'Réinitialiser',
        },
        verifyEmail: {
            title: 'Vérification e-mail',
            instructions:
                'Merci de vous être inscrit ! Avant de commencer, vérifiez votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer.',
            resend: "Renvoyer l'e-mail de vérification",
            logout: 'Déconnexion',
            resent: 'Un nouveau lien de vérification a été envoyé.',
        },
        confirmPassword: {
            title: 'Confirmer le mot de passe',
            instructions: 'Zone sécurisée. Veuillez confirmer votre mot de passe avant de continuer.',
            password: 'Mot de passe',
            submit: 'Confirmer',
        },
    },

    dashboard: {
        title: 'Tableau de bord',
        welcome: 'Bienvenue, {name} !',
    },

    profile: {
        title: 'Mon profil',
        locale: {
            title: 'Langue',
            subtitle: "Choisissez la langue d'affichage de l'application.",
        },
        info: {
            title: 'Informations du profil',
            subtitle: 'Mettez à jour votre nom et adresse e-mail.',
            fieldName: 'Nom',
            fieldEmail: 'Adresse e-mail',
            unverified: "Votre adresse e-mail n'est pas vérifiée.",
            resend: "Renvoyer l'e-mail de vérification.",
            verificationSent: 'Un nouveau lien de vérification a été envoyé à votre adresse e-mail.',
        },
        password: {
            title: 'Modifier le mot de passe',
            subtitle: 'Utilisez un mot de passe long et aléatoire pour sécuriser votre compte.',
            fieldCurrent: 'Mot de passe actuel',
            fieldNew: 'Nouveau mot de passe',
            fieldConfirm: 'Confirmer le mot de passe',
        },
        delete: {
            title: 'Zone de danger',
            subtitle: 'Une fois votre compte supprimé, toutes les données associées seront définitivement effacées.',
            openBtn: 'Supprimer mon compte',
            modalTitle: 'Supprimer le compte',
            modalSubtitle: 'Cette action est irréversible. Confirmez avec votre mot de passe.',
            fieldPassword: 'Votre mot de passe',
            submitting: 'Suppression…',
            submitConfirm: 'Supprimer définitivement',
        },
    },

    locales: {
        fr: 'Français',
        en: 'English',
        es: 'Español',
        de: 'Deutsch',
    },

    pagination: {
        results: '{from}–{to} sur {total} résultats',
        previous: 'Précédent',
        next: 'Suivant',
    },
};
