@foreach($registrations as $registration)
    <tr>
        <td>
            <strong>{{ $registration->candidate?->nama_mahasiswa }}</strong><br>
            <small>{{ $registration->nomor_pendaftaran }}</small><br>
            <small class="text-muted">
                {{ $registration->candidate?->user?->email ?? $registration->candidate?->email ?? 'Email tidak tersedia' }}
                · {{ $registration->candidate?->handphone ?: 'Nomor HP belum diisi' }}
            </small>
        </td>
        <td>
            {{ $registration->period?->nama_periode ?? 'Periode tidak tersedia' }}<br>
            <small>
                {{ $registration->path?->nama_jalur ?? 'Jalur tidak tersedia' }} ·
                {{ $registration->program1?->nama_prodi ?? 'Program studi tidak tersedia' }}
            </small>
        </td>
        <td>
            <span class="badge bg-primary-subtle text-primary mb-2">{{ str_replace('_', ' ', ucfirst($registration->status)) }}</span>
            @can('pmb-review')
                <form method="POST" action="{{ route('admin.pmb.registrations.status', $registration->id) }}" class="d-flex gap-1" data-ajax-form>
                    @csrf
                    @method('PATCH')
                    <select class="form-select form-select-sm" name="status" aria-label="Status seleksi">
                        @foreach(['berkas_masuk' => 'Berkas masuk', 'review' => 'Dalam review', 'lulus' => 'Lulus', 'ditolak' => 'Ditolak'] as $value => $label)
                            <option value="{{ $value }}" @selected($registration->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-outline-primary btn-sm" type="submit">Simpan</button>
                </form>
            @endcan
        </td>
        <td>
            @forelse($registration->payments as $payment)
                <div class="mb-3">
                    <strong>{{ ucfirst(str_replace('_', ' ', $payment->jenis_tagihan)) }}: Rp {{ number_format($payment->nominal_tagihan, 0, ',', '.') }}</strong><br>
                    <span class="badge bg-secondary-subtle text-secondary">{{ str_replace('_', ' ', ucfirst($payment->status)) }}</span>
                    @if($payment->file_bukti_bayar)
                        <div class="mt-2"><a href="{{ route('pmb.payments.download', $payment->id) }}">Unduh bukti bayar</a></div>
                    @endif
                    @if($payment->status === 'menunggu_verifikasi')
                        @can('pmb-payment-review')
                            <div class="d-flex gap-1 mt-2">
                                @foreach(['lunas' => 'Setujui', 'ditolak' => 'Tolak'] as $status => $label)
                                    <form method="POST" action="{{ route('admin.pmb.payments.verify', $payment->id) }}" data-ajax-form>
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $status }}">
                                        <button class="btn {{ $status === 'lunas' ? 'btn-success' : 'btn-outline-danger' }} btn-sm" type="submit">{{ $label }}</button>
                                    </form>
                                @endforeach
                            </div>
                        @endcan
                    @endif
                </div>
            @empty
                <span class="text-muted">Belum ada tagihan</span>
            @endforelse
        </td>
        <td class="text-nowrap">
            @can('pmb-registration-manage')
                <button type="button" class="btn btn-sm btn-light-primary text-primary" data-pmb-edit data-update-url="{{ route('admin.pmb.registrations.update', $registration->id) }}" data-payload="{{ json_encode(['record' => ['nama_mahasiswa' => $registration->candidate?->nama_mahasiswa, 'nik' => $registration->candidate?->nik, 'jenis_kelamin' => $registration->candidate?->jenis_kelamin, 'handphone' => $registration->candidate?->handphone, 'status' => $registration->status]]) }}"><i class="ti ti-edit"></i> Edit</button>
                <button type="button" class="btn btn-sm btn-light-danger text-danger" data-pmb-delete data-delete-url="{{ route('admin.pmb.registrations.destroy', $registration->id) }}"><i class="ti ti-trash"></i> Hapus</button>
            @else
                <span class="text-muted">—</span>
            @endcan
        </td>
    </tr>
@endforeach
