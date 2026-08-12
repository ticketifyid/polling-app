(function () {
    'use strict';

    var step1 = document.getElementById('step-1');
    var step2 = document.getElementById('step-2');
    var btnNext = document.getElementById('btn-next');
    var btnBack = document.getElementById('btn-back');
    var nama = document.getElementById('nama');
    // Kolom pembungkus melebar di step 2 supaya 5 kartu kandidat muat mendatar.
    var col = document.querySelector('.voter-col');

    if (!step1 || !step2 || !btnNext) {
        return;
    }

    btnNext.addEventListener('click', function () {
        // Validasi sederhana step 1 di sisi klien.
        if (!nama.value.trim()) {
            nama.classList.add('is-invalid');
            nama.focus();
            return;
        }
        nama.classList.remove('is-invalid');

        step1.classList.add('d-none');
        step2.classList.remove('d-none');
        if (col) {
            col.classList.add('is-wide');
        }
        window.scrollTo(0, 0);
    });

    if (btnBack) {
        btnBack.addEventListener('click', function () {
            step2.classList.add('d-none');
            step1.classList.remove('d-none');
            if (col) {
                col.classList.remove('is-wide');
            }
            window.scrollTo(0, 0);
        });
    }
})();
