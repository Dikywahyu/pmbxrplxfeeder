@foreach($registrations as $registration)
    <tr>
        <td data-label="Pendaftar">
            <strong>{{ $registration->candidate?->nama_mahasiswa }}</strong><br>
            <small>{{ $registration->nomor_pendaftaran }}</small><br>
            <small class="text-muted">
                {{ $registration->candidate?->user?->email ?? $registration->candidate?->email ?? 'Email tidak tersedia' }}
                · {{ $registration->candidate?->handphone ?: 'Nomor HP belum diisi' }}
            </small>
        </td>
        <td data-label="Periode / Jalur / Prodi">
            {{ $registration->period?->nama_periode ?? 'Periode tidak tersedia' }}<br>
            <small>
                {{ $registration->path?->nama_jalur ?? 'Jalur tidak tersedia' }} ·
                {{ $registration->program1?->nama_prodi ?? 'Program studi tidak tersedia' }}
            </small>
        </td>
        <td data-label="Status seleksi">
            @can('pmb-review')
                <form method="POST" action="{{ route('admin.pmb.registrations.status', $registration->id) }}" data-registration-status-form>
                    @csrf
                    @method('PATCH')
                    <select class="form-select form-select-sm pmb-status-select" name="status" aria-label="Status seleksi" data-current-status="{{ $registration->status }}">
                        @foreach(['berkas_masuk' => 'Berkas masuk', 'review' => 'Dalam review', 'sudah_ujian' => 'Sudah ujian', 'lulus' => 'Lulus', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak', 'mundur' => 'Mundur'] as $value => $label)
                            <option value="{{ $value }}" @selected($registration->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            @else
                <span class="badge bg-primary-subtle text-primary">{{ str_replace('_', ' ', ucfirst($registration->status)) }}</span>
            @endcan
        </td>
        <td data-label="Berkas">
            @forelse($registration->documents as $document)
                @php
                    $documentStatusLabel = ['pending' => 'Menunggu', 'valid' => 'Disetujui', 'invalid' => 'Perbaikan'][$document->status_verifikasi] ?? ucfirst($document->status_verifikasi);
                    $documentName = $document->requirement?->nama_dokumen ?? 'Berkas';
                    $normalizedDocumentName = mb_strtolower($documentName);
                    $documentKind = 'default';

                    if (str_contains($normalizedDocumentName, 'ktp')) {
                        $documentKind = 'identity';
                    } elseif (str_contains($normalizedDocumentName, 'keluarga')) {
                        $documentKind = 'family';
                    } elseif (str_contains($normalizedDocumentName, 'sktm')) {
                        $documentKind = 'poverty';
                    } elseif (str_contains($normalizedDocumentName, 'ijazah')) {
                        $documentKind = 'diploma';
                    } elseif (str_contains($normalizedDocumentName, 'kip')) {
                        $documentKind = 'kip';
                    } elseif (str_contains($normalizedDocumentName, 'pip') || str_contains($normalizedDocumentName, 'pkh')) {
                        $documentKind = 'aid';
                    }
                @endphp
                <button type="button" class="pmb-document-chip" data-kind="{{ $documentKind }}" data-status="{{ $document->status_verifikasi }}" data-document-review data-document-name="{{ $documentName }}" data-document-status="{{ $documentStatusLabel }}" data-document-url="{{ route('pmb.documents.download', $document->id) }}" data-document-preview-url="{{ route('pmb.documents.preview', $document->id) }}" data-document-extension="{{ strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}" data-review-url="{{ route('admin.pmb.documents.verify', $document->id) }}" title="Klik untuk membuka dan meninjau berkas">
                    <i class="ti ti-file-text" aria-hidden="true"></i><span>{{ $document->requirement?->nama_dokumen ?? 'Berkas' }}</span><small>{{ $documentStatusLabel }}</small><i class="ti ti-chevron-right pmb-document-chevron" aria-hidden="true"></i>
                </button>
            @empty
                <small class="text-muted">Belum diunggah</small>
            @endforelse
        </td>
        <td data-label="Pembayaran">
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
        <td class="text-nowrap" data-label="Aksi">
            @can('pmb-registration-manage')
                <button type="button" class="btn btn-sm btn-light-primary text-primary" data-pmb-edit data-update-url="{{ route('admin.pmb.registrations.update', $registration->id) }}" data-payload="{{ json_encode(['record' => ['nama_mahasiswa' => $registration->candidate?->nama_mahasiswa, 'nik' => $registration->candidate?->nik, 'jenis_kelamin' => $registration->candidate?->jenis_kelamin, 'handphone' => $registration->candidate?->handphone, 'status' => $registration->status]]) }}"><i class="ti ti-edit"></i> Edit</button>
                <button type="button" class="btn btn-sm btn-light-danger text-danger" data-pmb-delete data-delete-url="{{ route('admin.pmb.registrations.destroy', $registration->id) }}"><i class="ti ti-trash"></i> Hapus</button>
            @else
                <span class="text-muted">—</span>
            @endcan
        </td>
    </tr>
@endforeach
