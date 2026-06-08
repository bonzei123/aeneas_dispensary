(function() {
    // ==========================================
    // 1. LOGIK FÜR DIE NORMALE USER-ANSICHT
    // ==========================================
    const userContainer = document.getElementById('aeneas-dispensary');
    if (userContainer) {
        const messageEl = document.getElementById('aeneas-dispensary-message');

        function showMessage(text, isError) {
            messageEl.textContent = text;
            messageEl.classList.remove('success', 'error');
            messageEl.classList.add(isError ? 'error' : 'success');
        }

        userContainer.querySelectorAll('.abgabe-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.disabled = true;
                const amount = parseInt(btn.getAttribute('data-amount'), 10);

                fetch(OC.generateUrl('/apps/aeneas_dispensary/dispensary/add'), {
                    method: 'POST',
                    headers: {
                        'requesttoken': OC.requestToken,
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'amount=' + encodeURIComponent(amount)
                })
                .then(async r => {
                    const isJson = r.headers.get('content-type')?.includes('application/json');
                    const body = isJson ? await r.json() : null;
                    return { status: r.status, body };
                })
                .then(({ status, body }) => {
                    if (status !== 200) {
                        showMessage((body && body.error) ? body.error : 'Fehler bei der Abgabe (Server-Response ' + status + ').', true);
                    } else {
                        showMessage('Abgabe gespeichert. Heute: ' + body.day + ' g, Monat: ' + body.month + ' g.', false);
                    }
                })
                .catch(err => {
                    console.error('Fetch Error:', err);
                    showMessage('Netzwerk- oder Serverfehler.', true);
                })
                .finally(() => {
                    btn.disabled = false;
                });
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
            adminMessageEl.classList.remove('success', 'error');
            adminMessageEl.classList.add(isError ? 'error' : 'success');
            setTimeout(() => { adminMessageEl.textContent = ''; }, 5000);
        }

        adminContainer.querySelectorAll('.edit-abgabe-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const currentAmount = btn.getAttribute('data-current');
                
                const newAmountStr = prompt(`Neue Menge für Abgabe ID ${id} eingeben (aktuell: ${currentAmount}g):`, currentAmount);
                
                if (newAmountStr === null || newAmountStr.trim() === "") {
                    return; 
                }

                const newAmount = parseInt(newAmountStr, 10);
                if (isNaN(newAmount) || newAmount <= 0) {
                    alert("Bitte eine gültige positive Zahl eingeben.");
                    return;
                }

                btn.disabled = true;

                fetch(OC.generateUrl('/apps/aeneas_dispensary/admin/update'), {
                    method: 'POST',
                    headers: {
                        'requesttoken': OC.requestToken,
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id=${encodeURIComponent(id)}&amount=${encodeURIComponent(newAmount)}`
                })
                .then(async r => {
                    const isJson = r.headers.get('content-type')?.includes('application/json');
                    const body = isJson ? await r.json() : null;
                    return { status: r.status, body };
                })
                .then(({ status, body }) => {
                    if (status !== 200) {
                        showAdminMessage((body && body.error) ? body.error : 'Fehler beim Ändern der Daten.', true);
                        btn.disabled = false;
                    } else {
                        showAdminMessage('Erfolgreich geändert! Lade Tabelle neu...', false);
                        setTimeout(() => window.location.reload(), 1000);
                    }
                })
                .catch(err => {
                    console.error('Fetch Error:', err);
                    showAdminMessage('Netzwerk- oder Serverfehler beim Speichern.', true);
                    btn.disabled = false;
                });
            });
        });
    }
})();