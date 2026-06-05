<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Database;
use App\Repository\EmployeeRepository;
use Throwable;

final class EmployeeController
{
    public function dashboard(): void
    {
        if (!$this->isEmployee()) {
            redirect('/connexion');
        }

        view('employee/dashboard', [
            'title' => 'Espace employe',
            'reviews' => $this->reviewsForDashboard(),
            'incidents' => $this->incidentsForDashboard(),
            'success' => $_SESSION['flash_success'] ?? null,
            'error' => $_SESSION['flash_error'] ?? null,
        ]);
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
    }

    public function validateReview(): void
    {
        $this->moderateReview('valide');
    }

    public function rejectReview(): void
    {
        $this->moderateReview('refuse');
    }

    private function moderateReview(string $status): void
    {
        if (!$this->isEmployee()) {
            redirect('/connexion');
        }

        $reviewId = (int) ($_POST['review_id'] ?? 0);
        if ($reviewId > 0) {
            try {
                $repository = new EmployeeRepository(Database::connection());
                $updated = $repository->moderateReview($reviewId, (int) current_user()['id'], $status);

                if (!$updated) {
                    $_SESSION['flash_error'] = 'Avis introuvable ou deja modere.';
                    redirect('/employe');
                }

                $this->updateSessionReviewStatus($reviewId, $status);
                $_SESSION['flash_success'] = $status === 'valide' ? 'Avis valide.' : 'Avis refuse.';
                redirect('/employe');
            } catch (Throwable) {
                // Si la base est indisponible, le prototype poursuit avec les donnees de session.
            }
        }

        $index = (int) ($_POST['index'] ?? -1);
        if (!isset($_SESSION['pending_reviews'][$index])) {
            $_SESSION['flash_error'] = 'Avis introuvable.';
            redirect('/employe');
        }

        $_SESSION['pending_reviews'][$index]['statut'] = $status;
        $_SESSION['pending_reviews'][$index]['moderated_at'] = date('Y-m-d H:i:s');
        $_SESSION['pending_reviews'][$index]['moderated_by'] = current_user()['pseudo'];
        $_SESSION['flash_success'] = $status === 'valide' ? 'Avis valide.' : 'Avis refuse.';

        redirect('/employe');
    }

    public function resolveIncident(): void
    {
        if (!$this->isEmployee()) {
            redirect('/connexion');
        }

        $incidentId = (int) ($_POST['incident_id'] ?? 0);
        if ($incidentId > 0) {
            try {
                $repository = new EmployeeRepository(Database::connection());
                $updated = $repository->resolveIncident($incidentId, (int) current_user()['id']);

                if (!$updated) {
                    $_SESSION['flash_error'] = 'Incident introuvable ou deja traite.';
                    redirect('/employe');
                }

                $this->updateSessionIncidentStatus($incidentId, 'resolu');
                $_SESSION['flash_success'] = 'Incident marque comme resolu.';
                redirect('/employe');
            } catch (Throwable) {
                // Si la base est indisponible, le prototype poursuit avec les donnees de session.
            }
        }

        $_SESSION['flash_error'] = 'Incident introuvable.';
        redirect('/employe');
    }

    private function isEmployee(): bool
    {
        return is_authenticated() && in_array('ROLE_EMPLOYE', current_user()['roles'], true);
    }

    private function reviewsForDashboard(): array
    {
        $sessionReviews = $_SESSION['pending_reviews'] ?? [];

        try {
            $repository = new EmployeeRepository(Database::connection());
            $sqlReviews = $repository->pendingReviews();
        } catch (Throwable) {
            return $sessionReviews;
        }

        return array_merge($sqlReviews, $sessionReviews);
    }

    private function incidentsForDashboard(): array
    {
        $sessionIncidents = $_SESSION['incidents'] ?? [];

        try {
            $repository = new EmployeeRepository(Database::connection());
            $sqlIncidents = $repository->openIncidents();
        } catch (Throwable) {
            return $sessionIncidents;
        }

        return array_merge($sqlIncidents, $sessionIncidents);
    }

    private function updateSessionReviewStatus(int $reviewId, string $status): void
    {
        foreach ($_SESSION['pending_reviews'] ?? [] as $index => $review) {
            if (!isset($review['id']) || (int) $review['id'] !== $reviewId) {
                continue;
            }

            $_SESSION['pending_reviews'][$index]['statut'] = $status;
            $_SESSION['pending_reviews'][$index]['moderated_at'] = date('Y-m-d H:i:s');
            $_SESSION['pending_reviews'][$index]['moderated_by'] = current_user()['pseudo'];
        }
    }

    private function updateSessionIncidentStatus(int $incidentId, string $status): void
    {
        foreach ($_SESSION['incidents'] ?? [] as $index => $incident) {
            if (!isset($incident['id']) || (int) $incident['id'] !== $incidentId) {
                continue;
            }

            $_SESSION['incidents'][$index]['statut'] = $status;
            $_SESSION['incidents'][$index]['resolved_at'] = date('Y-m-d H:i:s');
        }
    }
}
