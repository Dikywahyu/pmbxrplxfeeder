@forelse($fees as $fee)
    <tr>
        <td>{{ $fee->period?->nama_periode ?? 'Periode tidak tersedia' }}</td>
        <td>{{ $fee->path?->nama_jalur ?? 'Jalur tidak tersedia' }}</td>
        <td>{{ $fee->program?->nama_prodi ?? 'Prodi tidak tersedia' }}</td>
        <td>Rp {{ number_format($fee->nominal_pendaftaran, 0, ',', '.') }}</td>
        <td>Rp {{ number_format($fee->nominal_spp, 0, ',', '.') }}</td>
    </tr>
@empty
    <tr><td colspan="5" class="text-center text-muted">Belum ada pengaturan biaya.</td></tr>
@endforelse
