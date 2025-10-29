package perpustakaan;

import java.util.ArrayList;
import javax.swing.JFrame;
import javax.swing.table.DefaultTableModel;

public class FormPeminjaman extends javax.swing.JFrame {
    private PeminjamanController controller = new PeminjamanController();

    public FormPeminjaman() {
        initComponents();
    }

    public void tampilkan() {
        this.setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        this.pack();
        this.setLocationRelativeTo(null);
        this.setVisible(true);
    }

    public void tambahBukuDariPencarian(Buku buku) {
        try {
            int lama = Integer.parseInt(jTextFieldLama.getText());
            if (controller.tambahBuku(buku, lama)) {
                updateTable();
            }
        } catch (NumberFormatException e) {
            DialogUI dialog = new DialogUI("Masukkan lama peminjaman dalam angka hari");
            dialog.pack();
            dialog.setLocationRelativeTo(null);
            dialog.setVisible(true);
        }
    }

    private void updateTable() {
        ArrayList<PeminjamanController.BukuDipinjam> list = controller.getBukuDipinjam();
        Object[] kolom = {"Judul", "Lama Peminjaman", "Tanggal Pinjam", "Jatuh Tempo"};
        DefaultTableModel model = new DefaultTableModel(kolom, 0);
        for (PeminjamanController.BukuDipinjam bd : list) {
            Object[] baris = {bd.buku.judul, bd.lama + " hari", bd.tanggalPinjam, bd.getTanggalJatuhTempo()};
            model.addRow(baris);
        }
        jtBukuDipinjam.setModel(model);
    }

    private void jButtonKonfirmasiMouseClicked(java.awt.event.MouseEvent evt) {
        controller.konfirmasiPeminjaman();
    }

    @SuppressWarnings("unchecked")
    private void initComponents() {
        jLabelLama = new javax.swing.JLabel();
        jTextFieldLama = new javax.swing.JTextField();
        jScrollPane1 = new javax.swing.JScrollPane();
        jtBukuDipinjam = new javax.swing.JTable();
        jButtonKonfirmasi = new javax.swing.JButton();

        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);
        setTitle("Form Peminjaman Buku");

        jLabelLama.setText("Lama Peminjaman (hari):");

        jtBukuDipinjam.setModel(new javax.swing.table.DefaultTableModel(
            new Object [][] {},
            new String [] {"Judul", "Lama Peminjaman", "Tanggal Pinjam", "Jatuh Tempo"}
        ));
        jScrollPane1.setViewportView(jtBukuDipinjam);

        jButtonKonfirmasi.setText("Konfirmasi Peminjaman");
        jButtonKonfirmasi.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                jButtonKonfirmasiMouseClicked(evt);
            }
        });

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addComponent(jScrollPane1, javax.swing.GroupLayout.DEFAULT_SIZE, 376, Short.MAX_VALUE)
                    .addGroup(layout.createSequentialGroup()
                        .addComponent(jLabelLama)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addComponent(jTextFieldLama, javax.swing.GroupLayout.PREFERRED_SIZE, 100, javax.swing.GroupLayout.PREFERRED_SIZE)
                        .addGap(0, 0, Short.MAX_VALUE))
                    .addGroup(javax.swing.GroupLayout.Alignment.TRAILING, layout.createSequentialGroup()
                        .addGap(0, 0, Short.MAX_VALUE)
                        .addComponent(jButtonKonfirmasi)))
                .addContainerGap())
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabelLama)
                    .addComponent(jTextFieldLama, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(jScrollPane1, javax.swing.GroupLayout.PREFERRED_SIZE, 200, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(jButtonKonfirmasi)
                .addContainerGap(javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
        );

        pack();
    }

    private javax.swing.JButton jButtonKonfirmasi;
    private javax.swing.JLabel jLabelLama;
    private javax.swing.JScrollPane jScrollPane1;
    private javax.swing.JTextField jTextFieldLama;
    private javax.swing.JTable jtBukuDipinjam;
}