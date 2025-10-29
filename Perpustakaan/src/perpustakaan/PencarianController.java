package perpustakaan;

import java.util.ArrayList;

public class PencarianController {

    public void showFormPencarian() {
        Perpustakaan.formPencarian = new FormPencarian();
        Perpustakaan.formPencarian.tampilkan();
    }

    public ArrayList<Buku> cariBuku(String judul) {
        ArrayList<Buku> hasil = new ArrayList<>();
        try {
            BukuProvider provider = new BukuProvider();
            ArrayList<Buku> semuaBuku = provider.selectBuku();
            for (Buku b : semuaBuku) {
                if (b.judul.toLowerCase().contains(judul.toLowerCase())) {
                    hasil.add(b);
                }
            }
            if (hasil.isEmpty()) {
                DialogUI dialog = new DialogUI("Buku tidak terdaftar");
                dialog.pack();
                dialog.setLocationRelativeTo(null);
                dialog.setVisible(true);
            }
        } catch (Exception ex) {
            DialogUI dialog = new DialogUI("Connection Error");
            dialog.pack();
            dialog.setLocationRelativeTo(null);
            dialog.setVisible(true);
        }
        return hasil;
    }
}