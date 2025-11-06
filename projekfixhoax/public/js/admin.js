document.addEventListener('DOMContentLoaded', () => {
    // Toggle Sidebar
    const toggleButton = document.querySelector('.toggle-sidebar');
    const sidebar = document.querySelector('.sidebar');
    if (toggleButton && sidebar) {
        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !toggleButton.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    }

    // Tab Switching
    const tabs = document.querySelectorAll('.tab-button');
    const newsSection = document.querySelector('.news-section');
    const addFormSection = document.querySelector('.add-form-section');
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'news';
    tabs.forEach(tab => {
        if (tab.dataset.tab === activeTab) tab.classList.add('active');
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            newsSection.style.display = this.dataset.tab === 'news' ? 'block' : 'none';
            addFormSection.style.display = this.dataset.tab === 'add' ? 'block' : 'none';
            window.history.pushState({}, '', `?action=admin/news&tab=${this.dataset.tab}`);
        });
    });
    if (document.querySelector(`[data-tab="${activeTab}"]`)) {
        document.querySelector(`[data-tab="${activeTab}"]`).click();
    }

    // Handle Table Row Click for Detail
    document.querySelectorAll('.news-table tr.clickable').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('.action-column')) return;
            fetchNewsData(this.dataset.id, 'detail');
        });
    });

    // Handle Edit Button Click
    document.querySelectorAll('.edit-news-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            fetchNewsData(this.dataset.id, 'edit');
        });
    });

    // Modal Edit
    const modalEdit = document.querySelector('#edit-modal');
    const modalEditContent = modalEdit?.querySelector('.modal-content');
    const closeEditBtn = modalEdit?.querySelector('.close-btn');
    const cancelEditBtn = modalEdit?.querySelector('.cancel-btn');

    // Modal Detail
    const modalDetail = document.querySelector('#detail-modal');
    const modalDetailContent = modalDetail?.querySelector('.modal-content');
    const closeDetailBtn = modalDetail?.querySelector('.close-btn');
    const closeDetailActionBtn = modalDetail?.querySelector('.close-detail-btn');

    // Close Modal Edit
    if (closeEditBtn) {
        closeEditBtn.addEventListener('click', () => modalEdit.style.display = 'none');
    }
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', () => modalEdit.style.display = 'none');
    }

    // Close Modal Detail
    if (closeDetailBtn) {
        closeDetailBtn.addEventListener('click', () => modalDetail.style.display = 'none');
    }
    if (closeDetailActionBtn) {
        closeDetailActionBtn.addEventListener('click', () => modalDetail.style.display = 'none');
    }

    // Handle Edit Button in Detail Modal
    const editBtn = modalDetailContent?.querySelector('.edit-btn');
    if (editBtn) {
        editBtn.addEventListener('click', function() {
            const id = this.dataset.id;
            modalDetail.style.display = 'none';
            fetchNewsData(id, 'edit');
        });
    }

    function fetchNewsData(id, mode) {
        fetch(`/projekfixhoax/fetch_news.php?id=${id}`)
            .then(response => {
                if (!response.ok) throw new Error(`Network response was not ok: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                if (mode === 'edit') {
                    modalEditContent.querySelector('input[name="id"]').value = data.id;
                    modalEditContent.querySelector('input[name="judul"]').value = data.judul;
                    modalEditContent.querySelector('textarea[name="isi"]').value = data.isi;
                    modalEditContent.querySelector('select[name="keterangan"]').value = data.keterangan;
                    modalEditContent.querySelector('textarea[name="klarifikasi"]').value = data.klarifikasi;
                    modalEditContent.querySelector('input[name="penulis"]').value = data.penulis;
                    modalEditContent.querySelector('input[name="tanggal"]').value = data.tanggal;
                    modalEditContent.querySelector('select[name="tema"]').value = data.tema;
                    modalEditContent.querySelector('input[name="existing_gambar"]').value = data.gambar || '';
                    const artikelSelect = modalEditContent.querySelector('select[name="artikel[]"]');
                    Array.from(artikelSelect.options).forEach(option => {
                        option.selected = data.artikel.includes(parseInt(option.value));
                    });
                    const imagePreview = modalEditContent.querySelector('.image-preview');
                    const noImageEdit = modalEditContent.querySelector('.no-image');
                    if (data.gambar && data.gambar_exists) {
                        imagePreview.src = `/projekfixhoax/public/Uploads/${data.gambar}`;
                        imagePreview.style.display = 'block';
                        noImageEdit.style.display = 'none';
                    } else {
                        imagePreview.style.display = 'none';
                        noImageEdit.style.display = 'block';
                    }
                    modalEdit.style.display = 'block';
                } else {
                    modalDetailContent.querySelector('.detail-id').textContent = data.id;
                    modalDetailContent.querySelector('.detail-judul').textContent = data.judul;
                    modalDetailContent.querySelector('.detail-isi').textContent = data.isi;
                    modalDetailContent.querySelector('.detail-keterangan').textContent = data.keterangan;
                    modalDetailContent.querySelector('.detail-klarifikasi').textContent = data.klarifikasi;
                    modalDetailContent.querySelector('.detail-penulis').textContent = data.penulis;
                    modalDetailContent.querySelector('.detail-tanggal').textContent = data.tanggal;
                    modalDetailContent.querySelector('.detail-tema').textContent = data.tema;
                    const artikelList = modalDetailContent.querySelector('.artikel-list');
                    artikelList.innerHTML = '';
                    if (data.artikel.length > 0 && window.artikelTitles) {
                        data.artikel.forEach(artikelId => {
                            const li = document.createElement('li');
                            li.textContent = window.artikelTitles[artikelId] || 'Unknown Artikel';
                            artikelList.appendChild(li);
                        });
                    } else {
                        const li = document.createElement('li');
                        li.textContent = 'Tidak ada artikel terkait';
                        artikelList.appendChild(li);
                    }
                    const imageDetail = modalDetailContent.querySelector('.detail-image');
                    const noImageDetail = modalDetailContent.querySelector('.no-image');
                    if (data.gambar && data.gambar_exists) {
                        imageDetail.src = `/projekfixhoax/public/Uploads/${data.gambar}`;
                        imageDetail.style.display = 'block';
                        noImageDetail.style.display = 'none';
                    } else {
                        imageDetail.style.display = 'none';
                        noImageDetail.style.display = 'block';
                    }
                    modalDetailContent.querySelector('.edit-btn').dataset.id = data.id;
                    modalDetail.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Gagal memuat data berita: ' + error.message);
            });
    }
});