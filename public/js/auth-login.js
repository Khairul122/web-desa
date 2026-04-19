document.addEventListener('DOMContentLoaded', function () {
    var config = window.AUTH_LOGIN_CONFIG || {};
    var breakpoints = config.breakpoints || {};
    var timing = config.timing || {};
    var ui = config.ui || {};

    var BREAKPOINT_DESKTOP_PARALLAX = Number(breakpoints.desktopParallaxMinWidth || 1100);
    var CLOCK_REFRESH_MS = Number(timing.clockRefreshMs || 1000);
    var THEME_REFRESH_MS = Number(timing.timeThemeRefreshMs || 60000);
    var SUBMIT_VERIFYING_TEXT = String(ui.submitVerifyingText || 'Memverifikasi...');

    var passwordInput = document.getElementById('password');
    var togglePasswordButton = document.getElementById('togglePassword');
    var submitButton = document.getElementById('loginSubmitButton');
    var form = document.querySelector('.auth-form');
    var clockEl = document.getElementById('authLiveClock');
    var deviceEl = document.getElementById('authDeviceType');
    var capsLockHint = document.getElementById('capsLockHint');
    var identifierInput = document.getElementById('identifier');
    var authBody = document.body;
    var authVisual = document.querySelector('.auth-visual');
    var parallaxLayers = document.querySelectorAll('.auth-parallax-layer');
    var parallaxCardLayers = document.querySelectorAll('.auth-parallax-card');
    var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function detectDeviceType() {
        var width = window.innerWidth || document.documentElement.clientWidth || 0;
        if (width <= 640) return 'Mobile';
        if (width <= 1024) return 'Tablet';
        return 'Desktop';
    }

    function updateClock() {
        if (!clockEl) return;
        var now = new Date();
        clockEl.textContent = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    function refreshDeviceLabel() {
        if (deviceEl) deviceEl.textContent = detectDeviceType();
    }

    function getTimeTheme() {
        var now = new Date();
        var hour = now.getHours();
        if (hour < 11) return 'pagi';
        if (hour < 15) return 'siang';
        if (hour < 19) return 'sore';
        return 'malam';
    }

    function applyTimeTheme() {
        if (!authBody) return;
        authBody.setAttribute('data-auth-time', getTimeTheme());
    }

    function enableParallax() {
        if (!authVisual || prefersReducedMotion) return;
        if (window.innerWidth < BREAKPOINT_DESKTOP_PARALLAX) return;

        var rafId = null;
        var pointerX = 0;
        var pointerY = 0;

        var updateLayers = function () {
            parallaxLayers.forEach(function (layer) {
                var depth = Number(layer.getAttribute('data-parallax-depth') || 8);
                var x = pointerX * (depth / 100);
                var y = pointerY * (depth / 100);
                layer.style.transform = 'translate3d(' + x.toFixed(2) + 'px,' + y.toFixed(2) + 'px,0)';
            });

            parallaxCardLayers.forEach(function (layer) {
                var depth = Number(layer.getAttribute('data-card-depth') || 5);
                var x = pointerX * (depth / 140);
                var y = pointerY * (depth / 140);
                layer.style.transform = 'translate3d(' + x.toFixed(2) + 'px,' + y.toFixed(2) + 'px,0)';
            });

            rafId = null;
        };

        authVisual.addEventListener('mousemove', function (event) {
            var rect = authVisual.getBoundingClientRect();
            var relX = (event.clientX - rect.left) / rect.width;
            var relY = (event.clientY - rect.top) / rect.height;
            pointerX = (relX - 0.5) * 10;
            pointerY = (relY - 0.5) * 10;

            if (!rafId) {
                rafId = requestAnimationFrame(updateLayers);
            }
        });

        authVisual.addEventListener('mouseleave', function () {
            parallaxLayers.forEach(function (layer) {
                layer.style.transform = 'translate3d(0, 0, 0)';
            });
            parallaxCardLayers.forEach(function (layer) {
                layer.style.transform = 'translate3d(0, 0, 0)';
            });
        });
    }

    function validateRequiredField(input) {
        if (!input) return true;
        var isValid = String(input.value || '').trim() !== '';
        input.classList.toggle('is-invalid', !isValid);
        return isValid;
    }

    if (passwordInput && togglePasswordButton) {
        togglePasswordButton.addEventListener('click', function () {
            var isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            togglePasswordButton.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });

        passwordInput.addEventListener('keyup', function (event) {
            if (!capsLockHint) return;
            var active = event.getModifierState && event.getModifierState('CapsLock');
            capsLockHint.hidden = !active;
        });

        passwordInput.addEventListener('blur', function () {
            if (capsLockHint) capsLockHint.hidden = true;
        });

        passwordInput.addEventListener('input', function () {
            validateRequiredField(passwordInput);
        });
    }

    if (identifierInput) {
        identifierInput.addEventListener('input', function () {
            validateRequiredField(identifierInput);
        });
    }

    updateClock();
    refreshDeviceLabel();
    applyTimeTheme();
    enableParallax();
    setInterval(updateClock, CLOCK_REFRESH_MS);
    setInterval(applyTimeTheme, THEME_REFRESH_MS);
    window.addEventListener('resize', refreshDeviceLabel);

    if (form && submitButton) {
        form.addEventListener('submit', function () {
            var isIdentifierValid = validateRequiredField(identifierInput);
            var isPasswordValid = validateRequiredField(passwordInput);
            var hasError = !isIdentifierValid || !isPasswordValid;

            if (hasError) {
                return;
            }

            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + SUBMIT_VERIFYING_TEXT;
        });
    }
});
