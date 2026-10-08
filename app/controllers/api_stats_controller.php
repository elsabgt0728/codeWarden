<?php

function _api_json(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    exit;
}

function _api_filtres(): array
{
    return [
        'date_debut' => $_GET['date_debut'] ?? null,
        'date_fin'   => $_GET['date_fin']   ?? null,
        'id_test'    => $_GET['id_test']    ?? null,
        'statut'     => $_GET['statut']     ?? null,
    ];
}

// GET /api/stats/candidat/:id
function api_stats_candidat(int $id): void
{
    require_once ROOT . '/app/models/stats.php';

    $est_admin    = !empty($_SESSION['id_admin']);
    $est_candidat = !empty($_SESSION['id_candidat']) && (int) $_SESSION['id_candidat'] === $id;

    if (!$est_admin && !$est_candidat) {
        _api_json(['erreur' => 'Non autorisé'], 403);
    }

    $candidat = trouver_candidat_par_id($id);
    if (!$candidat) {
        _api_json(['erreur' => 'Candidat introuvable'], 404);
    }

    // Un admin ne peut consulter que les candidats de son propre établissement
    // (avant : un admin pouvait consulter n'importe quel candidat de la plateforme)
    if ($est_admin && (int)$candidat['id_etablissement'] !== (int)$_SESSION['id_etablissement']) {
        _api_json(['erreur' => 'Non autorisé'], 403);
    }

    $data = stats_candidat($id, _api_filtres());

    _api_json([
        'candidat' => [
            'id_candidat' => (int) $candidat['id_candidat'],
            'nom'         => $candidat['nom'],
            'prenom'      => $candidat['prenom'],
            'email'       => $candidat['email'],
        ],
        'passages' => $data['passages'],
        'resume'   => $data['resume'],
    ]);
}

// GET /api/stats/admin/global
function api_stats_admin_global(): void
{
    if (empty($_SESSION['id_admin'])) {
        _api_json(['erreur' => 'Non autorisé'], 403);
    }

    require_once ROOT . '/app/models/stats.php';

    $data = stats_admin_global(_api_filtres(), $_SESSION['id_etablissement']);
    _api_json($data);
}

// GET /api/stats/admin/epreuve/:id
function api_stats_admin_epreuve(int $id): void
{
    if (empty($_SESSION['id_admin'])) {
        _api_json(['erreur' => 'Non autorisé'], 403);
    }

    require_once ROOT . '/app/models/stats.php';

    $data = stats_admin_epreuve($id, _api_filtres(), $_SESSION['id_etablissement']);
    _api_json($data);
}

// GET /api/stats/admin/comparaison
function api_stats_admin_comparaison(): void
{
    if (empty($_SESSION['id_admin'])) {
        _api_json(['erreur' => 'Non autorisé'], 403);
    }

    require_once ROOT . '/app/models/stats.php';

    $tests = stats_admin_comparaison(_api_filtres(), $_SESSION['id_etablissement']);
    _api_json(['tests' => $tests]);
}
