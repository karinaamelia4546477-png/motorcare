@extends('layouts.app')

@section('title', 'Daftar Service - MOTORCARE')

@section('content')

<style>
    .page { padding: 35px; }
    .page-header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:25px;
    }
    .muted { color:#89958b; font-size:13px; }
    .panel {
        background:white;
        padding:25px;
        border-radius:15px;
        margin-bottom:22px;
    }
    .form-grid {
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:15px;
    }
    .field label {
        display:block;
        font-size:13px;
        margin-bottom:7px;
    }
    .field input, .field select, .field textarea {
        width:100%;
        padding:11px;
        border:1px solid #dce3dc;
        border-radius:8px;
        font-family:inherit;
        outline:none;
        background:white;
    }
    .field textarea {
        min-height:85px;
        resize:vertical;
    }
    .btn {
        padding:11px 17px;
        border:0;
        border-radius:8px;
        cursor:pointer;
        font-family:inherit;
    }
    .primary { background:#536b58; color:white; }
    .danger { background:#e66b57; color:white; }
    .secondary { background:#edf1eb; color:#536b58; }
    .table-wrap { overflow-x:auto; }
    table {
        width:100%;
        border-collapse:collapse;
        font-size:13px;
    }
    th, td {
        padding:14px 12px;
        text-align:left;
        border-bottom:1px solid #edf0ec;
    }
    th {
        color:#718875;
        background:#f7f9f6;
    }
    .actions { display:flex; gap:7px; }
    .status { margin-top:12px; font-size:13px; }
    @media(max-width:650px) {
        .page { padding:18px; }
        .form-grid { grid-template-columns:1fr; }
    }
</style>

<div class="page">

    <div class="page-header">
        <div>
            <h2>Daftar Service</h2>
            <p class="muted">
                Kelola data dan riwayat service kendaraan MOTORCARE.
            </p>
        </div>
    </div>

    <!-- FORM SERVICE -->
    <div class="panel">
        <h3 id="formTitle">Tambah Service</h3>
        <p class="muted" style="margin:7px 0 20px">
            Isi informasi service kendaraan berikut.
        </p>

        <form id="serviceForm">

            <div class="form-grid">

                <div class="field">
                    <label>Pilih Pelanggan</label>
                    <select name="pelanggan_id" id="pelangganSelect" required>
                        <option value="">Memuat pelanggan...</option>
                    </select>
                </div>

                <div class="field">
                    <label>Pilih Kendaraan</label>
                    <select name="kendaraan_id" id="kendaraanSelect" required>
                        <option value="">Memuat kendaraan...</option>
                    </select>
                </div>

                <div class="field">
                    <label>Tanggal Service</label>
                    <input type="date" name="tanggal_servis" required>
                </div>

                <div class="field">
                    <label>Keluhan Kendaraan</label>
                    <textarea
                        name="keluhan_kendaraan"
                        placeholder="Masukkan keluhan kendaraan..."
                        required
                    ></textarea>
                </div>

            </div>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button class="btn primary" type="submit" id="submitBtn">
                    <i class="bi bi-plus-circle"></i> Simpan Service
                </button>

                <button class="btn secondary"
                    type="button"
                    id="cancelBtn"
                    style="display:none"
                    onclick="resetForm()">
                    Batal Edit
                </button>
            </div>

            <p class="status" id="formMessage"></p>

        </form>
    </div>

    <!-- TABEL SERVICE -->
    <div class="panel">

        <div style="display:flex; justify-content:space-between;
                    align-items:center; margin-bottom:20px; gap:10px;">
            <div>
                <h3>Riwayat Service</h3>
                <p class="muted">
                    Data service kendaraan yang terdaftar.
                </p>
            </div>

            <button class="btn secondary" onclick="loadService()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Keluhan</th>
                        <th>Tanggal Service</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="serviceTable">
                    <tr>
                        <td colspan="6">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</div>

<script>
const API_SERVICE = '/api/daftar-service';
const API_PELANGGAN = '/api/pelanggan';
const API_KENDARAAN = '/api/kendaraan';

let serviceData = [];
let editId = null;

const form = document.getElementById('serviceForm');
const table = document.getElementById('serviceTable');
const message = document.getElementById('formMessage');

function safe(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&':'&amp;',
        '<':'&lt;',
        '>':'&gt;',
        '"':'&quot;',
        "'":'&#039;'
    })[char]);
}

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

        throw new Error(errors || 'Terjadi kesalahan pada server.');
    }

    return result;
}

function getArray(result) {
    if (Array.isArray(result)) return result;
    if (Array.isArray(result.data)) return result.data;
    return [];
}

// MEMUAT PILIHAN PELANGGAN DAN KENDARAAN
async function loadOptions() {
    const pelangganSelect = document.getElementById('pelangganSelect');
    const kendaraanSelect = document.getElementById('kendaraanSelect');

    try {
        const [pelangganResult, kendaraanResult] = await Promise.all([
            requestJSON(API_PELANGGAN),
            requestJSON(API_KENDARAAN)
        ]);

        const pelanggan = getArray(pelangganResult);
        const kendaraan = getArray(kendaraanResult);

        pelangganSelect.innerHTML =
            '<option value="">-- Pilih Pelanggan --</option>';

        pelanggan.forEach(p => {
            pelangganSelect.innerHTML += `
                <option value="${Number(p.id)}">
                    ${safe(p.nama_lengkap || p.nama || 'Pelanggan #' + p.id)}
                </option>
            `;
        });

        kendaraanSelect.innerHTML =
            '<option value="">-- Pilih Kendaraan --</option>';

        kendaraan.forEach(k => {
            kendaraanSelect.innerHTML += `
                <option value="${Number(k.id)}">
                    ${safe(k.no_plat || 'Kendaraan #' + k.id)}
                    - ${safe(k.jenis_kendaraan || '')}
                </option>
            `;
        });

    } catch (error) {
        message.textContent = 'Gagal memuat pilihan: ' + error.message;
        message.style.color = '#e66b57';
    }
}

// MEMUAT DATA SERVICE
async function loadService() {
    table.innerHTML =
        '<tr><td colspan="6">Memuat data...</td></tr>';

    try {
        const result = await requestJSON(API_SERVICE);
        serviceData = getArray(result);
        renderTable();

    } catch (error) {
        table.innerHTML = `
            <tr>
                <td colspan="6">${safe(error.message)}</td>
            </tr>
        `;
    }
}

// MENAMPILKAN TABEL
function renderTable() {
    if (serviceData.length === 0) {
        table.innerHTML = `
            <tr>
                <td colspan="6">Belum ada data service.</td>
            </tr>
        `;
        return;
    }

    table.innerHTML = serviceData.map((s, index) => `
        <tr>
            <td>${index + 1}</td>
            <td>${safe(
                s.pelanggan?.nama_lengkap ||
                s.pelanggan?.nama ||
                'Pelanggan #' + s.pelanggan_id
            )}</td>
            <td>${safe(
                s.kendaraan?.no_plat ||
                'Kendaraan #' + s.kendaraan_id
            )}</td>
            <td>${safe(s.keluhan_kendaraan)}</td>
            <td>${safe(s.tanggal_servis)}</td>
            <td>
                <div class="actions">
                    <button class="btn secondary"
                        onclick="editService(${Number(s.id)})">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button class="btn danger"
                        onclick="deleteService(${Number(s.id)})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// TAMBAH DAN UPDATE SERVICE
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

    message.textContent = 'Menyimpan data...';
    message.style.color = '#536b58';

    try {
        const result = await requestJSON(url, {
            method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        message.textContent =
            result.message || 'Data service berhasil disimpan!';

        message.style.color = '#536b58';

        resetForm();
        await loadService();

    } catch (error) {
        message.textContent = error.message;
        message.style.color = '#e66b57';

    } finally {
        submitBtn.disabled = false;
    }
});

// EDIT SERVICE
function editService(id) {
    const s = serviceData.find(
        item => Number(item.id) === id
    );

    if (!s) return;

    editId = id;

    form.elements.pelanggan_id.value = s.pelanggan_id ?? '';
    form.elements.kendaraan_id.value = s.kendaraan_id ?? '';
    form.elements.keluhan_kendaraan.value =
        s.keluhan_kendaraan ?? '';

    form.elements.tanggal_servis.value =
        String(s.tanggal_servis ?? '').substring(0, 10);

    document.getElementById('formTitle').textContent =
        'Edit Data Service';

    document.getElementById('submitBtn').innerHTML =
        '<i class="bi bi-check-circle"></i> Update Service';

    document.getElementById('cancelBtn').style.display =
        'inline-block';

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// HAPUS SERVICE
async function deleteService(id) {
    if (!confirm('Yakin ingin menghapus data service ini?')) {
        return;
    }

    try {
        const result = await requestJSON(`${API_SERVICE}/${id}`, {
            method: 'DELETE'
        });

        message.textContent =
            result.message || 'Data service berhasil dihapus.';

        message.style.color = '#536b58';

        if (editId === id) resetForm();

        await loadService();

    } catch (error) {
        message.textContent = error.message;
        message.style.color = '#e66b57';
    }
}

// RESET FORM
function resetForm() {
    form.reset();
    editId = null;

    document.getElementById('formTitle').textContent =
        'Tambah Service';

    document.getElementById('submitBtn').innerHTML =
        '<i class="bi bi-plus-circle"></i> Simpan Service';

    document.getElementById('cancelBtn').style.display =
        'none';
}

// JALANKAN HALAMAN
async function initPage() {
    await loadOptions();
    await loadService();
}

initPage();
</script>

@endsection
