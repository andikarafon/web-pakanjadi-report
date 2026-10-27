<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login - Web Pakanjadi Report</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f6fa;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border: 0;
            border-radius: 16px;
        }

        .login-title {
            color: #174ea6;
        }
    </style>
</head>
<body>

<div class="container px-3">
    <div class="card login-card shadow-sm mx-auto">
        <div class="card-body p-4 p-md-5">

            <div class="text-center mb-4">
                <h3 class="fw-bold login-title">
                    PAKANJADI REPORT
                </h3>

                <p class="text-secondary mb-0">
                    Silakan login untuk melanjutkan
                </p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" role="alert">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('login') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="username" class="form-label">
                        Username
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="username"
                        name="username"
                        value="<?= esc(old('username') ?? '') ?>"
                        maxlength="10"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">
                        Password
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        maxlength="10"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100 py-2"
                >
                    Login
                </button>
            </form>

            <div class="text-center mt-4">
                <small class="text-secondary">
                    Web Pakanjadi Report
                </small>
            </div>

        </div>
    </div>
</div>

</body>
</html>
