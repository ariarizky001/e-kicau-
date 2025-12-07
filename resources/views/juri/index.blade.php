@extends('layouts.adminlte')

@section('title', 'Juri')

@section('page-title', 'Juri')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Juri</li>
@endsection

@section('content')
    <style>
        #inlineFormRow input {
            height: 32px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        #inlineFormRow input:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        #inlineFormRow td {
            vertical-align: middle;
            padding: 10px 8px !important;
        }

        #inlineFormRow .btn {
            height: 32px;
            line-height: 1;
        }

        #inlineFormRow small {
            display: block;
            margin-top: 4px;
            height: 16px;
        }
    </style>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Juri</h3>
            <div class="card-tools">
                <a href="{{ route('juri.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Form Tambah
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th>Nama Juri</th>
                            <th style="width: 15%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="juriBody">
                        @forelse($juris as $index => $juri)
                            <tr>
                                <td>{{ $juris->firstItem() + $index }}</td>
                                <td>{{ $juri->nama_juri }}</td>
                                <td>
                                    <a href="{{ route('juri.edit', $juri->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('juri.destroy', $juri->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus juri ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Tidak ada data juri</td>
                            </tr>
                        @endforelse

                        <!-- Inline Add Form Row -->
                        <tr id="inlineFormRow" class="table-light">
                            <td></td>
                            <td>
                                <input type="text" id="inlineNamaJuri" class="form-control form-control-sm"
                                       placeholder="Nama juri" required>
                                <small class="text-danger" id="errorNamaJuri"></small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" id="btnSaveInline" class="btn btn-success"
                                            onclick="saveInlineJuri()" title="Simpan">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" id="btnCancelInline" class="btn btn-secondary"
                                            onclick="cancelInlineJuri()" title="Batal">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $juris->links() }}
            </div>
        </div>
    </div>

    <script>
        function clearInlineForm() {
            document.getElementById('inlineNamaJuri').value = '';
            clearAllErrors();
        }

        function clearAllErrors() {
            document.getElementById('errorNamaJuri').textContent = '';
        }

        function saveInlineJuri() {
            clearAllErrors();

            const namaJuri = document.getElementById('inlineNamaJuri').value.trim();

            if (!namaJuri) {
                document.getElementById('errorNamaJuri').textContent = 'Nama juri harus diisi';
                return;
            }

            const btnSave = document.getElementById('btnSaveInline');
            const btnCancel = document.getElementById('btnCancelInline');
            btnSave.disabled = true;
            btnCancel.disabled = true;
            btnSave.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            const data = {
                nama_juri: namaJuri,
                _token: document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            };

            fetch('{{ route("juri.store-inline") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    // Tambah row baru ke table
                    addNewRowToTable(result.data);

                    // Clear form
                    clearInlineForm();

                    // Show success message
                    showSuccessAlert(result.message);

                    // Enable buttons
                    btnSave.disabled = false;
                    btnCancel.disabled = false;
                    btnSave.innerHTML = '<i class="fas fa-check"></i>';
                }
            })
            .catch(error => {
                console.error('Error:', error);

                if (error.message && !error.errors) {
                    showErrorAlert(error.message);
                }

                if (error.errors) {
                    if (error.errors.nama_juri) {
                        document.getElementById('errorNamaJuri').textContent = error.errors.nama_juri[0];
                    }
                }

                btnSave.disabled = false;
                btnCancel.disabled = false;
                btnSave.innerHTML = '<i class="fas fa-check"></i>';
            });
        }

        function addNewRowToTable(juri) {
            const tbody = document.getElementById('juriBody');

            // Hitung nomor urut: jumlah rows existing + 1
            const existingRows = tbody.querySelectorAll('tr:not(#inlineFormRow)').length;
            const noUrut = existingRows + 1;

            const editLink = `{{ route('juri.edit', ['juri' => '__ID__']) }}`.replace('__ID__', juri.id);
            const deleteLink = `{{ route('juri.destroy', ['juri' => '__ID__']) }}`.replace('__ID__', juri.id);

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${noUrut}</td>
                <td>${escapeHtml(juri.nama_juri)}</td>
                <td>
                    <a href="${editLink}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="${deleteLink}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus juri ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            `;

            // Insert sebelum form row
            const formRow = document.getElementById('inlineFormRow');
            formRow.parentNode.insertBefore(newRow, formRow);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function cancelInlineJuri() {
            clearInlineForm();
        }

        function showSuccessAlert(message) {
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i> <strong>${message}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;

            const cardBody = document.querySelector('.card-body');
            const alertElement = document.createElement('div');
            alertElement.innerHTML = alertHtml;
            cardBody.insertBefore(alertElement.firstElementChild, cardBody.firstChild);

            // Auto dismiss after 4 seconds
            setTimeout(() => {
                const alert = cardBody.querySelector('.alert-success');
                if (alert) {
                    alert.remove();
                }
            }, 4000);
        }

        function showErrorAlert(message) {
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 1rem;">
                    <i class="fas fa-exclamation-circle"></i> <strong>Error:</strong> ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;

            const cardBody = document.querySelector('.card-body');
            const alertElement = document.createElement('div');
            alertElement.innerHTML = alertHtml;
            cardBody.insertBefore(alertElement.firstElementChild, cardBody.firstChild);
        }

        // Add CSRF token meta tag if not exists
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
    </script>
@endsection
