<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de l'élection 2020</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #1e2a3a;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 2px solid #457bdd;
            padding-bottom: 1rem;
        }

        .header h1 {
            font-size: 28px;
            color: #457bdd;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #6b7f99;
            font-size: 14px;
        }

        .results-section {
            margin-bottom: 2rem;
        }

        .results-section h2 {
            font-size: 18px;
            color: #1e2a3a;
            margin-bottom: 1rem;
            border-bottom: 1px solid #d5dfef;
            padding-bottom: 0.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        thead {
            background-color: #f0f4f8;
        }

        th {
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #457bdd;
            color: #1e2a3a;
        }

        td {
            padding: 0.75rem;
            border-bottom: 1px solid #d5dfef;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .winner-box {
            background-color: #e6f5e6;
            border-left: 4px solid #2d7d2d;
            padding: 1.5rem;
            border-radius: 4px;
            margin-top: 1.5rem;
            text-align: center;
        }

        .winner-box h3 {
            font-size: 18px;
            color: #2d7d2d;
            margin-bottom: 0.5rem;
        }

        .winner-box p {
            font-size: 24px;
            font-weight: bold;
            color: #2d7d2d;
            margin-bottom: 0.5rem;
        }

        .winner-box .electors {
            font-size: 14px;
            color: #6b7f99;
        }

        .footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #d5dfef;
            color: #6b7f99;
            font-size: 12px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🗳️ Élection présidentielle américaine 2020</h1>
            <p>Résultats finaux - Grands électeurs</p>
        </div>

        <div class="results-section">
            <h2>Résultats par candidat</h2>
            
            <?php if (!empty($results)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Candidat</th>
                            <th style="text-align: right;">Grands électeurs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $candidate): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $candidate['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="text-align: right; font-weight: 500;">
                                    <?= (int) $candidate['total_electors'] ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #6b7f99;">Aucune donnée disponible pour cette élection.</p>
            <?php endif; ?>

            <?php if (!empty($winner)): ?>
                <div class="winner-box">
                    <h3>🏆 Vainqueur</h3>
                    <p><?= htmlspecialchars((string) $winner['name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="electors"><?= (int) $winner['total_electors'] ?> grands électeurs</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Document généré le <?= date('d/m/Y à H:i') ?></p>
            <p>Élection Américaine - Système de gestion des votes</p>
        </div>
    </div>
</body>
</html>
