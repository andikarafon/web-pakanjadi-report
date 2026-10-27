
document.addEventListener('DOMContentLoaded', function () {
    const layout = document.querySelector('.app-layout');
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const overlay = document.getElementById('sidebarOverlay');

    const collapseToggle = document.getElementById(
        'sidebarCollapseToggle'
    );

    const collapseIcon = document.getElementById(
        'sidebarToggleIcon'
    );

    if (!layout || !sidebar) {
        return;
    }

    const mobileQuery = window.matchMedia('(max-width: 760px)');

    /*
     * SIDEBAR MOBILE
     * Membuka sidebar dari tombol hamburger.
     */
    function openSidebar() {
        if (!sidebar || !overlay || !menuToggle) {
            return;
        }

        sidebar.classList.add('is-open');
        overlay.classList.add('is-visible');

        menuToggle.setAttribute('aria-expanded', 'true');

        document.body.style.overflow = 'hidden';
    }

    /*
     * SIDEBAR MOBILE
     * Menutup sidebar dan mengembalikan scroll halaman.
     */
    function closeSidebar() {
        if (sidebar && overlay && menuToggle) {
            sidebar.classList.remove('is-open');
            overlay.classList.remove('is-visible');

            menuToggle.setAttribute('aria-expanded', 'false');

            document.body.style.overflow = '';
        }
    }

    /*
     * Event tombol hamburger.
     */
    if (menuToggle && overlay) {
        menuToggle.addEventListener('click', function () {
            if (sidebar.classList.contains('is-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        /*
         * Klik overlay menutup sidebar.
         */
        overlay.addEventListener('click', closeSidebar);
    }

    /*
     * Klik link pada sidebar menutup panel
     * jika sedang berada di tampilan mobile.
     */
    sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (mobileQuery.matches) {
                closeSidebar();
            }
        });
    });

    /*
     * DESKTOP
     * Memperbarui ikon dan keterangan tombol collapse.
     */
    function updateCollapseButton() {
        if (!collapseToggle || !collapseIcon) {
            return;
        }

        const collapsed = layout.classList.contains(
            'sidebar-collapsed'
        );

        collapseToggle.setAttribute(
            'aria-expanded',
            collapsed ? 'false' : 'true'
        );

        collapseToggle.setAttribute(
            'aria-label',
            collapsed ? 'Besarkan sidebar' : 'Kecilkan sidebar'
        );

        collapseToggle.setAttribute(
            'title',
            collapsed ? 'Besarkan sidebar' : 'Kecilkan sidebar'
        );

        collapseIcon.textContent = collapsed ? '▶' : '◀';
    }

    /*
     * DESKTOP
     * Tombol untuk mengecilkan atau memperbesar sidebar.
     */
    if (collapseToggle) {
        collapseToggle.addEventListener('click', function () {
            if (mobileQuery.matches) {
                return;
            }

            layout.classList.toggle('sidebar-collapsed');

            updateCollapseButton();
        });
    }

    /*
     * Tombol Escape menutup sidebar mobile.
     */
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });

    /*
     * Menyesuaikan layout ketika ukuran browser berubah.
     */
    function handleViewportChange() {
        closeSidebar();

        if (mobileQuery.matches) {
            layout.classList.remove('sidebar-collapsed');
        }

        updateCollapseButton();
    }

    window.addEventListener('resize', handleViewportChange);

    /*
     * Menangani perubahan breakpoint secara langsung.
     */
    if (typeof mobileQuery.addEventListener === 'function') {
        mobileQuery.addEventListener(
            'change',
            handleViewportChange
        );
    } else if (typeof mobileQuery.addListener === 'function') {
        // Dukungan browser lama.
        mobileQuery.addListener(handleViewportChange);
    }

    /*
     * Inisialisasi keadaan tombol saat halaman dibuka.
     */
    updateCollapseButton();
});
