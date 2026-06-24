let index = 0;

function afficherQuestion() {
    const question = testData.questions[index];
    const container = document.querySelector('.contenu');

    let html = `
        <div class="question">
            <h2>${question.intitule}</h2>
            <div class="propositions">
    `;

    question.propositions.forEach((p, i) => {
        if (p.type === "texte") {
            html += `
                <label class="prop">
                    <input type="radio" name="q${index}" value="${i}">
                    ${p.label}
                </label>
            `;
        } else {
            html += `
                <label class="prop">
                    <input type="radio" name="q${index}" value="${i}">
                    <img src="${BASE_URL}/${p.src}" alt="image">
                </label>
            `;
        }
    });

    html += `
            </div>
        </div>
    `;

    container.innerHTML = html;

    // Mise à jour progression
    document.getElementById("count").textContent = Math.round(((index+1) / testData.nbquestions) * 100);
}

document.querySelector('.btn-nav:nth-child(1)').addEventListener('click', () => {
    if (index > 0) {
        index--;
        afficherQuestion();
    }
});

document.querySelector('.btn-nav:nth-child(2)').addEventListener('click', () => {
    if (index < testData.nbquestions - 1) {
        index++;
        afficherQuestion();
    }
});

// Chargement initial
afficherQuestion();
