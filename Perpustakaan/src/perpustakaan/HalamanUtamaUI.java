package perpustakaan;

public class HalamanUtamaUI extends javax.swing.JFrame {

    public HalamanUtamaUI() {
        initComponents();
        if (Perpustakaan.controllerPeminjaman != null && Perpustakaan.controllerPeminjaman.adaBukuJatuhTempo()) {
            DialogUI dialog = new DialogUI("Ada buku yang jatuh tempo! Periksa menu Jatuh Tempo.");
            dialog.pack();
            dialog.setLocationRelativeTo(null);
            dialog.setVisible(true);
        }
    }

    @SuppressWarnings("unchecked")
    private void initComponents() {
        jMenuBar1 = new javax.swing.JMenuBar();
        menuPencarian = new javax.swing.JMenu();
        menuPeminjaman = new javax.swing.JMenu();
        menuJatuhTempo = new javax.swing.JMenu();
        menuLihatPeminjaman = new javax.swing.JMenu();

        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);

        menuPencarian.setText("Pencarian");
        menuPencarian.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                menuPencarianMouseClicked(evt);
            }
        });
        jMenuBar1.add(menuPencarian);

        menuPeminjaman.setText("Peminjaman");
        menuPeminjaman.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                menuPeminjamanMouseClicked(evt);
            }
        });
        jMenuBar1.add(menuPeminjaman);

        menuJatuhTempo.setText("Jatuh Tempo");
        menuJatuhTempo.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                menuJatuhTempoMouseClicked(evt);
            }
        });
        jMenuBar1.add(menuJatuhTempo);

        menuLihatPeminjaman.setText("Lihat Peminjaman");
        menuLihatPeminjaman.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                menuLihatPeminjamanMouseClicked(evt);
            }
        });
        jMenuBar1.add(menuLihatPeminjaman);

        setJMenuBar(jMenuBar1);

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGap(0, 257, Short.MAX_VALUE)
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGap(0, 135, Short.MAX_VALUE)
        );

        pack();
    }

    private void menuPencarianMouseClicked(java.awt.event.MouseEvent evt) {
        Perpustakaan.controllerPencarian = new PencarianController();
        Perpustakaan.controllerPencarian.showFormPencarian();
    }

    private void menuPeminjamanMouseClicked(java.awt.event.MouseEvent evt) {
        Perpustakaan.controllerPeminjaman = new PeminjamanController();
        Perpustakaan.controllerPeminjaman.showFormPeminjaman();
    }

    private void menuJatuhTempoMouseClicked(java.awt.event.MouseEvent evt) {
        FormJatuhTempo form = new FormJatuhTempo();
        form.tampilkan();
    }

    private void menuLihatPeminjamanMouseClicked(java.awt.event.MouseEvent evt) {
        FormLihatPeminjaman form = new FormLihatPeminjaman();
        form.tampilkan();
    }

    private javax.swing.JMenuBar jMenuBar1;
    private javax.swing.JMenu menuJatuhTempo;
    private javax.swing.JMenu menuLihatPeminjaman;
    private javax.swing.JMenu menuPeminjaman;
    private javax.swing.JMenu menuPencarian;
}