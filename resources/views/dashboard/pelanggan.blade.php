@extends('layouts.app')

@section('title', 'Data Pelanggan - MOTORCARE')

@section('content')

<style>
    .page { padding: 35px; }
    .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; flex-wrap:wrap; gap:15px; }
    .muted { color:#89958b; font-size:13px; }
    .panel { background:white; padding:25px; border-radius:15px; margin-bottom:22px; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:15px; }
    .field label { display:block; font-size:13px; margin-bottom:7px; }
    .field input, .field textarea {
        width:100%; padding:11px; border:1px solid #dce3dc;
        border-radius:8px; font-family:inherit; outline:none;
    }
    .field textarea { min-height:75px; resize:vertical; }
    .btn { padding:11px 17px; border:0; border-radius:8px; cursor:pointer; font-family:inherit; }
    .primary { background:#536b58; color:white; }
    .danger { background:#e66b57; color:white; }
    .secondary { background:#edf1eb; color:#536b58; }
    .table-wrap { overflow-x:auto; }
    table { width:100%; border-collapse:collapse; font-size:13px; }
    th, td { padding:14px 12px; text-align:left; border-bottom:1px solid #edf0ec; }
    th { color:#718875; background:#f7f9f6; }
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
            <h2>Data Pelanggan</h2>
            <p class="muted">Kelola seluruh informasi pelanggan MOTORCARE.</p>
        </div>
    </div>

    <!-- FORM TAMBAH DAN EDIT -->
    <div class="panel">
        <h3 id="formTitle">Tambah Pelanggan</h3>
        <p class="muted" style="margin:7px 0 20px">
            Isi informasi pelanggan berikut.
        </p>

        <form id="pelangganForm">
            <div class="form-grid">

                <div class="field">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required>
                </div>

                <div class="field">
                    <label>Nomor HP</label>
                    <input type="text" name="no_hp" required>
                </div>

                <div class="field">
                    <label>Pekerjaan</label>
                    <input type="text" name="pekerjaan" required>
                </div>

                <div class="field">
                    <label>Alamat</label>
                    <textarea name="alamat" required></textarea>
                </div>

            </div>

            <div style="margin-top:20px; display:flex; gap:10px;">
                <button class="btn primary" type="submit" id="submitBtn">
                    <i class="bi bi-plus-circle"></i> Simpan Pelanggan
                </button>

                <button class="btn secondary" type="button"
                    id="cancelBtn" style="display:none" onclick="resetForm()">
                    Batal Edit
                </button>
            </div>

            <p class="status" id="formMessage"></p>
        </form>
    </div>

    <!-- TABEL PELANGGAN -->
    <div class="panel">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div>
                <h3>Daftar Pelanggan</h3>
                <p class="muted">Data langsung diambil dari database melalui API.</p>
            </div>

            <button class="btn secondary" onclick="loadPelanggan()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th>Pekerjaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="pelangganTable">
                    <tr>
                        <td colspan="6">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
const API = '/api/pelanggan';
let pelangganData = [];
let editId = null;

const form = document.getElementById('pelangganForm');
const table = document.getElementById('pelangganTable');
const message = document.getElementById('formMessage');

function safe(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&':'&amp;', '<':'&lt;', '>':'&gt;',
        '"':'&quot;', "'":'&#039;'
    })[char]);
}

async function loadPelanggan() {
    table.innerHTML = '<tr><td colspan="6">Memuat data...</td></tr>';

    try {
        const response = await fetch(API, {
            headers: { 'Accept': 'application/json' }
        });

        const result = await response.json();

        if (!response.ok || result.success === false) {
            throw new Error(result.message || 'Gagal mengambil data');
        }

        pelangganData = Array.isArray(result.data) ? result.data : [];
        renderTable();

    } catch (error) {
        table.innerHTML = `<tr><td colspan="6">${safe(error.message)}</td></tr>`;
    }
}

function renderTable() {
    if (pelangganData.length === 0) {
        table.innerHTML = '<tr><td colspan="6">Belum ada data pelanggan.</td></tr>';
        return;
    }

    table.innerHTML = pelangganData.map((p, index) => `
        <tr>
            <td>${index + 1}</td>
            <td>${safe(p.nama_lengkap)}</td>
            <td>${safe(p.no_hp)}</td>
            <td>${safe(p.alamat)}</td>
            <td>${safe(p.pekerjaan)}</td>
            <td>
                <div class="actions">
                    <button class="btn secondary" onclick="editPelanggan(${Number(p.id)})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn danger" onclick="deletePelanggan(${Number(p.id)})">
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

    const url = editId ? `${API}/${editId}` : API;
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

            throw new Error(errors || 'Gagal menyimpan data');
        }

        message.textContent = result.message || 'Data berhasil disimpan!';
        message.style.color = '#536b58';

        resetForm();
        await loadPelanggan();

    } catch (error) {
        message.textContent = error.message;
        message.style.color = '#e66b57';
    } finally {
        document.getElementById('submitBtn').disabled = false;
    }
});

function editPelanggan(id) {
    const p = pelangganData.find(item => Number(item.id) === id);
    if (!p) return;

    editId = id;

    form.elements.nama_lengkap.value = p.nama_lengkap ?? '';
    form.elements.no_hp.value = p.no_hp ?? '';
    form.elements.alamat.value = p.alamat ?? '';
    form.elements.pekerjaan.value = p.pekerjaan ?? '';

    document.getElementById('formTitle').textContent = 'Edit Pelanggan';
    document.getElementById('submitBtn').innerHTML = '<i class="bi bi-check-circle"></i> Update Pelanggan';
    document.getElementById('cancelBtn').style.display = 'inline-block';

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function deletePelanggan(id) {
    if (!confirm('Yakin ingin menghapus pelanggan ini?')) return;

    try {
        const response = await fetch(`${API}/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal menghapus data');
        }

        message.textContent = result.message || 'Data berhasil dihapus.';
        message.style.color = '#536b58';

        if (editId === id) resetForm();
        await loadPelanggan();

    } catch (error) {
        message.textContent = error.message;
        message.style.color = '#e66b57';
    }
}

function resetForm() {
    form.reset();
    editId = null;

    document.getElementById('formTitle').textContent = 'Tambah Pelanggan';
    document.getElementById('submitBtn').innerHTML = '<i class="bi bi-plus-circle"></i> Simpan Pelanggan';
    document.getElementById('cancelBtn').style.display = 'none';
}

loadPelanggan();
</script>

@endsection
