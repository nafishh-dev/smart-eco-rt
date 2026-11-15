(() => {
    const toastRegion = document.getElementById('toast-region');

    function showToast(message, type = 'success') {
        if (!toastRegion) return;

        const toast = document.createElement('div');
        toast.className = `toast toast--${type}`;

        const icon = document.createElement('span');
        icon.className = 'toast__icon';
        icon.setAttribute('aria-hidden', 'true');
        icon.textContent = type === 'error' ? '!' : '✓';

        const text = document.createElement('span');
        text.textContent = message;

        toast.append(icon, text);
        toastRegion.append(toast);

        window.setTimeout(() => {
            toast.classList.add('toast--leaving');
            window.setTimeout(() => toast.remove(), 220);
        }, 3600);
    }

    async function postJson(url, payload = {}) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        let result = {};
        try {
            result = await response.json();
        } catch {
            result = {};
        }

        if (!response.ok) {
            throw new Error(result.message || 'Permintaan gagal. Silakan coba kembali.');
        }

        return result;
    }

    function setBusy(button, busy) {
        if (!button) return;

        button.disabled = busy;
        if (busy) {
            button.dataset.originalText = button.textContent.trim();
            button.setAttribute('aria-busy', 'true');
        } else {
            button.removeAttribute('aria-busy');
        }
    }

    function openDialog(id) {
        const dialog = document.getElementById(id);
        if (dialog && typeof dialog.showModal === 'function') dialog.showModal();
    }

    function closeDialog(dialog) {
        if (dialog?.open) dialog.close();
    }

    document.querySelectorAll('[data-open-modal]').forEach((button) => {
        button.addEventListener('click', () => openDialog(button.dataset.openModal));
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => closeDialog(button.closest('dialog')));
    });

    document.querySelectorAll('.action-dialog').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) closeDialog(dialog);
        });
    });

    const otpForm = document.getElementById('otp-form');
    otpForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!otpForm.reportValidity()) return;

        const submitButton = otpForm.querySelector('[type="submit"]');
        const formData = new FormData(otpForm);
        setBusy(submitButton, true);

        try {
            const result = await postJson('/api/claim-otp', {
                otp_code: formData.get('otp_code'),
                citizen_name: formData.get('citizen_name') || null,
            });

            closeDialog(otpForm.closest('dialog'));
            otpForm.reset();
            showToast(`Klaim berhasil. +${result.total_points ?? 0} Poin Eco.`);
            refreshLiveStatus();
        } catch (error) {
            showToast(error.message, 'error');
        } finally {
            setBusy(submitButton, false);
        }
    });

    document.querySelectorAll('[data-servo-direction]').forEach((button) => {
        button.addEventListener('click', async () => {
            setBusy(button, true);

            try {
                const result = await postJson('/api/device/servo', {
                    direction: button.dataset.servoDirection,
                });
                closeDialog(button.closest('dialog'));
                showToast(`Perintah ${result.direction || button.dataset.servoDirection} berhasil dikirim.`);
            } catch (error) {
                showToast(error.message, 'error');
            } finally {
                setBusy(button, false);
            }
        });
    });

    document.querySelectorAll('[data-api-action]').forEach((button) => {
        button.addEventListener('click', async () => {
            setBusy(button, true);

            try {
                const result = await postJson(button.dataset.apiAction);
                showToast(result.message || 'Permintaan berhasil dikirim.');
                refreshLiveStatus();
            } catch (error) {
                showToast(error.message, 'error');
            } finally {
                setBusy(button, false);
            }
        });
    });

    document.querySelectorAll('[data-read-all]').forEach((button) => {
        button.addEventListener('click', async () => {
            setBusy(button, true);

            try {
                const result = await postJson('/api/notifications/read-all');
                document.querySelectorAll('[data-unread-badge]').forEach((badge) => {
                    badge.textContent = '';
                    badge.hidden = true;
                });
                showToast(result.message || 'Semua notifikasi ditandai sudah dibaca.');
                refreshLiveStatus();
            } catch (error) {
                showToast(error.message, 'error');
            } finally {
                setBusy(button, false);
            }
        });
    });

    document.querySelectorAll('[data-empty-bin]').forEach((button) => {
        button.addEventListener('click', async () => {
            const category = button.dataset.emptyBin;
            if (!category) return;

            setBusy(button, true);

            try {
                const result = await postJson(`/api/bins/${encodeURIComponent(category)}/empty`);
                const card = button.closest('[data-notification-card]');

                if (card) {
                    card.dataset.resolved = 'true';
                    card.classList.remove('notification-card--warning');
                    card.classList.add('is-resolved');

                    const state = card.querySelector('[data-notification-state]');
                    if (state) state.textContent = 'Selesai';

                    button.remove();
                }

                showToast(result.message || 'Tempat sampah berhasil dikosongkan.');
                refreshLiveStatus();
            } catch (error) {
                showToast(error.message, 'error');
                setBusy(button, false);
            }
        });
    });

    function updateBinCards(bins) {
        if (!Array.isArray(bins)) return;

        bins.forEach((bin) => {
            const card = document.querySelector(`[data-bin-card="${bin.category}"]`);
            if (!card) return;

            const weight = Number(bin.current_weight);
            const capacity = Math.max(0, Math.min(100, Number(bin.capacity_percent) || 0));
            const weightNode = card.querySelector('[data-bin-weight]');
            const capacityNode = card.querySelector('[data-bin-capacity]');
            const progress = card.querySelector('[data-bin-progress]');
            const progressTrack = card.querySelector('[role="progressbar"]');
            const statusNode = card.querySelector('[data-bin-status]');

            if (weightNode && Number.isFinite(weight)) weightNode.textContent = weight.toFixed(1);
            if (capacityNode) capacityNode.textContent = String(capacity);
            if (progress) progress.style.width = `${capacity}%`;
            if (progressTrack) progressTrack.setAttribute('aria-valuenow', String(capacity));

            if (statusNode) {
                const almostFull = bin.status === 'hampir_penuh';
                statusNode.textContent = almostFull ? 'Hampir Penuh' : 'Kondisi Baik';
                statusNode.classList.toggle('bin-status--warning', almostFull);
                statusNode.classList.toggle('bin-status--safe', !almostFull);
            }
        });
    }

    function updateDeviceState(isOnline) {
        document.querySelectorAll('[data-device-status-text]').forEach((node) => {
            const onlineBadge = node.classList.contains('online-badge');
            node.textContent = isOnline
                ? (onlineBadge ? 'Online (Aktif)' : 'Online')
                : 'Offline';
            node.classList.toggle('online-badge--online', onlineBadge && isOnline);
            node.classList.toggle('online-badge--offline', onlineBadge && !isOnline);
        });

        document.querySelectorAll('[data-device-status]').forEach((node) => {
            node.classList.toggle('status-dot--online', isOnline);
            node.classList.toggle('status-dot--offline', !isOnline);
            node.setAttribute('aria-label', isOnline ? 'Online' : 'Offline');
        });

        const banner = document.querySelector('[data-sensor-banner]');
        if (banner) {
            banner.classList.toggle('sensor-banner--online', isOnline);
            banner.classList.toggle('sensor-banner--offline', !isOnline);

            const message = banner.querySelector('[data-sensor-message]');
            const description = banner.querySelector('[data-sensor-description]');
            if (message) message.textContent = isOnline ? 'Semua Sensor Berfungsi Normal' : 'Perangkat Offline';
            if (description) {
                description.textContent = isOnline
                    ? 'Data diperbarui langsung dari perangkat.'
                    : 'Menunggu perangkat mengirim data terbaru.';
            }
        }
    }

    function updateUnreadCount(count) {
        const unreadCount = Math.max(0, Number(count) || 0);

        document.querySelectorAll('[data-unread-badge]').forEach((badge) => {
            badge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
            badge.hidden = unreadCount === 0;
        });
    }

    async function refreshLiveStatus() {
        const url = document.body.dataset.liveStatusUrl;
        if (!url) return;

        try {
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) throw new Error(`Live status returned ${response.status}`);

            const status = await response.json();
            updateBinCards(status.bins);

            const totalWeight = Number(status.total_weight);
            const totalWeightNode = document.querySelector('[data-total-weight]');
            if (totalWeightNode && Number.isFinite(totalWeight)) {
                totalWeightNode.textContent = totalWeight.toFixed(1);
            }

            updateUnreadCount(status.unread_count);
            updateDeviceState(Boolean(status.is_online));
        } catch (error) {
            console.warn('Tidak dapat memperbarui status Smart Eco-RT.', error);
        }
    }

    refreshLiveStatus();
    window.setInterval(refreshLiveStatus, 4000);
})();
