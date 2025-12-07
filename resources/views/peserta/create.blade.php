@extends('layouts.adminlte')

@section('title', 'Tambah Peserta & Burung')

@section('page-title', 'Tambah Peserta & Burung')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('peserta.index') }}">Peserta & Burung</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Peserta & Burung</h3>
        </div>
        <form action="{{ route('peserta.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="kelas_lomba_id">Kelas Lomba <span class="text-danger">*</span></label>
                    <select class="form-control @error('kelas_lomba_id') is-invalid @enderror" 
                            id="kelas_lomba_id" name="kelas_lomba_id" required>
                        <option value="">Pilih Kelas Lomba</option>
                        @foreach($kelasLomba as $kelas)
                            @php
                                $jumlahPeserta = $kelas->peserta_count ?? $kelas->peserta->count();
                                $batasPeserta = $kelas->batas_peserta;
                                $isFull = $batasPeserta && $jumlahPeserta >= $batasPeserta;
                            @endphp
                            <option value="{{ $kelas->id }}" 
                                    {{ old('kelas_lomba_id') == $kelas->id ? 'selected' : '' }}
                                    data-batas="{{ $batasPeserta }}"
                                    data-jumlah="{{ $jumlahPeserta }}"
                                    {{ $isFull ? 'disabled' : '' }}>
                                {{ $kelas->nomor_kelas }} - {{ $kelas->nama_kelas }}
                                @if($isFull)
                                    (PENUH)
                                @elseif($batasPeserta)
                                    ({{ $jumlahPeserta }}/{{ $batasPeserta }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Nomor urut akan di-generate otomatis</small>
                    <div id="info-batas" class="mt-2" style="display: none;">
                        <div class="alert alert-info mb-0">
                            <small>
                                <strong>Info:</strong> 
                                <span id="info-text"></span>
                            </small>
                        </div>
                    </div>
                    @error('kelas_lomba_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="nama_pemilik">Nama Pemilik (NAMA PESERTA) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_pemilik') is-invalid @enderror" 
                           id="nama_pemilik" name="nama_pemilik" 
                           placeholder="Masukkan nama pemilik" 
                           value="{{ old('nama_pemilik') }}" required>
                    @error('nama_pemilik')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="nama_burung">Nama Burung (NAMA BURUNG) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_burung') is-invalid @enderror" 
                           id="nama_burung" name="nama_burung" 
                           placeholder="Masukkan nama burung" 
                           value="{{ old('nama_burung') }}" required>
                    @error('nama_burung')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="nomor_gantangan">Nomor Gantangan (G) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nomor_gantangan') is-invalid @enderror" 
                           id="nomor_gantangan" name="nomor_gantangan" 
                           placeholder="Masukkan nomor gantangan burung" 
                           value="{{ old('nomor_gantangan') }}">
                    <small class="form-text text-muted">Nomor gantangan tempat burung digantung</small>
                    @error('nomor_gantangan')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="alamat_team">Alamat/Team</label>
                    <input type="text" class="form-control @error('alamat_team') is-invalid @enderror" 
                           id="alamat_team" name="alamat_team" 
                           placeholder="Masukkan alamat atau nama team" 
                           value="{{ old('alamat_team') }}">
                    @error('alamat_team')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('peserta.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#kelas_lomba_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const batas = selectedOption.data('batas');
                const jumlah = selectedOption.data('jumlah');
                const infoDiv = $('#info-batas');
                const infoText = $('#info-text');

                if (batas) {
                    const sisa = batas - jumlah;
                    if (sisa > 0) {
                        infoText.html(`Kelas ini memiliki batas <strong>${batas} peserta</strong>. Saat ini ada <strong>${jumlah} peserta</strong>. Sisa slot: <strong>${sisa}</strong>`);
                        infoDiv.removeClass('alert-danger').addClass('alert-info').show();
                    } else {
                        infoText.html(`Kelas ini sudah <strong>PENUH</strong> (${jumlah}/${batas}). Tidak dapat menambah peserta lagi.`);
                        infoDiv.removeClass('alert-info').addClass('alert-danger').show();
                    }
                } else {
                    infoText.html(`Kelas ini tidak memiliki batas peserta. Saat ini ada <strong>${jumlah} peserta</strong>.`);
                    infoDiv.removeClass('alert-danger').addClass('alert-info').show();
                }
            });

            // Trigger on page load if kelas already selected
            if ($('#kelas_lomba_id').val()) {
                $('#kelas_lomba_id').trigger('change');
            }
        });
    </script>
@endpush

