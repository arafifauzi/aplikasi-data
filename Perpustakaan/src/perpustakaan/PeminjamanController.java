package perpustakaan;

import java.time.LocalDate;
import java.util.ArrayList;

public class PeminjamanController {
    private ArrayList<BukuDipinjam> bukuDipinjam = new ArrayList<>();
    private static final int MAX_HARI = 3;
    private static final int MAX_BUKU = 10;

    public static class BukuDipinjam {
        public Buku buku;
        public int lama;
        public LocalDate tanggalPinjam;

        public BukuDipinjam(Buku buku, int lama) {
            this.buku = buku;
            this.lama = lama;
            this.tanggalPinjam = LocalDate.now();
        }

        public LocalDate getTanggalJatuhTempo() {
            return tanggalPinjam.plusDays(lama);
        }
    }

    public void showFormPeminjaman() {
        Perpustakaan.formPeminjaman = new FormPeminjaman();
        Perpustakaan.formPeminjaman.tampilkan();
    }

    public boolean tambahBuku(Buku buku, int lama) {
        if (lama > MAX_HARI) {
            DialogUI dialog = new DialogUI("Lama peminjaman maksimal 3 hari");
            dialog.pack();
            dialog.setLocationRelativeTo(null);
            dialog.setVisible(true);
            return false;
        }
        if (bukuDipinjam.size() >= MAX_BUKU) {
            DialogUI dialog = new DialogUI("Jumlah buku yang dipinjam melebihi batas maksimal 10 buku");
            dialog.pack();
            dialog.setLocationRelativeTo(null);
            dialog.setVisible(true);
            return false;
        }
        bukuDipinjam.add(new BukuDipinjam(buku, lama));
        return true;
    }

    public void konfirmasiPeminjaman() {
        DialogUI dialog = new DialogUI("Peminjaman Buku Anda telah dikonfirmasi");
        dialog.pack();
        dialog.setLocationRelativeTo(null);
        dialog.setVisible(true);
    }

    public ArrayList<BukuDipinjam> getBukuDipinjam() {
        return bukuDipinjam;
    }

    public ArrayList<BukuDipinjam> getBukuJatuhTempo() {
        ArrayList<BukuDipinjam> jatuhTempo = new ArrayList<>();
        LocalDate today = LocalDate.now();
        for (BukuDipinjam bd : bukuDipinjam) {
            if (bd.getTanggalJatuhTempo().isBefore(today) || bd.getTanggalJatuhTempo().isEqual(today)) {
                jatuhTempo.add(bd);
            }
        }
        return jatuhTempo;
    }

    public boolean adaBukuJatuhTempo() {
        return !getBukuJatuhTempo().isEmpty();
    }
}