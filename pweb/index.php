<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Data Pengguna</h1>

        <div class="input-area">
            <h2>Tambah Pengguna</h2>
            <form id="formTambah">
                <input type="text" id="nama_tambah" placeholder="Nama" required>
                <input type="email" id="email_tambah" placeholder="Email" required>
                <button type="submit" class="button primary">Tambah</button>
            </form>
        </div>

        <div class="data-area">
            <h2>Daftar Pengguna</h2>
            <table id="tabelData">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="dataBody">
                </tbody>
            </table>
        </div>

        <div id="modalUpdate" class="modal">
            <div class="modal-content">
                <span class="close-button">&times;</span>
                <h2>Edit Pengguna</h2>
                <form id="formUpdate">
                    <input type="hidden" id="id_update">
                    <input type="text" id="nama_update" placeholder="Nama" required>
                    <input type="email" id="email_update" placeholder="Email" required>
                    <div class="modal-actions">
                        <button type="submit" class="button primary">Simpan</button>
                        <button type="button" class="button secondary close-button">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let dataPengguna = JSON.parse(localStorage.getItem('dataPengguna')) || [];
        let nextId = parseInt(localStorage.getItem('nextId')) || 1;

        function saveData() {
            localStorage.setItem('dataPengguna', JSON.stringify(dataPengguna));
            localStorage.setItem('nextId', nextId);
        }

        function renderTable() {
            const dataBody = document.getElementById('dataBody');
            dataBody.innerHTML = '';
            dataPengguna.forEach(user => {
                const row = dataBody.insertRow();
                row.dataset.id = user.id;
                row.insertCell().textContent = user.id;
                row.insertCell().textContent = user.nama;
                row.insertCell().textContent = user.email;
                const actionsCell = row.insertCell();
                actionsCell.innerHTML = `
                    <button class="button update-button" data-id="${user.id}" data-nama="${user.nama}" data-email="${user.email}">Edit</button>
                    <button class="button delete-button" data-id="${user.id}">Hapus</button>
                `;
            });
        }

        document.getElementById('formTambah').addEventListener('submit', function(e) {
            e.preventDefault();
            const nama = this.nama_tambah.value.trim();
            const email = this.email_tambah.value.trim();
            if (nama && email) {
                const newUser = { id: nextId++, nama, email };
                dataPengguna.push(newUser);
                renderTable();
                saveData();
                this.reset();
            }
        });

        const modalUpdate = document.getElementById('modalUpdate');
        const closeButtons = document.querySelectorAll('.close-button');
        const formUpdate = document.getElementById('formUpdate');

        document.getElementById('tabelData').addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-button')) {
                const idToDelete = parseInt(e.target.dataset.id);
                dataPengguna = dataPengguna.filter(user => user.id !== idToDelete);
                renderTable();
                saveData();
            } else if (e.target.classList.contains('update-button')) {
                const user = dataPengguna.find(u => u.id === parseInt(e.target.dataset.id));
                if (user) {
                    formUpdate.id_update.value = user.id;
                    formUpdate.nama_update.value = user.nama;
                    formUpdate.email_update.value = user.email;
                    modalUpdate.style.display = 'block';
                }
            }
        });

        closeButtons.forEach(button => {
            button.addEventListener('click', () => modalUpdate.style.display = 'none');
        });

        window.addEventListener('click', (e) => {
            if (e.target === modalUpdate) {
                modalUpdate.style.display = 'none';
            }
        });

        formUpdate.addEventListener('submit', function(e) {
            e.preventDefault();
            const idUpdate = parseInt(this.id_update.value);
            const namaUpdate = this.nama_update.value.trim();
            const emailUpdate = this.email_update.value.trim();
            dataPengguna = dataPengguna.map(user =>
                user.id === idUpdate ? { ...user, nama: namaUpdate, email: emailUpdate } : user
            );
            renderTable();
            saveData();
            modalUpdate.style.display = 'none';
        });

        renderTable(); // Initial render
    </script>
</body>
</html>