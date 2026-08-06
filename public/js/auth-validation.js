/**
 * auth-validation.js
 * Client-side validation for VIBE Store login & register forms.
 * Vanilla JS only — no jQuery, no external libraries.
 */

(function () {
    'use strict';

    /* ─────────────────────────────────────────────
       Utility helpers
    ───────────────────────────────────────────── */

    /**
     * Return (or lazily create) the error <div> that lives right after `input`.
     */
    function getErrorEl(input) {
        let el = input.parentElement.querySelector('.vibe-field-error');
        if (!el) {
            el = document.createElement('div');
            el.className = 'vibe-field-error';
            input.parentElement.appendChild(el);
        }
        return el;
    }

    function showError(input, message) {
        input.classList.add('is-invalid');
        const el = getErrorEl(input);
        el.textContent = message;
        el.style.display = 'block';
    }

    function clearError(input) {
        input.classList.remove('is-invalid');
        const el = input.parentElement.querySelector('.vibe-field-error');
        if (el) {
            el.textContent = '';
            el.style.display = 'none';
        }
    }

    /**
     * Basic RFC-5322-inspired email check (same as <input type="email"> validity).
     */
    function isValidEmail(value) {
        // Reuse the browser's own validator for consistency
        const tmp = document.createElement('input');
        tmp.type = 'email';
        tmp.value = value;
        return tmp.validity.valid;
    }

    /* ─────────────────────────────────────────────
       Per-field validators  →  return '' if OK, or error string
    ───────────────────────────────────────────── */

    function validateName(input) {
        if (!input.value.trim()) return 'Full name is required.';
        return '';
    }

    function validateEmail(input) {
        if (!input.value.trim()) return 'Email address is required.';
        if (!isValidEmail(input.value.trim())) return 'Please enter a valid email address.';
        return '';
    }

    function validatePassword(input) {
        if (!input.value) return 'Password is required.';
        if (input.value.length < 8) return 'Password must be at least 8 characters.';
        return '';
    }

    function validatePasswordLogin(input) {
        if (!input.value) return 'Password is required.';
        return '';
    }

    function validateConfirmation(input, passwordInput) {
        if (!input.value) return 'Please confirm your password.';
        if (input.value !== passwordInput.value) return 'Passwords do not match.';
        return '';
    }

    /* ─────────────────────────────────────────────
       Attach real-time (input + blur) listeners
    ───────────────────────────────────────────── */

    function attachRealTime(input, validatorFn) {
        ['input', 'blur'].forEach(function (evt) {
            input.addEventListener(evt, function () {
                const msg = validatorFn(input);
                if (msg) showError(input, msg);
                else clearError(input);
            });
        });
    }

    /* ─────────────────────────────────────────────
       LOGIN form
    ───────────────────────────────────────────── */

    function initLoginForm() {
        const form = document.querySelector('form[action*="login"]');
        if (!form) return;

        const emailInput    = form.querySelector('input[name="email"]');
        const passwordInput = form.querySelector('input[name="password"]');

        if (emailInput)    attachRealTime(emailInput,    validateEmail);
        if (passwordInput) attachRealTime(passwordInput, validatePasswordLogin);

        form.addEventListener('submit', function (e) {
            const errors = [];

            if (emailInput) {
                const msg = validateEmail(emailInput);
                if (msg) { showError(emailInput, msg); errors.push(emailInput); }
                else clearError(emailInput);
            }
            if (passwordInput) {
                const msg = validatePasswordLogin(passwordInput);
                if (msg) { showError(passwordInput, msg); errors.push(passwordInput); }
                else clearError(passwordInput);
            }

            if (errors.length > 0) {
                e.preventDefault();
                errors[0].focus();
            }
        });
    }

    /* ─────────────────────────────────────────────
       REGISTER form
    ───────────────────────────────────────────── */

    function initRegisterForm() {
        const form = document.querySelector('form[action*="register"]');
        if (!form) return;

        const nameInput     = form.querySelector('input[name="name"]');
        const emailInput    = form.querySelector('input[name="email"]');
        const passwordInput = form.querySelector('input[name="password"]');
        const confirmInput  = form.querySelector('input[name="password_confirmation"]');

        if (nameInput)    attachRealTime(nameInput,    validateName);
        if (emailInput)   attachRealTime(emailInput,   validateEmail);
        if (passwordInput) {
            attachRealTime(passwordInput, validatePassword);
            // Re-validate confirm whenever password changes
            passwordInput.addEventListener('input', function () {
                if (confirmInput && confirmInput.value) {
                    const msg = validateConfirmation(confirmInput, passwordInput);
                    if (msg) showError(confirmInput, msg);
                    else clearError(confirmInput);
                }
            });
        }
        if (confirmInput && passwordInput) {
            attachRealTime(confirmInput, function (inp) {
                return validateConfirmation(inp, passwordInput);
            });
        }

        form.addEventListener('submit', function (e) {
            const errors = [];

            function check(input, fn) {
                if (!input) return;
                const msg = fn(input);
                if (msg) { showError(input, msg); errors.push(input); }
                else clearError(input);
            }

            check(nameInput,    validateName);
            check(emailInput,   validateEmail);
            check(passwordInput, validatePassword);
            if (confirmInput && passwordInput) {
                check(confirmInput, function (inp) {
                    return validateConfirmation(inp, passwordInput);
                });
            }

            if (errors.length > 0) {
                e.preventDefault();
                errors[0].focus();
            }
        });
    }

    /* ─────────────────────────────────────────────
       Bootstrap — run after DOM is ready
    ───────────────────────────────────────────── */

    document.addEventListener('DOMContentLoaded', function () {
        initLoginForm();
        initRegisterForm();
    });

}());
