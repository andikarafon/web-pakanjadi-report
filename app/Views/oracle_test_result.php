
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengujian Oracle PDOOCI</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px auto;
            max-width: 900px;
            padding: 0 20px;
            color: #222;
        }

        .status {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            background: #f2f2f2;
        }

        .success {
            color: #166534;
            background: #dcfce7;
        }

        .error {
            color: #991b1b;
            background: #fee2e2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        code {
            background: #f3f4f6;
            padding: 2px 5px;
        }
    </style>
</head>
<body>

    <h1>Pengujian Oracle PDOOCI</h1>

    <div class="status <?= $status === 'BERHASIL' ? 'success' : 'error' ?>">
        <strong>Status: <?= esc($status) ?></strong>
        <p><?= esc($message) ?></p>
    </div>

    <?php if ($status === 'BERHASIL'): ?>
        <h2>Data dari AKSES_KELOLOSANPAKAN</h2>

        <?php if (empty($rows)): ?>
            <p>Query berhasil, tetapi tidak ada data yang dikembalikan.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Status Akses</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= esc($row['USERNAME'] ?? '') ?></td>
                            <td><?= esc($row['STATUS_AKSES'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>
