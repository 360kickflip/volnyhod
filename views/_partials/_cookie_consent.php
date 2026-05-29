<?php
/**
 * Cookie consent banner + customization modal.
 * Подключается в views/layouts/main.php
 */
use yii\helpers\Url;
?>

<!-- Cookie banner (показывается, если согласие ещё не дано) -->
<div id="vh-cookie-banner" class="vh-cookie-banner" hidden aria-live="polite" role="dialog" aria-label="Уведомление об использовании cookie">
    <div class="vh-cookie-banner__inner">
        <div class="vh-cookie-banner__icon">
            <span style="font-size: 28px;">🍪</span>
        </div>
        <div class="vh-cookie-banner__content">
            <h6 class="mb-1">Мы используем cookies</h6>
            <p class="small mb-0">
                Файлы cookie помогают нам улучшать сервис, запоминать ваши настройки и анализировать трафик.
                Подробнее — в <a href="<?= Url::to(['/page/view', 'slug' => 'privacy']) ?>" target="_blank">политике конфиденциальности</a>.
            </p>
        </div>
        <div class="vh-cookie-banner__actions">
            <button type="button" class="btn btn-soft btn-sm" data-cookie-action="essential">Только обязательные</button>
            <button type="button" class="btn btn-soft btn-sm" data-cookie-action="customize" data-bs-toggle="modal" data-bs-target="#vhCookieModal">Настроить</button>
            <button type="button" class="btn btn-primary btn-sm" data-cookie-action="accept-all">Принять все</button>
        </div>
    </div>
</div>

<!-- Модалка настройки -->
<div class="modal fade" id="vhCookieModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">🍪 Настройки cookie</h5>
                    <small class="text-soft">Выберите, какие cookie разрешить</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p class="small text-soft mb-4">
                    Мы используем cookies, чтобы сделать сервис удобнее. Вы можете включить или отключить отдельные категории.
                    Подробнее — в <a href="<?= Url::to(['/page/view', 'slug' => 'cookies']) ?>">политике cookie</a>.
                </p>

                <div class="vh-cookie-cat">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><i class="fa-solid fa-shield-halved text-success me-2"></i>Обязательные</h6>
                            <span class="small text-soft">Авторизация, корзина, безопасность. Без них сайт не работает.</span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" checked disabled>
                        </div>
                    </div>
                </div>

                <div class="vh-cookie-cat">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><i class="fa-solid fa-sliders text-info me-2"></i>Функциональные</h6>
                            <span class="small text-soft">Запоминают настройки интерфейса, предпочтения, регион.</span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cookie-functional" checked>
                        </div>
                    </div>
                </div>

                <div class="vh-cookie-cat">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><i class="fa-solid fa-chart-line text-primary me-2"></i>Аналитика</h6>
                            <span class="small text-soft">Помогают понять, как используется сайт (Яндекс.Метрика, Google Analytics).</span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cookie-analytics">
                        </div>
                    </div>
                </div>

                <div class="vh-cookie-cat">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><i class="fa-solid fa-bullhorn text-warning me-2"></i>Маркетинг</h6>
                            <span class="small text-soft">Используются для показа релевантной рекламы и измерения её эффективности.</span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="cookie-marketing">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-wrap gap-2">
                <button type="button" class="btn btn-soft" data-cookie-action="essential" data-bs-dismiss="modal">Только обязательные</button>
                <button type="button" class="btn btn-soft" data-cookie-action="save-custom" data-bs-dismiss="modal">Сохранить выбор</button>
                <button type="button" class="btn btn-primary" data-cookie-action="accept-all" data-bs-dismiss="modal">Принять все</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<'JS'
(function () {
    const KEY = 'vh_cookie_consent_v1';
    const banner = document.getElementById('vh-cookie-banner');
    if (!banner) return;

    const fnEl = document.getElementById('cookie-functional');
    const anEl = document.getElementById('cookie-analytics');
    const mkEl = document.getElementById('cookie-marketing');

    function load() {
        try { return JSON.parse(localStorage.getItem(KEY) || 'null'); }
        catch (e) { return null; }
    }
    function save(state) {
        const data = Object.assign({essential: true, functional: true, analytics: false, marketing: false, ts: Date.now()}, state || {});
        localStorage.setItem(KEY, JSON.stringify(data));
        const days = 365;
        const exp = new Date(Date.now() + days * 86400000).toUTCString();
        document.cookie = 'vh_cookie_consent=' + (data.analytics || data.marketing ? 'all' : 'essential') + '; expires=' + exp + '; path=/; SameSite=Lax';
        applyConsent(data);
    }
    function showBanner() { banner.hidden = false; banner.classList.add('is-visible'); }
    function hideBanner() { banner.hidden = true; banner.classList.remove('is-visible'); }
    function syncTogglesFromState() {
        const s = load() || {functional: true, analytics: false, marketing: false};
        if (fnEl) fnEl.checked = !!s.functional;
        if (anEl) anEl.checked = !!s.analytics;
        if (mkEl) mkEl.checked = !!s.marketing;
    }
    function applyConsent(state) {
        window.dispatchEvent(new CustomEvent('vh:cookie-consent', {detail: state}));
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-cookie-action]');
        if (!btn) return;
        const action = btn.dataset.cookieAction;
        if (action === 'accept-all') {
            save({functional: true, analytics: true, marketing: true});
            hideBanner();
        } else if (action === 'essential') {
            save({functional: false, analytics: false, marketing: false});
            hideBanner();
        } else if (action === 'save-custom') {
            save({
                functional: !!(fnEl && fnEl.checked),
                analytics: !!(anEl && anEl.checked),
                marketing: !!(mkEl && mkEl.checked)
            });
            hideBanner();
        } else if (action === 'open-settings') {
            syncTogglesFromState();
            const m = bootstrap.Modal.getOrCreateInstance(document.getElementById('vhCookieModal'));
            m.show();
        }
    });

    const existing = load();
    if (existing) {
        applyConsent(existing);
        syncTogglesFromState();
    } else {
        showBanner();
    }
})();
JS);
?>
