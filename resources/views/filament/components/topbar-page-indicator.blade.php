<div 
    x-data="{ 
        title: '',
        updateTitle() {
            const heading = document.querySelector('.fi-page-header-heading') || document.querySelector('h1.fi-header-heading') || document.querySelector('.fi-header-heading');
            if (heading && heading.innerText.trim()) {
                this.title = heading.innerText.trim();
            } else {
                const doc = document.title.split(' - ')[0] || '';
                const cleaned = doc.replace('SIMONIKA', '').replace('|', '').trim();
                this.title = cleaned || 'Dashboard';
            }
        }
    }"
    x-init="
        updateTitle();
        setTimeout(() => updateTitle(), 150);
        setTimeout(() => updateTitle(), 500);
        document.addEventListener('livewire:navigated', () => {
            setTimeout(() => updateTitle(), 150);
        });
        window.addEventListener('popstate', () => {
            setTimeout(() => updateTitle(), 150);
        });
    "
    class="fi-topbar-page-indicator"
    style="display: inline-flex; align-items: center; gap: 10px; margin-left: 12px;"
>
    {{-- Vertical divider --}}
    <div style="height: 18px; width: 1.5px; background: #cbd5e1; border-radius: 99px;" class="dark:bg-slate-700"></div>

    {{-- Breadcrumb trail --}}
    <div style="display: flex; align-items: center; gap: 7px;">
        <span style="font-size: 0.74rem; font-weight: 800; color: #64748b; letter-spacing: 0.05em; text-transform: uppercase;" class="dark:text-slate-400">
            SIMONIKA
        </span>
        <svg style="width: 11px; height: 11px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
        </svg>
        <span 
            x-text="title || 'Dashboard'"
            style="font-size: 0.86rem; font-weight: 800; color: #0f172a; max-width: 360px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; letter-spacing: -0.01em;"
            class="dark:text-slate-100"
        ></span>
    </div>
</div>

