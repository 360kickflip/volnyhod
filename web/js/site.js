/**
 * Вольный Ход — front-end helpers
 */
(function () {
    'use strict';

    // CSRF helper
    window.VH = window.VH || {};
    VH.csrf = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    };
    VH.csrfParam = () => {
        const meta = document.querySelector('meta[name="csrf-param"]');
        return meta ? meta.content : '_csrf';
    };

    // Format ruble currency
    VH.formatRub = (n) => {
        n = Number(n) || 0;
        return n.toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₽';
    };

    // Format duration HH:MM:SS
    VH.formatDuration = (sec) => {
        sec = Math.max(0, Math.floor(sec));
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        return [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
    };

    // Active rental timer
    document.addEventListener('DOMContentLoaded', () => {
        const timerEl = document.querySelector('[data-rental-timer]');
        if (timerEl) {
            const startedAt = parseInt(timerEl.dataset.startedAt, 10);
            const tick = () => {
                const elapsed = Math.floor(Date.now() / 1000) - startedAt;
                timerEl.textContent = VH.formatDuration(elapsed);
            };
            tick();
            setInterval(tick, 1000);
        }

        // Rental cost auto-refresh (every 30s)
        const costEl = document.querySelector('[data-rental-cost]');
        if (costEl) {
            const url = costEl.dataset.url;
            if (url) {
                const refresh = () => {
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.json())
                        .then(d => {
                            if (d && typeof d.cost !== 'undefined') {
                                costEl.textContent = VH.formatRub(d.cost);
                            }
                        }).catch(() => {});
                };
                setInterval(refresh, 30000);
            }
        }

        // Star input
        document.querySelectorAll('.star-input').forEach(group => {
            const input = document.querySelector('input[name="' + group.dataset.input + '"]');
            const stars = group.querySelectorAll('i');
            const setStars = (val) => {
                stars.forEach((s, i) => s.classList.toggle('is-on', i < val));
            };
            stars.forEach((s, i) => {
                s.addEventListener('mouseenter', () => { group.classList.add('is-hovering'); });
                s.addEventListener('mouseleave', () => { group.classList.remove('is-hovering'); });
                s.addEventListener('click', () => {
                    if (input) input.value = i + 1;
                    setStars(i + 1);
                });
            });
            if (input && input.value) setStars(parseInt(input.value, 10));
        });

        // Auto-mark notifications as read on click
        document.querySelectorAll('[data-notification-id]').forEach(item => {
            item.addEventListener('click', (e) => {
                const id = item.dataset.notificationId;
                fetch('/notifications/read?id=' + id, {
                    method: 'POST',
                    headers: {'X-CSRF-Token': VH.csrf(), 'X-Requested-With': 'XMLHttpRequest'}
                });
            });
        });

        // Promocode apply (booking modal)
        const promoBtn = document.querySelector('[data-promo-apply]');
        if (promoBtn) {
            promoBtn.addEventListener('click', () => {
                const input = document.querySelector('[data-promo-input]');
                const carId = promoBtn.dataset.carId;
                const minutes = document.querySelector('[name="BookingForm[planned_minutes]"]')?.value || 60;
                if (!input.value) return;
                fetch(`/booking/check-promo?code=${encodeURIComponent(input.value)}&car_id=${carId}&minutes=${minutes}`, {
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                })
                    .then(r => r.json())
                    .then(d => {
                        document.querySelector('[data-promo-result]').textContent = d.message || '';
                        document.querySelector('[data-promo-result]').className = 'small ' + (d.success ? 'text-success' : 'text-danger');
                        if (d.success) {
                            document.querySelector('[data-discount-row]')?.classList.remove('d-none');
                            document.querySelector('[data-discount]').textContent = '-' + VH.formatRub(d.discount);
                            document.querySelector('[data-total]').textContent = VH.formatRub(d.total);
                        }
                    });
            });
        }

        // Recompute booking total when planned_minutes changes
        const minutesInput = document.querySelector('[name="BookingForm[planned_minutes]"]');
        if (minutesInput) {
            minutesInput.addEventListener('input', () => {
                const carId = minutesInput.dataset.carId;
                fetch(`/booking/calculate?car_id=${carId}&minutes=${minutesInput.value}`, {
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                })
                    .then(r => r.json())
                    .then(d => {
                        if (d.estimated !== undefined) {
                            document.querySelector('[data-estimated]').textContent = VH.formatRub(d.estimated);
                            document.querySelector('[data-deposit]').textContent = VH.formatRub(d.deposit);
                            document.querySelector('[data-total]').textContent = VH.formatRub(d.total);
                        }
                    });
            });
        }

        // Admin sidebar toggle
        const sbToggle = document.querySelector('[data-admin-sidebar-toggle]');
        const sb = document.querySelector('.admin-sidebar');
        if (sbToggle && sb) {
            sbToggle.addEventListener('click', () => sb.classList.toggle('is-open'));
        }

        // Random promo code generator (admin)
        document.querySelectorAll('[data-generate-code]').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = document.querySelector(btn.dataset.target);
                if (!target) return;
                const alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                let code = '';
                for (let i = 0; i < 8; i++) code += alphabet[Math.floor(Math.random() * alphabet.length)];
                target.value = code;
            });
        });
    });
})();
