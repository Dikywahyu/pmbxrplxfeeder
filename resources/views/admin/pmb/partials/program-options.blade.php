<option value="">Pilih program studi</option>
@foreach($programs as $program)
    <option value="{{ $program->id }}">{{ $program->nama_prodi }} ({{ $program->jenjang }})</option>
@endforeach
