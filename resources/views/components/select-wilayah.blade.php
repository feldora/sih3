<div>
  <label class="label" for="kabupaten">Kabupaten</label>    
  <select name="kabupaten_id" id="kabupaten" class="form-select input input-bordered w-full mb-2">
      <option value="">-- Pilih Kabupaten --</option>
      @foreach($kabupatens as $kab)
          <option value="{{ $kab->id }}" @selected(old('kabupaten_id', $kabupatenId)==$kab->id)>
              {{ $kab->nama }}
          </option>
      @endforeach
  </select>
</div>
<div>
  <label class="label" for="kecamatan">Kecamatan</label>    
  <select name="kecamatan_id" id="kecamatan" class="form-select input input-bordered w-full mb-2">
      <option value="">-- Pilih Kecamatan --</option>
      @if(old('kabupaten_id', $kabupatenId) && $kecamatanId)
          @foreach(\App\Models\Kabupaten::find(old('kabupaten_id',$kabupatenId))->kecamatan as $kec)
              <option value="{{ $kec->id }}" @selected(old('kecamatan_id',$kecamatanId)==$kec->id)>
                  {{ $kec->nama }}
              </option>
          @endforeach
      @endif
  </select>
</div>
<div>
  <label class="label" for="desa">Desa</label>    
  <select name="desa_id" id="desa" class="form-select input input-bordered w-full mb-2">
      <option value="">-- Pilih Desa --</option>
      @if(old('kecamatan_id',$kecamatanId) && $desaId)
          @foreach(\App\Models\Kecamatan::find(old('kecamatan_id',$kecamatanId))->desa as $desa)
              <option value="{{ $desa->id }}" @selected(old('desa_id',$desaId)==$desa->id)>
                  {{ $desa->nama }}
              </option>
          @endforeach
      @endif
  </select>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const kabSelect = document.getElementById('kabupaten');
    const kecSelect = document.getElementById('kecamatan');
    const desaSelect = document.getElementById('desa');

    kabSelect.addEventListener('change', function () {
        const kabId = this.value;
        console.log(kabId);
        
        kecSelect.innerHTML = '<option>Loading...</option>';
        desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';

        if (!kabId) {
            kecSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            return;
        }

        fetch(`{{ url('api/kecamatan') }}?kabupaten_id=${kabId}`)
            .then(resp => resp.json())
            .then(data => {
                let html = '<option value="">-- Pilih Kecamatan --</option>';
                data.forEach(k => {
                    html += `<option value="${k.id}">${k.nama}</option>`;
                });
                kecSelect.innerHTML = html;
            })
            .catch(() => {
                kecSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            });
    });

    kecSelect.addEventListener('change', function () {
        const kecId = this.value;
        desaSelect.innerHTML = '<option>Loading...</option>';

        if (!kecId) {
            desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
            return;
        }

        fetch(`{{ url('api/desa') }}?kecamatan_id=${kecId}`)
            .then(resp => resp.json())
            .then(data => {
                let html = '<option value="">-- Pilih Desa --</option>';
                data.forEach(d => {
                    html += `<option value="${d.id}">${d.nama}</option>`;
                });
                desaSelect.innerHTML = html;
            })
            .catch(() => {
                desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
            });
    });
});
</script>
@endpush
