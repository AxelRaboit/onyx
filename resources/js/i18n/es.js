export default {
    nav: {
        dashboard: 'Panel',
        profile: 'Mi perfil',
        logout: 'Cerrar sesión',
        lightMode: 'Modo claro',
        darkMode: 'Modo oscuro',
        'dev-dashboard': 'Administración',
    },

    common: {
        save: 'Guardar',
        loading: 'Cargando…',
        saved: 'Guardado.',
        cancel: 'Cancelar',
        create: 'Crear',
        edit: 'Editar',
        update: 'Actualizar',
        add: 'Añadir',
        delete: 'Eliminar',
        confirm: 'Confirmar',
        or: 'o',
        error: 'Se produjo un error.',
    },

    auth: {
        login: {
            title: 'Iniciar sesión',
            email: 'Correo electrónico',
            password: 'Contraseña',
            rememberMe: 'Recuérdame',
            forgot: '¿Olvidaste tu contraseña?',
            submit: 'Iniciar sesión',
            noAccount: '¿No tienes cuenta? Regístrate',
        },
        register: {
            title: 'Registrarse',
            name: 'Nombre',
            email: 'Correo electrónico',
            password: 'Contraseña',
            passwordConfirm: 'Confirmar contraseña',
            alreadyAccount: '¿Ya tienes cuenta?',
            submit: 'Registrarse',
        },
        forgotPassword: {
            title: 'Contraseña olvidada',
            instructions:
                '¿Olvidaste tu contraseña? No hay problema. Indícanos tu correo y te enviaremos un enlace de restablecimiento.',
            submit: 'Enviar enlace',
        },
        resetPassword: {
            title: 'Restablecer contraseña',
            email: 'Correo electrónico',
            password: 'Nueva contraseña',
            passwordConfirm: 'Confirmar contraseña',
            submit: 'Restablecer',
        },
        verifyEmail: {
            title: 'Verificación de correo',
            instructions:
                '¡Gracias por registrarte! Verifica tu dirección de correo haciendo clic en el enlace que acabamos de enviarte.',
            resend: 'Reenviar correo de verificación',
            logout: 'Cerrar sesión',
            resent: 'Se ha enviado un nuevo enlace de verificación.',
        },
        confirmPassword: {
            title: 'Confirmar contraseña',
            instructions: 'Área segura. Por favor confirma tu contraseña antes de continuar.',
            password: 'Contraseña',
            submit: 'Confirmar',
        },
    },

    dashboard: {
        title: 'Panel',
        welcome: '¡Bienvenido, {name}!',
    },

    profile: {
        title: 'Mi perfil',
        locale: {
            title: 'Idioma',
            subtitle: 'Elige el idioma de visualización de la aplicación.',
        },
        info: {
            title: 'Información del perfil',
            subtitle: 'Actualiza tu nombre y correo electrónico.',
            fieldName: 'Nombre',
            fieldEmail: 'Correo electrónico',
            unverified: 'Tu dirección de correo no está verificada.',
            resend: 'Haz clic aquí para reenviar el correo de verificación.',
            verificationSent: 'Se ha enviado un nuevo enlace de verificación a tu correo.',
        },
        password: {
            title: 'Cambiar contraseña',
            subtitle: 'Usa una contraseña larga y aleatoria para mantener tu cuenta segura.',
            fieldCurrent: 'Contraseña actual',
            fieldNew: 'Nueva contraseña',
            fieldConfirm: 'Confirmar contraseña',
        },
        delete: {
            title: 'Eliminar cuenta',
            subtitle: 'Una vez eliminada tu cuenta, todos los datos asociados se borrarán permanentemente.',
            openBtn: 'Eliminar mi cuenta',
            modalTitle: 'Eliminar cuenta',
            modalSubtitle: 'Esta acción es irreversible. Confirma con tu contraseña.',
            fieldPassword: 'Tu contraseña',
            submitting: 'Eliminando…',
            submitConfirm: 'Eliminar permanentemente',
        },
    },

    locales: {
        fr: 'Français',
        en: 'English',
        es: 'Español',
        de: 'Deutsch',
    },

    pagination: {
        results: '{from}–{to} de {total} resultados',
        previous: 'Anterior',
        next: 'Siguiente',
    },
};
