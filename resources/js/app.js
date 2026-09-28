//

import Alpine from 'alpinejs';

// Input rupiah dengan pemisah ribuan otomatis (mis. 105000 -> 105.000).
// Dipakai lewat atribut x-data="rupiahInput(nilaiAwal)" pada form harga.
Alpine.data('rupiahInput', (awal = 0) => ({
    nilai: Number(awal) || 0,
    get tampil() {
        return this.nilai ? new Intl.NumberFormat('id-ID').format(this.nilai) : '';
    },
    set tampil(teks) {
        const digit = String(teks).replace(/\D/g, '');
        this.nilai = digit ? parseInt(digit, 10) : 0;
    },
}));

// Ringkasan hitungan diskon pada form riwayat order: total sebelum diskon,
// nominal potongannya (rupiah), dan total setelah dipotong. Dipakai lewat
// x-data="hitungDiskon(harga, qty, diskonPersen)" pada blok Harga & Diskon.
Alpine.data('hitungDiskon', (harga = 0, qty = 0, diskon = 0) => ({
    harga: Number(harga) || 0,
    qty: Number(qty) || 0,
    diskon: Number(diskon) || 0,

    init() {
        // Input qty ada di luar blok ini, jadi nilainya diambil dari DOM.
        const inputQty = document.getElementById('qty');

        if (inputQty) {
            this.qty = parseInt(String(inputQty.value).replace(/\D/g, ''), 10) || 0;
            inputQty.addEventListener('input', (e) => {
                this.qty = parseInt(String(e.target.value).replace(/\D/g, ''), 10) || 0;
            });
        }

        this.$el.addEventListener('harga-diubah', (e) => {
            this.harga = Number(e.detail.nilai) || 0;
        });
    },

    get totalKotor() {
        return this.qty * this.harga;
    },

    // Dihitung dari angka mentah supaya nominalnya pas dengan selisih
    // total kotor - total akhir (sama seperti accessor di model).
    get nominalDiskon() {
        return this.totalKotor * (this.diskon / 100);
    },

    get totalAkhir() {
        return this.totalKotor - this.nominalDiskon;
    },

    rupiah(angka) {
        // Tidak dibulatkan: sen aslinya (,50 / ,60) tetap ikut tampil,
        // dan selalu dua angka di belakang koma.
        const nilai = Number(angka) || 0;

        return 'Rp ' + new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(nilai);
    },
}));

window.Alpine = Alpine;

Alpine.start();
