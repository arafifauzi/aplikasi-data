package perpustakaan;

import java.util.ArrayList;
import javax.swing.JFrame;
import javax.swing.table.DefaultTableModel;

public class FormPencarian extends javax.swing.JFrame {
    private PencarianController controller = new PencarianController();

    public FormPencarian() {
        initComponents();
    }

    public void tampilkan() {
        this.setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        this.pack();
        this.setLocationRelativeTo(null);
        this.setVisible(true);
    }

    public void display(ArrayList<Buku> hasil) {
        Object[] kolom = {"Judul"};
        DefaultTableModel model = new DefaultTableModel(kolom, 0);
        for (Buku b : hasil) {
            Object[] baris = {b.judul};
            model.addRow(baris);
        }
        jtHasil.setModel(model);
    }

    private void jButtonCariMouseClicked(java.awt.event.MouseEvent evt) {
        String judul = jTextFieldJudul.getText();
        if (judul == null || judul.trim().isEmpty()) {
            DialogUI dialog = new DialogUI("Masukkan judul buku");
            dialog.pack();
            dialog.setLocationRelativeTo(null);
            dialog.setVisible(true);
            return;
        }
        ArrayList<Buku> hasil = controller.cariBuku(judul);
        display(hasil);
    }

    @SuppressWarnings("unchecked")
    private void initComponents() {
        jLabelJudul = new javax.swing.JLabel();
        jTextFieldJudul = new javax.swing.JTextField();
        jButtonCari = new javax.swing.JButton();
        jScrollPane1 = new javax.swing.JScrollPane();
        jtHasil = new javax.swing.JTable();

        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);
        setTitle("Form Pencarian Buku");

        jLabelJudul.setText("Judul Buku:");

        jButtonCari.setText("Cari");
        jButtonCari.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                jButtonCariMouseClicked(evt);
            }
        });

        jtHasil.setModel(new javax.swing.table.DefaultTableModel(
            new Object [][] {},
            new String [] {"Judul"}
        ));
        jScrollPane1.setViewportView(jtHasil);

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
                    .addComponent(jScrollPane1, javax.swing.GroupLayout.DEFAULT_SIZE, 376, Short.MAX_VALUE)
                    .addGroup(layout.createSequentialGroup()
                        .addComponent(jLabelJudul)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addComponent(jTextFieldJudul, javax.swing.GroupLayout.PREFERRED_SIZE, 200, javax.swing.GroupLayout.PREFERRED_SIZE)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                        .addComponent(jButtonCari)
                        .addGap(0, 0, Short.MAX_VALUE)))
                .addContainerGap())
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addContainerGap()
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(jLabelJudul)
                    .addComponent(jTextFieldJudul, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(jButtonCari))
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED)
                .addComponent(jScrollPane1, javax.swing.GroupLayout.PREFERRED_SIZE, 200, javax.swing.GroupLayout.PREFERRED_SIZE)
                .addContainerGap(javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE))
        );

        pack();
    }

    private javax.swing.JButton jButtonCari;
    private javax.swing.JLabel jLabelJudul;
    private javax.swing.JScrollPane jScrollPane1;
    private javax.swing.JTextField jTextFieldJudul;
    private javax.swing.JTable jtHasil;
}