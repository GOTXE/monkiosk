<?php
require_once __DIR__ . '/auth_lib.php';
auth_session_start();

if (auth_is_authenticated()) {
    header('Location: /estado_quioscos/');
    exit;
}

$error = isset($_GET['e']) ? trim((string)$_GET['e']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Estado Quioscos</title>
    <style>
        :root {
            --bg: #001f3f;
            --card: #ffffff;
            --ink: #13233b;
            --muted: #4f627c;
            --line: #d8e2ef;
            --btn: #2b78d9;
            --btn-hover: #1f66bf;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; min-height: 100vh; font-family: Verdana, sans-serif; background: var(--bg); }
        .page { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; }
        .card {
            width: min(460px, 100%);
            background: var(--card);
            color: var(--ink);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 10px 28px rgba(8, 25, 48, 0.28);
        }
        .app-footer {
            margin-top: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #d6e7ff;
            font-size: 0.84rem;
            width: 100%;
        }
        .footer-brand { font-weight: 700; }
        .footer-github {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            color: #d6e7ff;
            border: 1px solid rgba(214, 231, 255, 0.35);
            text-decoration: none;
        }
        .footer-github svg { width: 15px; height: 15px; fill: currentColor; }
        h1 { margin: 0 0 16px; font-size: 1.35rem; line-height: 1.3; text-align: center; }
        h1 .subline { display: block; }
        .field { display: grid; gap: 6px; margin-top: 10px; }
        .field label { color: var(--muted); font-size: 0.86rem; font-weight: 700; }
        .field input[type="text"], .field input[type="password"] {
            border: 1px solid #cdd9e8;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.96rem;
            color: var(--ink);
            background: #f8fbff;
        }
        .password-row {
            position: relative;
            width: 100%;
        }
        .password-row input {
            display: block;
            width: 100%;
            padding-right: 44px;
        }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            width: 28px;
            height: 28px;
            border: 0;
            background: transparent;
            color: #55708f;
            border-radius: 50%;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .password-toggle:hover { background: rgba(43, 120, 217, 0.1); }
        .password-toggle svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }
        .remember {
            margin-top: 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 0.86rem;
            font-weight: 700;
        }
        .submit {
            width: 100%;
            margin-top: 16px;
            border: 1px solid #2a70c8;
            background: var(--btn);
            color: #fff;
            border-radius: 10px;
            padding: 10px 12px;
            font-weight: 700;
            cursor: pointer;
        }
        .submit:hover { background: var(--btn-hover); }
        .error {
            margin-top: 10px;
            color: #a61e2a;
            font-size: 0.88rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="page">
        <form class="card" method="post" action="/estado_quioscos/login_action.php" id="login-form" autocomplete="on">
            <h1>Bienvenido a Estado Quioscos <span class="subline">OFAP 601</span></h1>

            <div class="field">
                <label for="username">Usuario</label>
                <input id="username" name="username" type="text" required autocomplete="username">
            </div>

            <div class="field">
                <label for="password">Contraseña</label>
                <div class="password-row">
                    <input id="password" name="password" type="password" required autocomplete="current-password">
                    <button id="password-toggle" class="password-toggle" type="button" aria-label="Mostrar contraseña" aria-controls="password" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 5c5.23 0 9.27 3.11 11 7-1.73 3.89-5.77 7-11 7S2.73 15.89 1 12c1.73-3.89 5.77-7 11-7Zm0 2C8.18 7 5.14 9.13 3.42 12 5.14 14.87 8.18 17 12 17s6.86-2.13 8.58-5C18.86 9.13 15.82 7 12 7Zm0 1.5A3.5 3.5 0 1 1 8.5 12 3.5 3.5 0 0 1 12 8.5Zm0 2A1.5 1.5 0 1 0 13.5 12 1.5 1.5 0 0 0 12 10.5Z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <label class="remember">
                <input id="remember" type="checkbox" name="remember">
                <span>Recordar usuario</span>
            </label>

            <button class="submit" type="submit">Entrar</button>
            <?php if ($error !== ''): ?>
                <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </form>
        <footer class="app-footer">
            <span class="footer-brand">OFAP 601</span>
            <a class="footer-github" href="https://github.com/GOTXE/monkiosk" target="_blank" rel="noopener noreferrer" aria-label="Repositorio GitHub monkiosk" title="Repositorio GitHub monkiosk">
                <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M8 0C3.58 0 0 3.58 0 8a8 8 0 0 0 5.47 7.59c.4.07.55-.17.55-.38v-1.49c-2.23.48-2.7-.95-2.7-.95-.36-.92-.89-1.16-.89-1.16-.73-.5.06-.49.06-.49.81.06 1.24.83 1.24.83.72 1.24 1.89.88 2.35.67.07-.52.28-.88.5-1.08-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.58.82-2.14-.08-.2-.36-1.01.08-2.1 0 0 .67-.21 2.2.82a7.62 7.62 0 0 1 4 0c1.53-1.03 2.2-.82 2.2-.82.44 1.09.16 1.9.08 2.1.51.56.82 1.27.82 2.14 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48v2.2c0 .21.15.46.55.38A8 8 0 0 0 16 8c0-4.42-3.58-8-8-8Z"/>
                </svg>
            </a>
        </footer>
    </div>

    <script>
        const u = document.getElementById('username');
        const p = document.getElementById('password');
        const t = document.getElementById('password-toggle');
        const r = document.getElementById('remember');
        const f = document.getElementById('login-form');

        const remembered = localStorage.getItem('estado_quioscos_remember') === '1';
        localStorage.removeItem('estado_quioscos_pass');
        if (remembered) {
            u.value = localStorage.getItem('estado_quioscos_user') || '';
            r.checked = true;
        }

        if (t && p) {
            t.addEventListener('click', () => {
                const reveal = p.type === 'password';
                p.type = reveal ? 'text' : 'password';
                t.setAttribute('aria-label', reveal ? 'Ocultar contraseña' : 'Mostrar contraseña');
                t.setAttribute('aria-pressed', reveal ? 'true' : 'false');
            });
        }

        f.addEventListener('submit', () => {
            if (r.checked) {
                localStorage.setItem('estado_quioscos_remember', '1');
                localStorage.setItem('estado_quioscos_user', u.value);
            } else {
                localStorage.removeItem('estado_quioscos_remember');
                localStorage.removeItem('estado_quioscos_user');
            }
        });
    </script>
</body>
</html>
