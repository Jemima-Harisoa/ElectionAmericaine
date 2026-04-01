<?php

namespace app\services;

use app\repositories\ResultRepository;
use Dompdf\Dompdf;

class PdfService
{
    private ResultRepository $resultRepository;

    public function __construct(ResultRepository $resultRepository)
    {
        $this->resultRepository = $resultRepository;
    }

    /**
     * Génère un PDF avec les résultats de l'élection
     * et déclenche le téléchargement
     */
    public function exportResultsPDF(int $electionId): void
    {
        $results = $this->resultRepository->getElectionResults($electionId);
        $winner = $this->resultRepository->getElectionWinner($electionId);

        // Générer le contenu HTML du PDF
        $html = $this->generatePdfContent($results, $winner);

        // Créer une instance de Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Envoyer le PDF au navigateur avec le bon header
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="resultats_election_2020.pdf"');

        echo $dompdf->output();
        exit;
    }

    /**
     * Génère le contenu HTML pour le PDF
     */
    private function generatePdfContent(array $results, ?array $winner): string
    {
        ob_start();
        extract(['results' => $results, 'winner' => $winner]);
        include(__DIR__ . '/../views/results/pdf_template.php');
        return ob_get_clean();
    }
}
