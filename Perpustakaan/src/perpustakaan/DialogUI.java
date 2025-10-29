package perpustakaan;

import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class DialogUI extends JFrame {
    public DialogUI(String message) {
        JPanel panel = new JPanel();
        panel.add(new JLabel(message));
        this.add(panel);
        this.setTitle("Pesan");
    }
}