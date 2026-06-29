// ── Séquence multi-jeux ─────────────────────────────────────────────────────
const iframe       = document.getElementById('game-frame');
const transitionEl = document.getElementById('transition-screen');
const btnSuivant   = document.getElementById('btn-suivant');

// Restaurer la progression depuis les scores déjà enregistrés en base (refresh)
const scoresInitiaux = typeof SCORES_EXISTANTS !== 'undefined' ? SCORES_EXISTANTS : [];
const scores = scoresInitiaux.map(function(s) {
    const jeu = (typeof JEUX !== 'undefined' ? JEUX : []).find(function(j) { return j.id === s.id_jeux; });
    return { score: s.score, bareme: jeu ? jeu.bareme : 1 };
});
let currentJeu = scores.length;

function chargerJeu(index) {
    const el = document.getElementById('jeu-courant');
    if (el) el.textContent = index + 1;
    iframe.srcdoc = JEUX[index].html;
}

function afficherTransition() {
    if (!transitionEl) { chargerJeu(currentJeu); return; }

    // Précharger le jeu suivant dans l'iframe pendant que l'overlay est affiché
    chargerJeu(currentJeu);

    const restant = JEUX.length - currentJeu;
    document.getElementById('transition-titre').textContent =
        'Exercice ' + currentJeu + ' / ' + JEUX.length + ' terminé !';
    document.getElementById('transition-sub').textContent =
        restant + ' exercice' + (restant > 1 ? 's' : '') + ' restant' + (restant > 1 ? 's' : '');
    btnSuivant.textContent =
        currentJeu === JEUX.length - 1 ? 'Dernier exercice →' : 'Exercice suivant →';

    transitionEl.style.display = 'flex';
}

// Clic sur "Exercice suivant" — le jeu est déjà chargé, on retire l'overlay
if (btnSuivant) {
    btnSuivant.addEventListener('click', function() {
        transitionEl.style.display = 'none';
    });
}

// Démarrage : reprendre au bon jeu
if (typeof JEUX !== 'undefined' && JEUX.length > 0) {
    if (currentJeu >= JEUX.length) {
        // Tous les jeux déjà joués avant le refresh — soumettre directement
        soumettreTout();
    } else {
        chargerJeu(currentJeu);
    }
}

// Réception du score d'un jeu terminé
window.addEventListener('message', function(e) {
    if (!e.data || e.data.type !== 'cw_result') return;

    var jeuIndex = currentJeu;
    var scoreVal = Number(e.data.score) || 0;

    scores.push({
        score:  scoreVal,
        bareme: JEUX[jeuIndex] ? JEUX[jeuIndex].bareme : 1,
    });

    // Sauvegarde immédiate en base → le refresh reprend au bon jeu
    if (JEUX[jeuIndex]) {
        fetch(BASE_URL + '/candidat/score-jeu', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ id_passage: ID_PASSAGE, id_jeu: JEUX[jeuIndex].id, score: scoreVal })
        });
    }

    currentJeu++;

    if (currentJeu < JEUX.length) {
        afficherTransition();   // overlay + précharge jeu suivant
    } else {
        soumettreTout();        // tous terminés → soumettre
    }
});

function soumettreTout() {
    // Score global = moyenne pondérée par barème
    const totalBareme = scores.reduce(function(s, g) { return s + g.bareme; }, 0);
    const scoreGlobal = totalBareme > 0
        ? Math.round(scores.reduce(function(s, g) { return s + (g.score / 100 * g.bareme); }, 0) / totalBareme * 100)
        : Math.round(scores.reduce(function(s, g) { return s + g.score; }, 0) / scores.length);

    // Détail par jeu pour stockage en base — on envoie TOUS les jeux (y compris ceux restaurés)
    const scoresJeux = scores.map(function(g, i) {
        return { id_jeu: JEUX[i] ? JEUX[i].id : 0, score: g.score };
    });

    fetch(BASE_URL + '/candidat/terminer', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({
            id_passage:  ID_PASSAGE,
            id_test:     ID_TEST,
            score:       scoreGlobal,
            scores_jeux: scoresJeux,
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { window.location.href = data.redirect; })
    .catch(function() {
        window.location.href = BASE_URL + '/candidat?page=finish&id_passage=' + ID_PASSAGE;
    });
}

// ── Timer (compte à rebours) ─────────────────────────────────────────────────
const dureeTotal   = typeof TEST_DURATION  !== 'undefined' ? TEST_DURATION  : 0;
const tempsEcoule  = typeof TEMPS_ECOULE   !== 'undefined' ? TEMPS_ECOULE   : 0;
let timeLeft       = Math.max(0, dureeTotal - tempsEcoule);
let alertShown = false;
const timerEl  = document.getElementById('timer');

function updateTimer() {
    if (!timerEl) return;

    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerEl.textContent = minutes + 'm ' + (seconds < 10 ? '0' : '') + seconds + 's';

    if (timeLeft <= 10 && !alertShown) {
        alertShown = true;
        timerEl.classList.add('timer-alert');
    }

    if (timeLeft <= 0) {
        clearInterval(timerInterval);
        fetch(BASE_URL + '/candidat/expirer', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ id_passage: ID_PASSAGE, id_test: ID_TEST })
        })
        .then(function() {
            window.location.href = BASE_URL + '/candidat?page=finish&id_passage=' + ID_PASSAGE;
        })
        .catch(function() { window.location.href = BASE_URL + '/candidat'; });
        return;
    }

    timeLeft--;
}

const timerInterval = setInterval(updateTimer, 1000);
updateTimer();
