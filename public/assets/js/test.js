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
