import axios from 'axios';

class DashboardNotificationCenter {
    constructor() {
        this.config = window.GavanKit?.notifications;
        this.root = document.querySelector('[data-dashboard-notifications="root"]');

        if (!this.config || !this.root) {
            return;
        }

        this.badge = this.root.querySelector('[data-notification-badge]');
        this.subtitle = this.root.querySelector('[data-notification-subtitle]');
        this.markAllButton = this.root.querySelector('[data-notification-mark-all]');
        this.toggleButton = this.root.querySelector('[data-dashboard-notifications="toggle"]');
        this.menuElement = document.getElementById('dashboardNotificationsMenu');

        this.lists = {
            notifications: this.root.querySelector('[data-notification-list="notifications"]'),
            logs: this.root.querySelector('[data-notification-list="logs"]'),
        };

        this.badgeCount = 0;
        this.toast = null;
        this.audioCtx = this.createAudioContext();
        this.alertSoundSrc = this.config?.alertSoundUrl || this.defaultAlertSound();
        this.isMarkingAll = false;
        this.alertAudioUnlocked = false;
        this.htmlAudio = this.createHtmlAudio(this.alertSoundSrc);

        this.init();
    }

    init() {
        this.fetchFeed();
        this.subscribeToBroadcasts();
        this.bindMarkAll();
        this.bindToggleMarking();
        this.bindAudioUnlock();
    }

    fetchFeed() {
        if (!this.config?.feedUrl) {
            return;
        }

        axios.get(this.config.feedUrl)
            .then(({ data }) => {
                this.renderSections(data.sections || {});
                this.updateCounts(data.counts || {});
            })
            .catch((error) => {
                console.error('[GavanKit] Failed to load notifications feed.', error);
                this.showFeedbackToast(error?.response?.data?.message || 'Unable to load notifications', 'error');
            });
    }

    renderSections(sections) {
        ['notifications', 'logs'].forEach((category) => {
            const list = this.lists[category];
            if (!list) {
                return;
            }

            list.innerHTML = '';
            const items = sections?.[category] ?? [];

            if (!items.length) {
                list.appendChild(this.createEmptyNode(category));
                return;
            }

            items.forEach((item) => {
                list.appendChild(this.buildItemNode(item));
            });
        });
    }

    createEmptyNode(category) {
        const holder = document.createElement('div');
        holder.className = 'text-gray-500 text-center py-10 fw-semibold';
        holder.dataset.notificationPlaceholder = category;
        holder.textContent = this.config.translations?.empty ?? 'No notifications yet.';
        return holder;
    }

    buildItemNode(item) {
        const wrapper = document.createElement('div');
        wrapper.className = 'd-flex flex-stack py-4 border-bottom border-gray-200';
        wrapper.dataset.notificationItem = item.id || item.uuid || Math.random().toString(36).substring(2, 9);

        const meta = this.iconMeta(item.level);
        const createdAt = item.created_at || this.config.translations?.justNow || 'Just now';

        // Get locale-specific translations
        const locale = document.documentElement.lang || 'en';
        const titleTranslations = item.payload?.title_translations || {};
        const messageTranslations = item.payload?.message_translations || {};

        // Use translated title if available, fallback to item.title
        const title = titleTranslations[locale] || titleTranslations['en'] || item.title || '';
        const message = messageTranslations[locale] || messageTranslations['en'] || item.message || item.payload?.action_label || '';

        const userLabel = item.user_label || item.user?.name || item.user?.email || '';
        const performerLine = userLabel
            ? `<div class="text-gray-500 fs-7">${this.formatPerformer(userLabel)}</div>`
            : '';
        const messageLine = message
            ? `<div class="text-gray-500 fs-7">${this.escapeHtml(message)}</div>`
            : '';

        wrapper.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="symbol symbol-35px me-4">
                    <span class="symbol-label ${meta.background}">
                        <i class="${meta.icon} fs-2 ${meta.color}">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                </div>
                <div class="mb-0 me-2">
                    <div class="fs-6 text-gray-900 fw-semibold">${this.escapeHtml(title)}</div>
                    ${performerLine}
                    ${messageLine}
                </div>
            </div>
            <span class="badge badge-light fs-8">${this.escapeHtml(createdAt)}</span>
        `;

        return wrapper;
    }

    iconMeta(level = 'info') {
        const map = {
            success: { icon: 'ki-duotone ki-check-circle', background: 'bg-light-success', color: 'text-success' },
            warning: { icon: 'ki-duotone ki-information', background: 'bg-light-warning', color: 'text-warning' },
            danger: { icon: 'ki-duotone ki-flash-circle', background: 'bg-light-danger', color: 'text-danger' },
            error: { icon: 'ki-duotone ki-flash-circle', background: 'bg-light-danger', color: 'text-danger' },
            info: { icon: 'ki-duotone ki-information', background: 'bg-light-primary', color: 'text-primary' },
        };

        return map[level] ?? map.info;
    }

    updateCounts(counts) {
        const unreadNotifications = counts?.notifications?.unread ?? counts?.unread ?? 0;
        this.updateBadge(unreadNotifications);

        if (this.subtitle && counts?.total !== undefined) {
            const template = this.config.translations?.subtitle ?? ':count';
            this.subtitle.textContent = template.replace(':count', counts.total);
        }

        if (this.markAllButton) {
            this.markAllButton.disabled = unreadNotifications === 0;
        }

        this.counts = counts;
    }

    updateBadge(count) {
        this.badgeCount = count;

        if (!this.badge) {
            return;
        }

        if (count > 0) {
            this.badge.classList.remove('d-none');
            this.badge.textContent = count > 9 ? '9+' : count;
        } else {
            this.badge.classList.add('d-none');
            this.badge.textContent = '0';
        }
    }

    bindMarkAll() {
        if (!this.markAllButton || !this.config?.markAllUrl) {
            return;
        }

        this.markAllButton.addEventListener('click', (event) => {
            event.preventDefault();
            this.handleMarkAll(false);
        });
    }

    handleMarkAll(auto = false) {
        if (!this.config?.markAllUrl) {
            return;
        }

        if (this.isMarkingAll) {
            return;
        }

        if (auto && this.badgeCount === 0) {
            return;
        }

        this.isMarkingAll = true;

        if (!auto && this.markAllButton) {
            this.markAllButton.disabled = true;
        }

        axios.post(this.config.markAllUrl)
            .then(() => {
                this.updateBadge(0);
                this.fetchFeed();
                if (!auto && this.config.translations?.marked) {
                    this.showFeedbackToast(this.config.translations.marked, 'success');
                }
            })
            .catch((error) => {
                console.error('[GavanKit] Failed to mark notifications as read.', error);
                this.showFeedbackToast(error?.response?.data?.message || 'Unable to update notifications', 'error');
            })
            .finally(() => {
                this.isMarkingAll = false;
                if (!auto && this.markAllButton) {
                    this.markAllButton.disabled = false;
                }
            });
    }

    subscribeToBroadcasts() {
        if (!window.Echo) {
            return;
        }

        window.Echo.private('dashboard.notifications')
            .listen('.Modules\\Activity\\Events\\SystemNotificationCreated', (event) => {
                const bucket = (event.category === 'logs' || event.type === 'audit' || event.type === 'log')
                    ? 'logs'
                    : 'notifications';
                const normalized = this.normalizeEvent(event, bucket);
                this.prependItem(bucket, normalized);
                if (bucket === 'notifications') {
                    this.updateBadge(this.badgeCount + 1);
                }
                this.showNotificationToast(normalized);
            });
    }

    normalizeEvent(event, category) {
        const user = event.payload?.user ?? null;
        const userLabel = user?.name ?? user?.email ?? null;

        return {
            id: event.id,
            uuid: event.uuid,
            title: event.title,
            message: event.message,
            level: event.level,
            type: event.type,
            category,
            payload: event.payload || {},
            created_at: this.config.translations?.justNow || 'Just now',
            user,
            user_label: userLabel,
        };
    }

    prependItem(category, item) {
        const list = this.lists[category];
        if (!list) {
            return;
        }

        const placeholder = list.querySelector('[data-notification-placeholder]');
        if (placeholder) {
            placeholder.remove();
        }

        const node = this.buildItemNode(item);
        list.prepend(node);

        const items = Array.from(list.querySelectorAll('[data-notification-item]'));
        if (items.length > 30) {
            items.pop()?.remove();
        }
    }

    showNotificationToast(item) {
        const template = item.type === 'audit'
            ? this.config.translations?.auditToast
            : this.config.translations?.defaultToast;

        if (!template) {
            return;
        }

        // Get locale-specific title
        const locale = document.documentElement.lang || 'en';
        const titleTranslations = item.payload?.title_translations || {};
        const title = titleTranslations[locale] || titleTranslations['en'] || item.title || '';

        const userName = item.payload?.user?.name || item.payload?.user?.email || '';
        const action = item.payload?.action_label || item.payload?.action || title;
        const message = template
            .replace(':user', userName || this.config.translations?.justNow || '')
            .replace(':action', action)
            .replace(':title', title);

        this.showFeedbackToast(message, this.mapLevelToToastIcon(item.level));
        if (['warning', 'danger', 'error'].includes(item.level)) {
            this.playAlertSound();
        }
    }

    showFeedbackToast(message, icon = 'info') {
        const toast = this.getToastInstance();
        if (!toast) {
            return;
        }

        toast.fire({
            icon,
            title: message,
        });
    }

    getToastInstance() {
        if (typeof window.Swal === 'undefined') {
            return null;
        }

        if (!this.toast) {
            const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
            this.toast = window.Swal.mixin({
                toast: true,
                position: isRtl ? 'top-start' : 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });
        }

        return this.toast;
    }

    mapLevelToToastIcon(level = 'info') {
        switch (level) {
            case 'success':
                return 'success';
            case 'warning':
                return 'warning';
            case 'danger':
            case 'error':
                return 'error';
            default:
                return 'info';
        }
    }

    playAlertSound() {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) {
            return;
        }

        if (!this.audioCtx) {
            this.audioCtx = new AudioContextClass();
        }

        const ctx = this.audioCtx;

        if (ctx.state === 'suspended') {
            ctx.resume().catch(() => { });
        }

        const oscillator = ctx.createOscillator();
        const gain = ctx.createGain();

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(880, ctx.currentTime);
        gain.gain.setValueAtTime(0.12, ctx.currentTime);

        oscillator.connect(gain);
        gain.connect(ctx.destination);

        oscillator.start();
        oscillator.stop(ctx.currentTime + 0.2);
    }

    resolveCategory(type) {
        if (!type) {
            return 'alerts';
        }

        if (['update', 'report'].includes(type)) {
            return 'updates';
        }

        if (['audit', 'log'].includes(type)) {
            return 'logs';
        }

        return 'alerts';
    }

    formatPerformer(userLabel) {
        const template = this.config.translations?.performedBy ?? ':user';
        return template.replace(':user', this.escapeHtml(userLabel));
    }

    bindToggleMarking() {
        if (!this.toggleButton) {
            return;
        }

        const handler = () => this.autoMarkIfNeeded();
        this.toggleButton.addEventListener('click', handler);
        this.toggleButton.addEventListener('mouseenter', handler);
        this.menuElement?.addEventListener('mouseenter', handler);
    }

    autoMarkIfNeeded() {
        if (this.badgeCount === 0) {
            return;
        }

        this.handleMarkAll(true);
    }

    createAudioContext() {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) {
            return null;
        }

        try {
            return new AudioContextClass();
        } catch (error) {
            console.warn('[GavanKit] Unable to initialize audio context.', error);
            return null;
        }
    }

    bindAudioUnlock() {
        if (!this.audioCtx) {
            return;
        }

        const unlock = () => {
            if (this.audioCtx?.state === 'suspended') {
                this.audioCtx.resume().catch(() => { });
            }

            if (this.htmlAudio && !this.alertAudioUnlocked) {
                this.htmlAudio.play()
                    .then(() => {
                        this.htmlAudio.pause();
                        this.htmlAudio.currentTime = 0;
                        this.alertAudioUnlocked = true;
                    })
                    .catch(() => { });
            } else {
                this.alertAudioUnlocked = true;
            }

            targets.forEach((target) => {
                target.removeEventListener('pointerdown', unlock, { passive: true });
                target.removeEventListener('touchstart', unlock, { passive: true });
                target.removeEventListener('click', unlock, { passive: true });
            });
        };

        const targets = [document, this.toggleButton, this.menuElement].filter(Boolean);

        targets.forEach((target) => {
            target.addEventListener('pointerdown', unlock, { passive: true });
            target.addEventListener('touchstart', unlock, { passive: true });
            target.addEventListener('click', unlock, { passive: true });
        });
    }

    playAlertSound() {
        if (this.audioCtx) {
            try {
                if (this.audioCtx.state === 'suspended') {
                    this.audioCtx.resume().catch(() => { });
                }

                const oscillator = this.audioCtx.createOscillator();
                const gain = this.audioCtx.createGain();

                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(900, this.audioCtx.currentTime);
                gain.gain.setValueAtTime(0.18, this.audioCtx.currentTime);

                oscillator.connect(gain);
                gain.connect(this.audioCtx.destination);

                oscillator.start();
                oscillator.stop(this.audioCtx.currentTime + 0.25);
                return;
            } catch (error) {
                console.warn('[GavanKit] AudioContext playback failed, falling back to HTML5 audio.', error);
            }
        }

        if (!this.playHtmlAudio()) {
            console.warn('[GavanKit] Unable to play alert sound.');
        }
    }

    playHtmlAudio() {
        const source = this.alertSoundSrc;
        if (!source) {
            return false;
        }

        try {
            const node = this.htmlAudio ?? new Audio(source);
            const clone = node.cloneNode(true);
            clone.volume = 0.4;
            clone.play().catch(() => { });
            return true;
        } catch (error) {
            console.warn('[GavanKit] Unable to play alert sound.', error);
            return false;
        }
    }

    defaultAlertSound() {
        // 200ms sine beep generated offline, license-free
        return 'data:audio/wav;base64,UklGRiQAAABXQVZFZm10IBAAAAABAAEAIlYAAESsAAACABAAZGF0YQAAAEhJSUlJSUlJSUlJSUlJSUlJSUlJSUlJSUlJSUlJSUlJSUlJSUlJSUlJSUlJSUlJSU=';
    }

    createHtmlAudio(source) {
        if (!source || typeof Audio === 'undefined') {
            return null;
        }

        try {
            const audio = new Audio(source);
            audio.preload = 'auto';
            audio.volume = 0.4;
            audio.crossOrigin = 'anonymous';
            return audio;
        } catch (error) {
            console.warn('[GavanKit] Unable to preload alert audio.', error);
            return null;
        }
    }

    escapeHtml(value) {
        if (typeof value !== 'string') {
            return value ?? '';
        }
        return value
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
}

(() => new DashboardNotificationCenter())();
