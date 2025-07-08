<?php
//start sesji odnownie sesji 
session_start();
//sprawdzanie czy istnieje zmienna error, jesli tak to juz ustawia powiazanie do backendu
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
//czyszczenie zmiennej error 
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_index.css">
    <style>
        .toggle-password {
            cursor: pointer;
            color: #007bff;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
            text-align: right;
        }
        </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mt-5">Logowanie</h2>
                <form method="post" action="../PHP_Logic/login_logic.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nazwa użytkownika</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Hasło</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                            <span class="toggle-password">Pokaż Hasło</span>
                       
                    </div>
                    <?php if ($error)
                    {
                        echo ' <div class="alert alert-danger" role="alert">';
                        echo $error;
                        echo "<br/>";
                }
                if (isset($_SESSION['success'])) {
                    echo '<p style="color: green;">' . $_SESSION['success'] . '</p>';
                    unset($_SESSION['success']);
                }
                       
              ?>
              
                    <button type="submit" class="btn btn-primary w-100">Zaloguj się</button>
                </form>
                <hr>
                <h2 class="text-center mt-5">Odzyskiwanie hasła</h2>
                <form method="post" action="../PHP_Logic/recover_password.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adres Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    
                    </div>
                    <button type="submit" class="btn btn-secondary w-100">Odzyskaj hasło</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.toggle-password').addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                } else {
                    passwordInput.type = 'password';
                }
            });
        });
        </script>
</body>
</html>