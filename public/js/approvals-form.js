document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('detailModal');
    const rejectModal = document.getElementById('rejectModal');
    const rejectReason = document.getElementById('rejectReason');
    const cancelModal = document.getElementById('cancelModal');

    let currentRejectForm = null;
    let currentCancelForm = null;

    // =========================
    // DETAIL MODAL
    // =========================
    document.querySelectorAll('.btn-detail').forEach(btn => {

        btn.addEventListener('click', () => {

            try {

                const data = JSON.parse(btn.dataset.approval);

                document.getElementById('d-booking-code').textContent = data.booking_code || '-';
                document.getElementById('d-name').textContent = data.user?.name || '-';
                document.getElementById('d-email').textContent = data.user?.email || '-';
                document.getElementById('d-destination').textContent = data.destination || '-';
                document.getElementById('d-purpose').textContent = data.purpose || '-';

                if (data.created_at) {

                    const createdDate = new Date(data.created_at);

                    document.getElementById('d-date').textContent =
                        createdDate.toLocaleDateString('id-ID');

                }

                const startField = data.start_time || data.start_date;
                const endField = data.end_time || data.end_date;

                if (startField) {

                    const startDate = new Date(startField);

                    document.getElementById('d-start').textContent =
                        startDate.toLocaleString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                } else {
                    document.getElementById('d-start').textContent = '-';
                }

                if (endField) {

                    const endDate = new Date(endField);

                    document.getElementById('d-end').textContent =
                        endDate.toLocaleString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                } else {
                    document.getElementById('d-end').textContent = '-';
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');

            } catch (error) {

                console.error('Error parsing approval data:', error);
                alert('Terjadi kesalahan saat memuat data.');

            }

        });

    });


    // =========================
    // CLOSE DETAIL MODAL
    // =========================
    const closeDetailBtn = document.getElementById('closeDetailModal');

    if (closeDetailBtn) {
        closeDetailBtn.addEventListener('click', () => {

            modal.classList.add('hidden');
            modal.classList.remove('flex');

        });
    }


    // =========================
    // REJECT BUTTON
    // =========================
    document.querySelectorAll('.btn-reject-trigger').forEach(button => {

        button.addEventListener('click', () => {

            currentRejectForm = button.closest('form');

            rejectModal.classList.remove('hidden');
            rejectModal.classList.add('flex');

        });

    });


    // =========================
    // CANCEL REJECT
    // =========================
    const cancelRejectBtn = document.getElementById('cancelReject');

    if (cancelRejectBtn) {
        cancelRejectBtn.addEventListener('click', () => {

            rejectModal.classList.add('hidden');
            rejectModal.classList.remove('flex');

            rejectReason.value = '';

        });
    }


    // =========================
    // CONFIRM REJECT
    // =========================
    const confirmRejectBtn = document.getElementById('confirmReject');

    if (confirmRejectBtn) {
        confirmRejectBtn.addEventListener('click', () => {

            const reason = rejectReason.value.trim();

            if (!reason) {

                alert('Alasan penolakan wajib diisi.');
                return;

            }

            currentRejectForm.querySelector('.reject-reason-input').value = reason;

            currentRejectForm.submit();

        });
    }


    // =========================
    // CANCEL BOOKING MODAL
    // =========================
    document.querySelectorAll('.btn-cancel-booking').forEach(btn => {

        btn.addEventListener('click', () => {

            currentCancelForm = btn.closest('form');

            cancelModal.classList.remove('hidden');
            cancelModal.classList.add('flex');

        });

    });


    // =========================
    // CLOSE CANCEL MODAL
    // =========================
    const cancelCancelBtn = document.getElementById('cancelCancel');

    if (cancelCancelBtn) {
        cancelCancelBtn.addEventListener('click', () => {

            cancelModal.classList.add('hidden');
            cancelModal.classList.remove('flex');

        });
    }


    // =========================
    // CONFIRM CANCEL BOOKING
    // =========================
    const confirmCancelBtn = document.getElementById('confirmCancel');

    if (confirmCancelBtn) {
        confirmCancelBtn.addEventListener('click', () => {

            if (currentCancelForm) {
                currentCancelForm.submit();
            }

        });
    }

});