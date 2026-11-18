/**
 * Admin Portal JavaScript
 * Event Management System
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Table Search / Filter
    const searchInput = document.getElementById('tableSearch');
    const table = document.getElementById('registrationsTable');

    if (searchInput && table) {
        const tbody = table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr[data-id]');

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            rows.forEach((row) => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Handle "No matching results" row
            let noMatchRow = document.getElementById('noMatchRow');
            if (visibleCount === 0 && rows.length > 0) {
                if (!noMatchRow) {
                    noMatchRow = document.createElement('tr');
                    noMatchRow.id = 'noMatchRow';
                    noMatchRow.innerHTML = `
                        <td colspan="6" style="text-align: center; padding: 36px 20px; color: #64748b;">
                            <div style="font-size: 2rem; margin-bottom: 8px;">🔍</div>
                            <strong>No registrations matching "${escapeHtml(query)}"</strong>
                            <p style="font-size: 0.85rem; margin-top: 4px;">Try searching for a different name, email, or event.</p>
                        </td>
                    `;
                    tbody.appendChild(noMatchRow);
                }
            } else if (noMatchRow) {
                noMatchRow.remove();
            }
        });
    }

    // 2. View Modal Logic
    const viewModal = document.getElementById('viewModal');
    const closeViewModalBtn = document.getElementById('closeViewModal');
    const btnDismissView = document.getElementById('btnDismissView');
    const viewButtons = document.querySelectorAll('.btn-view-modal');

    if (viewModal) {
        viewButtons.forEach((btn) => {
            btn.addEventListener('click', (e) => {
                const row = e.target.closest('tr');
                if (!row) return;

                const regId = row.dataset.id;
                const name = row.dataset.name;
                const email = row.dataset.email;
                const event = row.dataset.event;
                const eventDate = row.dataset.eventDate;
                const location = row.dataset.eventLocation;
                const regDate = row.dataset.regDate;

                document.getElementById('modalRegId').textContent = '#' + regId;
                document.getElementById('modalUserName').textContent = name;
                document.getElementById('modalUserEmail').textContent = email;
                document.getElementById('modalEventTitle').textContent = event;
                document.getElementById('modalEventDate').textContent = eventDate;
                document.getElementById('modalEventLocation').textContent = location;
                document.getElementById('modalRegDate').textContent = regDate;
                document.getElementById('modalDirectLink').href = 'view.php?id=' + regId;

                openModal(viewModal);
            });
        });

        if (closeViewModalBtn) closeViewModalBtn.addEventListener('click', () => closeModal(viewModal));
        if (btnDismissView) btnDismissView.addEventListener('click', () => closeModal(viewModal));
    }

    // 3. Delete Modal Logic
    const deleteModal = document.getElementById('deleteModal');
    const closeDeleteModalBtn = document.getElementById('closeDeleteModal');
    const btnCancelDelete = document.getElementById('btnCancelDelete');
    const deleteButtons = document.querySelectorAll('.btn-delete-modal');

    if (deleteModal) {
        deleteButtons.forEach((btn) => {
            btn.addEventListener('click', (e) => {
                const btnEl = e.target.closest('.btn-delete-modal');
                const regId = btnEl.dataset.id;
                const name = btnEl.dataset.name;

                document.getElementById('deleteInputId').value = regId;
                document.getElementById('deleteTargetId').textContent = '#' + regId;
                document.getElementById('deleteTargetName').textContent = '"' + name + '"';

                openModal(deleteModal);
            });
        });

        if (closeDeleteModalBtn) closeDeleteModalBtn.addEventListener('click', () => closeModal(deleteModal));
        if (btnCancelDelete) btnCancelDelete.addEventListener('click', () => closeModal(deleteModal));
    }

    // Modal Helper functions
    function openModal(modalEl) {
        modalEl.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalEl) {
        modalEl.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close on click outside modal content
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-overlay')) {
            closeModal(e.target);
        }
    });

    // Close on Escape key press
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.modal-overlay.active');
            if (activeModal) closeModal(activeModal);
        }
    });

    function escapeHtml(str) {
        return str.replace(/[&<>'"]/g, (tag) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;'
        }[tag] || tag));
    }
});
