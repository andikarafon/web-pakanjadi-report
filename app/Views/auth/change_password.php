<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password - Web Pakan Jadi Report</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .password-container {
            width: 100%;
            max-width: 440px;
            padding: 32px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }

        .page-title {
            margin: 0 0 8px;
            font-size: 24px;
            text-align: center;
        }

        .page-description {
            margin: 0 0 24px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.5;
            text-align: center;
        }

        .alert {
            padding: 12px 14px;
            margin-bottom: 18px;
            border-radius: 6px;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-error {
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fecaca;
        }

        .alert-success {
            color: #166534;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 600;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
        }

        input[type="password"]:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .help-text {
            display: block;
            margin-top: 6px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 24px;
        }

        .button {
            flex: 1;
            display: inline-block;
            padding: 12px 16px;
            border: none;
            border-radius: 6px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .button-primary {
            color: #ffffff;
            background: #2563eb;
        }

        .button-primary:hover {
            background: #1d4ed8;
        }

        .button-secondary {
            color: #334155;
            background: #e2e8f0;
        }

        .button-secondary:hover {
            background: #cbd5e1;
        }

        @media (max-width: 480px) {
            .password-container {
                padding: 24px 20px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <div class="password-container">
        <h1 class="page-title">Ubah Password</h1>

        <p class="page-description">
            Silakan masukkan password lama dan password baru Anda.
        </p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error" role="alert">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="alert">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error" role="alert">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('ubah-password') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="old_password">Password Lama</label>
                <input
                    type="password"
                    id="old_password"
                    name="old_password"
                    maxlength="10"
                    autocomplete="current-password"
                    required
                >
            </div>

            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    maxlength="10"
                    autocomplete="new-password"
                    required
                >
                <small class="help-text">
                    Maksimal 10 karakter sesuai struktur kolom PASS pada database saat ini.
                </small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    maxlength="10"
                    autocomplete="new-password"
                    required
                >
            </div>

            <div class="button-group">
                <button type="submit" class="button button-primary">
                    Simpan Password
                </button>

                <a href="<?= site_url('dashboard') ?>" class="button button-secondary">
                    Kembali
                </a>
            </div>
        </form>
    </div>

</body>
</html>
