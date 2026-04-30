<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Semana del Estudiante</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 45%, #f8f9fa 45%, #f8f9fa 100%);
            padding:20px;
        }

        .login-card{
            width:100%;
            max-width:430px;
        }

        .card{
            border:none;
            border-radius:24px;
            overflow:hidden;
        }

        .form-control{
            border-radius:14px;
            padding:12px 14px;
        }

        .form-control:focus{
            box-shadow:none;
            border-color:#0d6efd;
        }

        .btn-login{
            border-radius:14px;
            padding:12px;
            font-weight:700;
        }

        .logo-circle{
            width:70px;
            height:70px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:2rem;
            margin:auto;
        }
    </style>
</head>

<body>

<div class="login-card">

    <div class="card shadow-lg">

        <!-- Header -->
        <div class="bg-primary text-white text-center py-4 px-4">

            <div class="logo-circle bg-white text-primary shadow-sm mb-3">
                🏆
            </div>

            <h3 class="fw-bold mb-1">Iniciar Sesión</h3>

            <p class="mb-0 text-white-50">
                Panel Administrativo
            </p>

        </div>

        <!-- Body -->
        <div class="card-body p-4 p-md-5 bg-white">

            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger border-0 rounded-4 text-center fw-semibold">
                    ❌ Usuario o contraseña incorrectos
                </div>
            <?php endif; ?>

            <form action="procesar_login.php" method="POST">

                <!-- Usuario -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">
                        Usuario
                    </label>

                    <input 
                        type="text" 
                        name="username" 
                        class="form-control" 
                        placeholder="Ingresa tu usuario"
                        required>
                </div>

                <!-- Contraseña -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">
                        Contraseña
                    </label>

                    <input 
                        type="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Ingresa tu contraseña"
                        required>
                </div>

                <!-- Botón -->
                <button type="submit" class="btn btn-primary w-100 btn-login shadow-sm">
                    🔐 Entrar
                </button>

            </form>

            <!-- Volver -->
            <div class="text-center mt-4">

                <a href="index.php" class="text-decoration-none fw-semibold text-primary">
                    ← Volver a la tabla de puntos
                </a>

            </div>

        </div>

    </div>

</div>

</body>
</html>