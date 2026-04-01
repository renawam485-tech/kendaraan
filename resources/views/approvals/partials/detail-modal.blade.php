<div class="modal fade" id="detailBooking{{ $booking->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Detail Peminjaman Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <dl class="row approval-detail">

                    <dt class="col-sm-4">Pemohon</dt>
                    <dd class="col-sm-8">
                        {{ $booking->user->name }}<br>
                        <small>{{ $booking->user->email }}</small>
                    </dd>

                    <dt class="col-sm-4">Tujuan</dt>
                    <dd class="col-sm-8">{{ $booking->destination }}</dd>

                    <dt class="col-sm-4">Keperluan</dt>
                    <dd class="col-sm-8">{{ $booking->purpose }}</dd>

                    <dt class="col-sm-4">Waktu Mulai</dt>
                    <dd class="col-sm-8">
                        {{ $booking->start_time->format('d M Y H:i') }}
                    </dd>

                    <dt class="col-sm-4">Waktu Selesai</dt>
                    <dd class="col-sm-8">
                        {{ $booking->end_time->format('d M Y H:i') }}
                    </dd>

                    <dt class="col-sm-4">Durasi</dt>
                    <dd class="col-sm-8 fw-semibold">
                        {{ $booking->start_time->diffForHumans($booking->end_time, true) }}
                    </dd>

                    <dt class="col-sm-4">Jenis Armada</dt>
                    <dd class="col-sm-8">
                        {{ $booking->vehicle_type ?? 'Unit Kampus / Diatur Admin' }}
                    </dd>

                    <dt class="col-sm-4">Jumlah Penumpang</dt>
                    <dd class="col-sm-8">{{ $booking->passenger_count }}</dd>

                    <dt class="col-sm-4">Pengemudi</dt>
                    <dd class="col-sm-8">
                        {{ $booking->with_driver ? 'Ya' : 'Tidak' }}
                    </dd>

                </dl>

                <div class="mb-2">
                    <label class="form-label">Catatan (Opsional)</label>
                    <textarea
                        class="form-control approval-comment"
                        rows="2"
                        placeholder="Catatan persetujuan / penolakan"></textarea>
                </div>
            </div>

            <div class="modal-footer">

                {{-- FORM TOLAK --}}
                <form method="POST"
                      action="{{ route('approvals.decide', $booking) }}"
                      class="me-auto approval-form">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <input type="hidden" name="comment">
                    <button class="btn btn-outline-danger">
                        Tolak
                    </button>
                </form>

                {{-- FORM SETUJUI --}}
                <form method="POST"
                      action="{{ route('approvals.decide', $booking) }}"
                      class="approval-form">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="comment">
                    <button class="btn btn-success">
                        Setujui
                    </button>
                </form>

            </div>

        </div>
    </div>
</div>
