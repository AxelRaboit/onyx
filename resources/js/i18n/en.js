export default {
    nav: {
        dashboard: 'Dashboard',
        profile: 'My profile',
        logout: 'Log out',
        lightMode: 'Light mode',
        darkMode: 'Dark mode',
        'dev-dashboard': 'Administration',
    },

    common: {
        save: 'Save',
        loading: 'Loading…',
        saved: 'Saved.',
        cancel: 'Cancel',
        create: 'Create',
        edit: 'Edit',
        update: 'Update',
        add: 'Add',
        delete: 'Delete',
        confirm: 'Confirm',
        or: 'or',
        error: 'An error occurred.',
    },

    auth: {
        login: {
            title: 'Log in',
            email: 'Email',
            password: 'Password',
            rememberMe: 'Remember me',
            forgot: 'Forgot your password?',
            submit: 'Log in',
            noAccount: "Don't have an account? Sign up",
        },
        register: {
            title: 'Register',
            name: 'Name',
            email: 'Email',
            password: 'Password',
            passwordConfirm: 'Confirm Password',
            alreadyAccount: 'Already registered?',
            submit: 'Register',
        },
        forgotPassword: {
            title: 'Forgot Password',
            instructions:
                'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.',
            submit: 'Email Password Reset Link',
        },
        resetPassword: {
            title: 'Reset Password',
            email: 'Email',
            password: 'Password',
            passwordConfirm: 'Confirm Password',
            submit: 'Reset Password',
        },
        verifyEmail: {
            title: 'Email Verification',
            instructions:
                'Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?',
            resend: 'Resend Verification Email',
            logout: 'Log out',
            resent: 'A new verification link has been sent.',
        },
        confirmPassword: {
            title: 'Confirm Password',
            instructions: 'This is a secure area of the application. Please confirm your password before continuing.',
            password: 'Password',
            submit: 'Confirm',
        },
    },

    dashboard: {
        title: 'Dashboard',
        welcome: 'Welcome, {name}!',
    },

    profile: {
        title: 'My profile',
        locale: {
            title: 'Language',
            subtitle: 'Choose the display language for the application.',
        },
        info: {
            title: 'Profile information',
            subtitle: 'Update your name and email address.',
            fieldName: 'Name',
            fieldEmail: 'Email address',
            unverified: 'Your email address is unverified.',
            resend: 'Click here to re-send the verification email.',
            verificationSent: 'A new verification link has been sent to your email address.',
        },
        password: {
            title: 'Update password',
            subtitle: 'Use a long, random password to keep your account secure.',
            fieldCurrent: 'Current password',
            fieldNew: 'New password',
            fieldConfirm: 'Confirm password',
        },
        delete: {
            title: 'Delete account',
            subtitle: 'Once your account is deleted, all associated data will be permanently erased.',
            openBtn: 'Delete my account',
            modalTitle: 'Delete account',
            modalSubtitle: 'This action is irreversible. Please confirm with your password.',
            fieldPassword: 'Your password',
            submitting: 'Deleting…',
            submitConfirm: 'Delete permanently',
        },
    },

    locales: {
        fr: 'Français',
        en: 'English',
        es: 'Español',
        de: 'Deutsch',
    },

    pagination: {
        results: '{from}–{to} of {total} results',
        previous: 'Previous',
        next: 'Next',
    },
};
