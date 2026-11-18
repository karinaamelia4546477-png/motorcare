@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Data Service</h3>
            <p class="text-muted">
                Kelola data pengerjaan service kendaraan.
            </p>
        </div>

        <button class="btn text-white"
            style="background-color: #819477;"
            onclick="openAddModal()">
            + Tambah Data Service
        </button>
    </div>

    <div id="pageMessage" class="mb-3"></div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Service</th>
                            <th>Keluhan Kendaraan</th>
                            <th>Estimasi Service</th>
                            <th>Nama Mekanik</th>
                            <th>Sparepart Pengganti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="serviceTable">
                        <tr>
                            <td colspan="7"
                                class="text-center text-muted py-4">
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- MODAL TAMBAH DAN EDIT -->
<div class="modal fade" id="tambahService" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">
                    Tambah Data Service
                </h5>

                <button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <form id="serviceForm">

                <div class="modal-body">

                    <div id="formMessage" class="mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label">ID Service</label>

                        <select name="daftar_service_id"
                            id="daftarServiceSelect"
                            class="form-select" required>
                            <option value="">Memuat data service...</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Keluhan Kendaraan
                        </label>

                        <textarea
                            id="keluhanKendaraan"
                            class="form-control"
                            rows="3"
                            placeholder="Keluhan otomatis muncul"
                            readonly></textarea>

                        <small class="text-muted">
                            Keluhan diambil dari Daftar Service.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Estimasi Service
                        </label>

                        <input type="number"
                            name="estimasi_service"
                            class="form-control"
                            min="0"
                            step="any"
                            placeholder="Masukkan estimasi service"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Mekanik</label>

                        <input type="text"
                            name="nama_mekanik"
                            class="form-control"
                            placeholder="Masukkan nama mekanik"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Sparepart Pengganti
                        </label>

                        <textarea
                            name="sparepart_pengganti"
                            class="form-control"
                            rows="2"
                            placeholder="Masukkan sparepart pengganti"></textarea>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                        class="btn text-white"
                        id="submitBtn"
                        style="background-color: #819477;">
                        Simpan
                    </button>

                </div>
            </form>

        </div>
    </div>
</div>

<script>
const API_SERVICE = '/api/data-service';
const API_DAFTAR = '/api/daftar-service';

let serviceData = [];
let daftarData = [];
let editId = null;

const form = document.getElementById('serviceForm');
const table = document.getElementById('serviceTable');
const selectService = document.getElementById('daftarServiceSelect');
const keluhanInput = document.getElementById('keluhanKendaraan');

const modalElement = document.getElementById('tambahService');
const serviceModal = bootstrap.Modal.getOrCreateInstance(modalElement);

// Mengamankan teks sebelum ditampilkan ke tabel
function safe(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);
}

// Mengirim request ke API
async function requestJSON(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            'Accept': 'application/json',
            ...(options.headers || {})
        }
    });

    const result = await response.json();

    if (!response.ok) {
        const errors = result.errors
            ? Object.values(result.errors).flat().join(', ')
            : result.message;

        throw new Error(errors || 'Terjadi kesalahan.');
    }

    return result;
}

function getArray(result) {
    if (Array.isArray(result)) return result;
    if (Array.isArray(result.data)) return result.data;
    return [];
}

// Menampilkan pesan
function showMessage(elementId, message, success = true) {
    const element = document.getElementById(elementId);

    element.innerHTML = `
        <div class="alert ${success ? 'alert-success' : 'alert-danger'} py-2">
            ${safe(message)}
        </div>
    `;
}

// Mengambil data Daftar Service untuk dropdown
async function loadDaftarService() {
    const result = await requestJSON(API_DAFTAR);

    daftarData = getArray(result);

    selectService.innerHTML =
        '<option value="">-- Pilih ID Service --</option>';

    daftarData.forEach(item => {
        const pelanggan =
            item.pelanggan?.nama_lengkap ||
            item.pelanggan?.nama ||
            'Pelanggan';

        const kendaraan =
            item.kendaraan?.no_plat ||
            'Kendaraan';

        const option = new Option(
            `Service #${item.id} - ${pelanggan} - ${kendaraan}`,
            item.id
        );

        selectService.add(option);
    });
}

// Mengisi keluhan secara otomatis
selectService.addEventListener('change', function() {
    const selected = daftarData.find(
        item => Number(item.id) === Number(this.value)
    );

    keluhanInput.value =
        selected?.keluhan_kendaraan || '';
});

// Mengambil data Data Service
async function loadService() {
    table.innerHTML = `
        <tr>
            <td colspan="7" class="text-center">
                Memuat data...
            </td>
        </tr>
    `;

    try {
        const result = await requestJSON(API_SERVICE);

        serviceData = getArray(result);

        renderTable();

    } catch (error) {
        table.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-danger">
                    ${safe(error.message)}
                </td>
            </tr>
        `;
    }
}

// Menampilkan data ke tabel
function renderTable() {
    if (serviceData.length === 0) {
        table.innerHTML = `
            <tr>
                <td colspan="7"
                    class="text-center text-muted py-4">
                    Belum ada data service.
                </td>
            </tr>
        `;
        return;
    }

    table.innerHTML = serviceData.map((item, index) => `
        <tr>
            <td>${index + 1}</td>

            <td>
                <span class="fw-semibold">
                    #${safe(item.daftar_service_id)}
                </span>
            </td>

            <td>${safe(item.keluhan_kendaraan)}</td>

            <td>
                ${safe(item.estimasi_service)}
            </td>

            <td>${safe(item.nama_mekanik)}</td>

            <td>
                ${safe(item.sparepart_pengganti || '-')}
            </td>

            <td>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-success"
                        onclick="editService(${Number(item.id)})">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button class="btn btn-sm btn-outline-danger"
                        onclick="deleteService(${Number(item.id)})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Membuka modal tambah
function openAddModal() {
    resetForm();

    document.getElementById('modalTitle').textContent =
        'Tambah Data Service';

    serviceModal.show();
}

// Menambah dan memperbarui data
form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const data = Object.fromEntries(
        new FormData(form).entries()
    );

    const url = editId
        ? `${API_SERVICE}/${editId}`
        : API_SERVICE;

    const method = editId ? 'PUT' : 'POST';

    const submitBtn = document.getElementById('submitBtn');

    submitBtn.disabled = true;

    try {
        const result = await requestJSON(url, {
            method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        serviceModal.hide();

        resetForm();

        showMessage(
            'pageMessage',
            result.message || 'Data berhasil disimpan.'
        );

        await loadService();

    } catch (error) {
        showMessage('formMessage', error.message, false);

    } finally {
        submitBtn.disabled = false;
    }
});

// Edit data
function editService(id) {
    const item = serviceData.find(
        data => Number(data.id) === Number(id)
    );

    if (!item) return;

    editId = id;

    form.elements.daftar_service_id.value =
        item.daftar_service_id;

    keluhanInput.value = item.keluhan_kendaraan || '';

    form.elements.estimasi_service.value =
        item.estimasi_service;

    form.elements.nama_mekanik.value =
        item.nama_mekanik;

    form.elements.sparepart_pengganti.value =
        item.sparepart_pengganti || '';

    document.getElementById('modalTitle').textContent =
        'Edit Data Service';

    document.getElementById('submitBtn').textContent =
        'Update Data';

    serviceModal.show();
}

// Hapus data
async function deleteService(id) {
    if (!confirm('Yakin ingin menghapus data service ini?')) {
        return;
    }

    try {
        const result = await requestJSON(
            `${API_SERVICE}/${id}`,
            { method: 'DELETE' }
        );

        showMessage(
            'pageMessage',
            result.message || 'Data berhasil dihapus.'
        );

        await loadService();

    } catch (error) {
        showMessage('pageMessage', error.message, false);
    }
}

// Reset form
function resetForm() {
    form.reset();

    editId = null;

    keluhanInput.value = '';

    document.getElementById('modalTitle').textContent =
        'Tambah Data Service';

    document.getElementById('submitBtn').textContent =
        'Simpan';

    document.getElementById('formMessage').innerHTML = '';
}

// Menjalankan halaman
async function initPage() {
    try {
        await loadDaftarService();
        await loadService();
    } catch (error) {
        showMessage('pageMessage', error.message, false);
    }
}

initPage();
</script>

@endsection
