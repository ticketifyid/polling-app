/**
 * Pembuka hasil untuk layar videotron.
 *
 * Tiga babak, dijalankan berurutan setelah gembok diklik:
 *   1. terkunci — angka diganti "??" supaya tidak bocor sebelum acara mulai
 *   2. mengocok — angka berputar acak, melambat, lalu mendarat di nilai asli
 *   3. terbuka  — sembilan kartu memudar, satu terfavorit membesar ke tengah,
 *                 sembilan sisanya muncul lagi sebagai satu baris kecil
 *
 * Semuanya berjalan di sisi klien dari data yang sudah ada di DOM: skrip ini
 * tidak pernah meminta ulang ke server, jadi angka yang mendarat pasti sama
 * dengan angka yang dirender Blade.
 */
(function () {
    'use strict';

    var board = document.getElementById('result-board');
    var lock = document.getElementById('result-lock');
    var grid = document.getElementById('result-grid');
    var finalStage = document.getElementById('result-final');
    var spotlight = document.getElementById('result-spotlight');
    var runners = document.getElementById('result-runners');

    if (!board || !lock || !grid || !finalStage || !spotlight || !runners) {
        return;
    }

    var cols = Array.prototype.slice.call(grid.querySelectorAll('.col'));
    if (!cols.length) {
        return;
    }

    var reduceMotion = window.matchMedia
        && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Kandidat diurutkan ulang di sini, bukan mengandalkan urutan render, supaya
    // pemenangnya tetap benar kalau suatu saat controller mengubah urutannya.
    var entries = cols.map(function (col) {
        var countEl = col.querySelector('.result-count');
        return {
            col: col,
            countEl: countEl,
            count: parseInt(countEl.getAttribute('data-count'), 10) || 0
        };
    }).sort(function (a, b) {
        return b.count - a.count;
    });

    var winner = entries[0];
    var losers = entries.slice(1);

    // Batas atas angka acak: sedikit di atas perolehan tertinggi supaya kocokan
    // terasa masuk akal tanpa membocorkan nilai akhirnya.
    var ceiling = Math.max(9, Math.ceil(winner.count * 1.4));

    // Semua angka acak dipadankan panjangnya dengan angka terpanjang. Selain
    // lebih rapi dibaca, ini menghindari reflow: teks yang berubah lebar tiap
    // pergantian angka memaksa label dihitung ulang puluhan kali per detik.
    var digits = String(ceiling).length;

    function pad(n) {
        var s = String(n);
        while (s.length < digits) {
            s = '0' + s;
        }
        return s;
    }

    // --- Babak 1: kunci papan -------------------------------------------
    board.classList.add('is-locked');
    lock.hidden = false;
    entries.forEach(function (entry) {
        entry.countEl.textContent = '??';
    });

    lock.addEventListener('click', function () {
        lock.classList.add('is-opening');
        lock.disabled = true;
        whenPhotosReady(function () {
            window.setTimeout(startRolling, reduceMotion ? 0 : 500);
        });
    }, { once: true });

    /**
     * Menunggu semua foto selesai dimuat sebelum babak animasi dimulai.
     *
     * Di localhost foto sudah ada di disk, jadi tidak pernah terasa. Di hosting,
     * foto bisa saja masih diunduh atau di-decode saat animasi sudah berjalan —
     * dan decode gambar terjadi di thread utama, jadi hasilnya patah-patah tepat
     * di detik pertama. Ditunggu maksimal 2 detik supaya satu foto yang gagal
     * dimuat tidak menyandera seluruh acara.
     */
    function whenPhotosReady(done) {
        var images = Array.prototype.slice.call(grid.querySelectorAll('img'));
        var pending = images.length;
        var finished = false;

        function tick() {
            if (finished) {
                return;
            }
            pending -= 1;
            if (pending <= 0) {
                finished = true;
                done();
            }
        }

        images.forEach(function (img) {
            if (img.complete && img.naturalWidth) {
                tick();
            } else {
                img.addEventListener('load', tick, { once: true });
                img.addEventListener('error', tick, { once: true });
            }
        });

        if (!images.length) {
            done();
            return;
        }

        window.setTimeout(function () {
            if (!finished) {
                finished = true;
                done();
            }
        }, 2000);
    }

    // --- Babak 2: kocok angka -------------------------------------------
    function startRolling() {
        lock.hidden = true;
        board.classList.remove('is-locked');

        if (reduceMotion) {
            landAll();
            window.setTimeout(reveal, 600);
            return;
        }

        board.classList.add('is-rolling');

        var duration = 3200;
        var started = null;

        function frame(now) {
            if (started === null) {
                started = now;
            }
            var progress = Math.min((now - started) / duration, 1);

            // Jeda antar pergantian angka melebar seiring waktu: cepat di awal,
            // tersendat-sendat di akhir — itu yang bikin kocokan terasa berhenti
            // sendiri, bukan dipotong.
            entries.forEach(function (entry) {
                entry.countEl.textContent = pad(Math.floor(Math.random() * ceiling));
            });

            if (progress < 1) {
                // Mulai dari 65ms (±15 pergantian per detik). Lebih cepat dari
                // itu tidak terbaca mata, tapi tetap dibayar mesin yang lemah.
                var delay = 65 + Math.pow(progress, 3) * 260;
                window.setTimeout(function () {
                    window.requestAnimationFrame(frame);
                }, delay);
            } else {
                board.classList.remove('is-rolling');
                landAll();
                window.setTimeout(reveal, 1400);
            }
        }

        window.requestAnimationFrame(frame);
    }

    function landAll() {
        entries.forEach(function (entry) {
            entry.countEl.textContent = entry.count;
            entry.countEl.classList.add('is-landed');
        });
    }

    // --- Babak 3: buka pemenang -----------------------------------------
    function reveal() {
        // Seluruh grid dipadamkan sekaligus sebagai satu elemen, bukan sepuluh
        // kartu yang dianimasikan sendiri-sendiri. Kartu pemenang tetap "pindah"
        // ke panggung karena yang muncul di sana adalah salinannya.
        grid.classList.add('is-out');

        window.setTimeout(function () {
            grid.style.display = 'none';

            spotlight.appendChild(winner.col.querySelector('.result-card').cloneNode(true));
            losers.forEach(function (entry) {
                runners.appendChild(entry.col.querySelector('.result-card').cloneNode(true));
            });

            finalStage.classList.add('is-shown');
        }, reduceMotion ? 0 : 520);
    }
})();
