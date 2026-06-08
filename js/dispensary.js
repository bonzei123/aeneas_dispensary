(function() {
    // ==========================================
    // 1. LOGIK FÜR DIE NORMALE USER-ANSICHT
    // ==========================================
    const userContainer = document.getElementById('aeneas-dispensary');
    if (userContainer) {
        const messageEl = document.getElementById('aeneas-dispensary-message');

        function showMessage(text, isError) {
            messageEl.textContent = text;
            messageEl.style.color = isError ? 'red' : 'green';
        }

        userContainer.querySelectorAll('.abgabe-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const amount = parseInt(btn.getAttribute('data-amount'), 10);

                fetch(OC.generateUrl('/apps/aeneas_dispensary/dispensary/add'), {
                    method: 'POST',
                    headers: {
                        'requesttoken': OC.requestToken,
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'amount=' + encodeURIComponent(amount)
                })
                .then(r => r.json().then(j => ({ status: r.status, body: j })))
                .then(({ status, body }) => {
                    if (status !== 200) {
                        showMessage(body.error || 'Fehler bei der Abgabe.', true);
                    } else {
                        showMessage('Abgabe gespeichert. Heute: ' + body.day + ' g, Monat: ' + body.month + ' g.', false);
                    }
                })
                .catch(() => showMessage('Netzwerkfehler.', true));
            });
        });
    }

    // ==========================================
    // 2. LOGIK FÜR DIE ADMIN-ANSICHT
    // ==========================================
    const adminContainer = document.getElementById('aeneas-dispensary-admin');
    if (adminContainer) {
        const adminMessageEl = document.getElementById('aeneas-admin-message');

        function showAdminMessage(text, isError) {
            adminMessageEl.textContent = text;
            adminMessageEl.style.color = isError ? 'red' : 'green';
            setTimeout(() => { adminMessageEl.textContent = ''; }, 5000);
        }

        adminContainer.querySelectorAll('.edit-abgabe-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const currentAmount = btn.getAttribute('data-current');
                
                // Simples Prompt-Fenster für die Eingabe
                const newAmountStr = prompt(`Neue Menge für Abgabe ID ${id} eingeben (aktuell: ${currentAmount}g):`, currentAmount);
                
                // Abbruch durch den User
                if (newAmountStr === null || newAmountStr.trim() === "") {
                    return; 
                }

                const newAmount = parseInt(newAmountStr, 10);
                if (isNaN(newAmount) || newAmount < 0) {
                    alert("Bitte eine gültige positive Zahl eingeben.");
                    return;
                }

                // Request an das Backend senden
                fetch(OC.generateUrl('/apps/aeneas_dispensary/admin/update'), {
                    method: 'POST',
                    headers: {
                        'requesttoken': OC.requestToken,
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    // Der Controller erwartet $id und $amount als Parameter
                    body: `id=${encodeURIComponent(id)}&amount=${encodeURIComponent(newAmount)}`
                })
                .then(r => r.json().then(j => ({ status: r.status, body: j })))
                .then(({ status, body }) => {
                    if (status !== 200) {
                        showAdminMessage(body.error || 'Fehler beim Ändern der Daten.', true);
                    } else {
                        showAdminMessage('Erfolgreich geändert! Lade Tabelle neu...', false);
                        // Kurze Pause, damit der Admin das grüne Feedback sieht, dann Page Reload
                        setTimeout(() => window.location.reload(), 1000);
                    }
                })
                .catch(() => showAdminMessage('Netzwerkfehler beim Speichern.', true));
            });
        });
    }
})();