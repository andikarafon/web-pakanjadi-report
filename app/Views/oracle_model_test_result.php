<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengujian Model Oracle CI4</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px auto;
            max-width: 1000px;
            padding: 0 15px;
            color: #222;
        }

        .result {
            padding: 15px;
            margin: 15px 0 25px;
            border-radius: 5px;
        }

        .success {
            background: #d1fae5;
            color: #065f46;
        }

        .failed {
            background: #fee2e2;
            color: #991b1b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }
    </style>
</head>
<body>

    <h1>Pengujian Model Oracle CI4</h1>

    <div class="result <?= $status === 'BERHASIL' ? 'success' : 'failed' ?>">
        <strong>Status: <?= esc($status) ?></strong>
        <p><?= esc($message) ?></p>
    </div>

    <?php if ($status === 'BERHASIL'): ?>
        <h2>Data AKSES_KELOLOSANPAKAN</h2>

        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Status Akses</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="2">Tidak ada data.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= esc($row['USERNAME'] ?? '') ?></td>
                            <td><?= esc($row['STATUS_AKSES'] ?? '') ?></td>
                        </tr>
                    <?php endforeach ?>
                <?php endif ?>
            </tbody>
        </table>
    <?php endif ?>

</body>
</html>
