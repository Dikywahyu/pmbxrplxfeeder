<option value="">Pilih periode</option>
@foreach($periods as $period)
    <option value="{{ $period->id }}">{{ $period->nama_periode }}</option>
@endforeach
