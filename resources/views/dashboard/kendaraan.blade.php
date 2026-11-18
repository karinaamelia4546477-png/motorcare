@extends('layouts.app')

@section('title', 'Data Kendaraan - MOTORCARE')

@section('content')

<style>
    .page { padding:35px; }
    .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; flex-wrap:wrap; gap:15px; }
    .muted { color:#89958b; font-size:13px; }
    .panel { background:white; padding:25px; border-radius:15px; margin-bottom:22px; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
    .field label { display:block; font-size:13px; margin-bottom:7px; }
    .field input, .field select {
        width:100%; padding:11px; border:1px solid #dce3dc;
        border-radius:8px; font-family:inherit;
    }
    .btn { padding:11px 17px; border:0; border-radius:8px; cursor:pointer; font-family:inherit; }
    .primary { background:#536b58; color:white; }
    .danger { background:#e66b57; color:white; }
    .secondary { background:#edf1eb; color:#536b58; }
    .table-wrap { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:13px; }
    th,td { padding:14px 12px; text-align:left; border-bottom:1px solid #edf0ec; }
    th { color:#718875; background:#f7f9f6; }
    .actions { display:flex; gap:7px; }
    @media(max-width:650px) {
        .page { padding:18px; }
        .form-grid { grid-template-columns:1fr; }
    }
</style>

<div class="page">

    <div class="header">
        <div>
            <h2>Data Kendaraan</h2>
            <p class="muted">Kelola informasi kendaraan pelanggan MOTORCARE.</p>
        </div>
    </div>

    <!-- FORM KENDARAAN -->
    <div class="panel">
        <h3 id="formTitle">Tambah Kendaraan</h3>
        <p class="muted" style="margin:7px 0 20px">Masukkan informasi kendaraan.</p>

        <form id="kendaraanForm">

            <div class="form-grid">

                <div class="field">
                    <label>Nomor Plat</label>
                    <input name="no_plat" type="text" placeholder="Contoh: BK 1234 ABC" required>
                </div>

                <div class="field">
                    <label>Jenis Kendaraan</label>
                    <select name="jenis_kendaraan" required>
                        <option value="">Pilih Jenis</option>
                        <option value="Matic">Matic</option>
                        <option value="Manual Transmisi">Manual Transmisi</option>
                    </select>
                </div>

                <div class="field">
                    <label>Nomor STNK</label>
                    <input name="no_stnk" type="text" required>
                </div>

                <div class="field">
                    <label>Tahun Pembuatan</label>
                    <input name="tahun_pembuatan" type="number" min="1900" max="2026" required>
                </div>

                <div class="field">
                    <label>Nama Pemilik</label>
                    <input name="nama_pemilik" type="text" required>
                </div>

                <div class="field">
                    <label>Warna Kendaraan</label>
                    <input name="warna_kendaraan" type="text" required>
                </div>

            </div>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button class="btn primary" type="submit" id="submitBtn">
                    <i class="bi bi-plus-circle"></i> Simpan Kendaraan
                </button>

                <button class="btn secondary" type="button"
                    id="cancelBtn" style="display:none" onclick="resetForm()">
                    Batal Edit
                </button>
            </div>

            <p id="formMessage" style="font-size:13px; margin-top:12px;"></p>

        </form>
    </div>

    <!-- TABEL KENDARAAN -->
    <div class="panel">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:10px;">
            <div>
                <h3>Daftar Kendaraan</h3>
                <p class="muted">Data kendaraan dari database.</p>
            </div>

            <button class="btn secondary" onclick="loadKendaraan()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Plat</th>
                        <th>Jenis</th>
                        <th>No. STNK</th>
                        <th>Tahun</th>
                        <th>Pemilik</th>
                        <th>Warna</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="kendaraanTable">
                    <tr><td colspan="8">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>

    </div>

</div>

<script>
const API_KENDARAAN = '/api/kendaraan';
let kendaraanData = [];
let editId = null;

const form = document.getElementById('kendaraanForm');
const table = document.getElementById('kendaraanTable');
const message = document.getElementById('formMessage');

function safe(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&':'&amp;', '<':'&lt;', '>':'&gt;',
        '"':'&quot;', "'":'&#039;'
    })[char]);
}

async function loadKendaraan() {
    table.innerHTML = '<tr><td colspan="8">Memuat data...</td></tr>';

    try {
        const response = await fetch(API_KENDARAAN, {
            headers: { 'Accept': 'application/json' }
        });

        const result = await response.json();

        if (!response.ok || result.success === false) {
            throw new Error(result.message || 'Gagal mengambil data kendaraan');
        }

        kendaraanData = Array.isArray(result.data) ? result.data : [];
        renderTable();

    } catch (error) {
        table.innerHTML = `<tr><td colspan="8">${safe(error.message)}</td></tr>`;
    }
}

function renderTable() {
    if (kendaraanData.length === 0) {
        table.innerHTML = '<tr><td colspan="8">Belum ada data kendaraan.</td></tr>';
        return;
    }

    table.innerHTML = kendaraanData.map((k, index) => `
        <tr>
            <td>${index + 1}</td>
            <td>${safe(k.no_plat)}</td>
            <td>${safe(k.jenis_kendaraan)}</td>
            <td>${safe(k.no_stnk)}</td>
            <td>${safe(k.tahun_pembuatan)}</td>
            <td>${safe(k.nama_pemilik)}</td>
            <td>${safe(k.warna_kendaraan)}</td>
            <td>
                <div class="actions">
                    <button class="btn secondary" onclick="editKendaraan(${Number(k.id)})">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button class="btn danger" onclick="deleteKendaraan(${Number(k.id)})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const data = Object.fromEntries(new FormData(form).entries());

    data.tahun_pembuatan = Number(data.tahun_pembuatan);

    const url = editId ? `${API_KENDARAAN}/${editId}` : API_KENDARAAN;
    const method = editId ? 'PUT' : 'POST';

    document.getElementById('submitBtn').disabled = true;
    message.textContent = 'Menyimpan data...';

    try {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            const errors = result.errors
                ? Object.values(result.errors).flat().join(', ')
                : result.message;

            throw new Error(errors || 'Gagal menyimpan kendaraan');
        }

        message.textContent = result.message || 'Data berhasil disimpan!';
        message.style.color = '#536b58';

        resetForm();
        await loadKendaraan();

    } catch (error) {
        message.textContent = error.message;
        message.style.color = '#e66b57';
    } finally {
        document.getElementById('submitBtn').disabled = false;
    }
});

function editKendaraan(id) {
    const k = kendaraanData.find(item => Number(item.id) === id);
    if (!k) return;

    editId = id;

    form.elements.no_plat.value = k.no_plat ?? '';
    form.elements.jenis_kendaraan.value = k.jenis_kendaraan ?? '';
    form.elements.no_stnk.value = k.no_stnk ?? '';
    form.elements.tahun_pembuatan.value = k.tahun_pembuatan ?? '';
    form.elements.nama_pemilik.value = k.nama_pemilik ?? '';
    form.elements.warna_kendaraan.value = k.warna_kendaraan ?? '';

    document.getElementById('formTitle').textContent = 'Edit Kendaraan';
    document.getElementById('submitBtn').innerHTML = '<i class="bi bi-check-circle"></i> Update Kendaraan';
    document.getElementById('cancelBtn').style.display = 'inline-block';

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function deleteKendaraan(id) {
    if (!confirm('Yakin ingin menghapus kendaraan ini?')) return;

    try {
        const response = await fetch(`${API_KENDARAAN}/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal menghapus kendaraan');
        }

        message.textContent = result.message || 'Kendaraan berhasil dihapus.';
        message.style.color = '#536b58';

        if (editId === id) resetForm();
        await loadKendaraan();

    } catch (error) {
        message.textContent = error.message;
        message.style.color = '#e66b57';
    }
}

function resetForm() {
    form.reset();
    editId = null;

    document.getElementById('formTitle').textContent = 'Tambah Kendaraan';
    document.getElementById('submitBtn').innerHTML = '<i class="bi bi-plus-circle"></i> Simpan Kendaraan';
    document.getElementById('cancelBtn').style.display = 'none';
}

loadKendaraan();
</script>

@endsection
