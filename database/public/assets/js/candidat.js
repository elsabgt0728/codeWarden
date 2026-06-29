(function () {
    const overlay  = document.getElementById('modal-profil');
    const btnOuvrir = document.getElementById('btn-ouvrir-profil');
    const btnFermer = document.getElementById('btn-fermer-profil');
    const form     = document.getElementById('form-profil');
    const msg      = document.getElementById('msg-profil');

    if (!overlay || !btnOuvrir) return;

    function ouvrir() {
        overlay.style.position = 'fixed';
        overlay.style.top      = '0';
        overlay.style.left     = '0';
        overlay.style.right    = '0';
        overlay.style.bottom   = '0';
        overlay.style.zIndex   = '9999';
        overlay.style.display  = 'flex';
        document.body.style.overflow = 'hidden';
        btnFermer.focus();
    }

    function fermer() {
        overlay.style.display = 'none';
        document.body.style.overflow = '';
        btnOuvrir.focus();
        msg.style.display = 'none';
        msg.textContent = '';
        ['prenom','nom','email'].forEach(function(f) {
            var el = document.getElementById('err-' + f);
            if (el) el.textContent = '';
        });
    }

    btnOuvrir.addEventListener('click', ouvrir);
    btnFermer.addEventListener('click', fermer);

    // Fermeture Échap
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.style.display !== 'none') fermer();
    });

    // Fermeture clic sur le fond
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) fermer();
    });

    // Soumission AJAX
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Efface erreurs précédentes
        ['prenom','nom','email'].forEach(function(f) {
            document.getElementById('err-' + f).textContent = '';
        });
        msg.style.display = 'none';

        var data = new FormData(form);

        fetch('<?= BASE_URL ?>/profile', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                var strong = document.querySelector('.infouser strong');
                if (strong) strong.textContent = res.nom_complet;

                msg.textContent    = 'Profil mis à jour.';
                msg.className      = 'msg-profil msg-profil--ok';
                msg.style.display  = 'block';
            } else {
                Object.keys(res.errors).forEach(function(f) {
                    var el = document.getElementById('err-' + f);
                    if (el) el.textContent = res.errors[f];
                });
            }
        })
        .catch(function() {
            msg.textContent    = 'Erreur réseau, veuillez réessayer.';
            msg.className      = 'msg-profil msg-profil--err';
            msg.style.display  = 'block';
        });
    });
})();