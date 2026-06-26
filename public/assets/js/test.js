function debug(msg) {
    let box = document.getElementById("debugBox");
    if (!box) {
        box = document.createElement("div");
        box.id = "debugBox";
        box.style.position = "fixed";
        box.style.bottom = "10px";
        box.style.left = "10px";
        box.style.padding = "10px";
        box.style.background = "rgba(0,0,0,0.7)";
        box.style.color = "white";
        box.style.zIndex = "999999";
        box.style.fontSize = "14px";
        document.body.appendChild(box);
    }
    box.textContent = msg;
}


document.addEventListener('DOMContentLoaded', () => {

    let current = 0;

    const questions = document.querySelectorAll('.question');
    const btnPrev = document.querySelector('.question-nav .btn-nav:first-child');
    const btnNext = document.querySelector('.question-nav .btn-nav:last-child');
    const boxes = document.getElementById('box-container');
    const progress = document.getElementById('progres');
    const count = document.getElementById('count');
    const submitBtn = document.getElementById('submit');

    if (!questions.length) {
        console.error("Aucune question trouvée !");
        return;
    }

    // Génération des cases numérotées
    questions.forEach((q, i) => {
        const div = document.createElement('div');
        div.classList.add('question-box');
        if (i === 0) div.classList.add('active');
        div.textContent = i + 1;
        div.dataset.index = i;
        boxes.appendChild(div);
    });

    // Vérifie si toutes les questions ont une réponse
    function checkCompletion() {
        let answered = 0;

        questions.forEach((q, i) => {
            if (document.querySelector(`input[name="q${i}"]:checked`)) {
                answered++;
            }
        });

        submitBtn.disabled = answered !== questions.length;

        let percent = Math.round((answered / questions.length) * 100);
        progress.style.width = percent + "%";
        count.textContent = percent;
    }

    // Mise à jour affichage navigation
    function updateDisplay() {
        questions.forEach((q, i) => {
            q.style.display = (i === current) ? 'block' : 'none';
        });

        btnPrev.disabled = current === 0;
        btnNext.disabled = current === questions.length - 1;

        document.querySelectorAll('.question-box').forEach(box => {
            box.classList.remove('active');
            if (parseInt(box.dataset.index) === current) {
                box.classList.add('active');
            }
        });
    }

    // Navigation boutons
    btnNext.addEventListener('click', () => {
        if (current < questions.length - 1) {
            current++;
            updateDisplay();
        }
    });

    btnPrev.addEventListener('click', () => {
        if (current > 0) {
            current--;
            updateDisplay();
        }
    });

    // Navigation via les cases numérotées
    document.querySelectorAll('.question-box').forEach(box => {
        box.addEventListener('click', () => {
            current = parseInt(box.dataset.index);
            updateDisplay();
        });
    });

    // Quand l’utilisateur sélectionne une réponse
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
    checkCompletion();

    // Marquer la question comme répondue
    const qIndex = parseInt(radio.name.replace("q", ""));
    const box = document.querySelector(`.question-box[data-index="${qIndex}"]`);
    if (box) {
        box.classList.add('answered');
    }
});

    });

    // Redirection quand le test est terminé
    submitBtn.addEventListener('click', () => {
        if (!submitBtn.disabled) {
            window.location.href = BASE_URL + "/candidat?page=finish";
        }
    });

    // Initialisation
    updateDisplay();
    checkCompletion();
});

let timeLeft = TEST_DURATION; // secondes
let alertShown = false;

const timerElement = document.getElementById("timer");

function updateTimer() {

    debug("timeLeft = " + timeLeft); // 🔍 DEBUG

    let minutes = Math.floor(timeLeft / 60);
    let seconds = timeLeft % 60;

    timerElement.textContent =
        minutes + "m " + (seconds < 10 ? "0" : "") + seconds + "s";

    // 🔥 Alerte visuelle à 10 secondes
    if (timeLeft <= 10 && !alertShown) {
        alertShown = true;
        timerElement.classList.add("timer-alert");
        debug("ALERTE TRIGGER"); // 🔍 DEBUG
    }

    // ⏰ Temps écoulé → expire + redirection
    if (timeLeft <= 0) {
        debug("EXPIRE TRIGGER"); // 🔍 DEBUG

        clearInterval(timerInterval);

    fetch(BASE_URL + "/candidat?page=expire", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id_passage: ID_PASSAGE,
            id_test: ID_TEST
        })
    })
    .then(response => {
        debug("FETCH STATUS = " + response.status);

        if (!response.ok) {
            debug("FETCH FAILED: " + response.status);
            return;
        }

        debug("REDIRECTION...");
        window.location.href =
            BASE_URL + "/candidat?page=finish&id_passage=" + ID_PASSAGE;
    })
    .catch(err => {
        debug("FETCH ERROR: " + err);
    });



        return;
    }

    timeLeft--;
}

const timerInterval = setInterval(updateTimer, 1000);
updateTimer();
