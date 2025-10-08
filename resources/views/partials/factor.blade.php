<div>
    <label class="font-semibold block mb-1">{{ $nombre }}</label>
    <select name="{{ $clave }}" class="w-full border rounded p-1">
        @foreach($niveles as $nivel)
            <option value="{{ $nivel }}" {{ $nivel=='Nominal' ? 'selected' : '' }}>
                {{ $nivel }}
            </option>
        @endforeach
    </select>
</div>
