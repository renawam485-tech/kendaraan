/* ============================================================
 * public/js/help.js
 * Alpine.js component untuk halaman Pusat Bantuan — Drivora
 * ============================================================ */

function helpPage() {
    return {

        /* ── State ─────────────────────────────────────────── */
        sections    : window.HELP_SECTIONS || [],
        search      : '',
        activeSection: 'all',
        openItems   : {},   // { 'sec.id-idx': true/false }

        /* ── Init ──────────────────────────────────────────── */
        init() {
            // Buka item pertama setiap section secara default
            (window.HELP_SECTIONS || []).forEach(sec => {
                if (sec.items && sec.items.length > 0) {
                    this.openItems[sec.id + '-0'] = true;
                }
            });
        },

        /* ── Toggle accordion ──────────────────────────────── */
        toggle(sectionId, idx) {
            const key = sectionId + '-' + idx;
            this.openItems[key] = !this.openItems[key];
        },

        isOpen(sectionId, idx) {
            return !!this.openItems[sectionId + '-' + idx];
        },

        /* ── Filtered sections ─────────────────────────────── */
        filteredSections() {
            const q = this.search.trim().toLowerCase();

            return this.sections
                .filter(sec => {
                    if (this.activeSection !== 'all' && sec.id !== this.activeSection) return false;
                    return true;
                })
                .map(sec => {
                    if (!q) return sec;

                    const matchedItems = sec.items.filter(item => {
                        const questionText = item.q.toLowerCase();
                        const answerText   = item.a.replace(/<[^>]+>/g, '').toLowerCase();
                        return questionText.includes(q) || answerText.includes(q);
                    });

                    return matchedItems.length ? { ...sec, items: matchedItems } : null;
                })
                .filter(Boolean);
        },

        /* ── Total visible items (untuk counter) ───────────── */
        totalVisible() {
            return this.filteredSections().reduce((sum, sec) => sum + sec.items.length, 0);
        },

        /* ── Highlight kata kunci di dalam pertanyaan ──────── */
        highlight(text) {
            if (!this.search.trim()) return text;

            const escaped = this.search.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex   = new RegExp('(' + escaped + ')', 'gi');
            return text.replace(regex, '<mark class="help-hl">$1</mark>');
        },

        /* ── Icon path map ─────────────────────────────────── */
        iconPath(key) {
            const icons = {
                'car'         : 'M8 17a2 2 0 11-4 0 2 2 0 014 0zM18 17a2 2 0 11-4 0 2 2 0 014 0zM3 9h18M7 3h10l2 6H5L7 3z',
                'info'        : 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'bell'        : 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                'user'        : 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                'check-circle': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'chart'       : 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'clipboard'   : 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'map'         : 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
                'truck'       : 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 2h8l2-2zM13 16l2-5h4l2 5H13z',
                'users'       : 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            };
            return icons[key] || icons['info'];
        },

    };
}
