package perpustakaan;

public class Perpustakaan {
    public static PencarianController controllerPencarian;
    public static PeminjamanController controllerPeminjaman;
    public static FormPencarian formPencarian;
    public static FormPeminjaman formPeminjaman;
    public static FormJatuhTempo formJatuhTempo;

    public static void main(String[] args) {
        HalamanUtamaUI halamanUtama = new HalamanUtamaUI();
        halamanUtama.setVisible(true);
    }
}