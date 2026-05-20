
    // Jalankan fungsi pasang event handler
    function initNavbarStorage() {
        const navLinks = document.querySelectorAll('.nav-links a');

        // BACA halaman aktif terakhir yang disimpan di memori browser (Session Storage)
        const activeNavId = sessionStorage.getItem('activeNav');
        
        if (activeNavId) {
            navLinks.forEach(link => {
                if (link.getAttribute('href') === activeNavId) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        // DENGARKAN setiap kali ada klik pada menu navigasi
        navLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                // Hapus class active dari semua menu
                navLinks.forEach(item => item.classList.remove('active'));
                
                // Tambahkan ke yang diklik saat ini
                this.classList.add('active');
                
                // Simpan pilihan ke memori browser agar saat rute berpindah nilainya tidak hilang
                sessionStorage.setItem('activeNav', this.getAttribute('href'));
            });
        });
    }

    // Eksekusi langsung
    document.addEventListener("DOMContentLoaded", initNavbarStorage);