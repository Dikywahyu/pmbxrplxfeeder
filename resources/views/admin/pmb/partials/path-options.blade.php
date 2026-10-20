<option value="">Pilih jalur</option>
@foreach($paths as $path)
    <option value="{{ $path->id }}">{{ $path->nama_jalur }}</option>
@endforeach
