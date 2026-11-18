@extends('layouts.app')

@section('title', 'Pembayaran Service - MOTORCARE')

@section('content')

<style>
    .payment-page {
        padding: 35px;
    }

    .payment-header {
        margin-bottom: 25px;
    }

    .payment-muted {
        color: #89958b;
        font-size: 13px;
    }

    .payment-panel {
        background: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 22px;
    }

    .payment-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .payment-field label {
        display: block;
        font-size: 13px;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .payment-field input,
    .payment-field select,
    .payment-field textarea {
        width: 100%;
        padding: 11px;
        border: 1px solid #dce3dc;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        outline: none;
    }

    .payment-field input:focus,
    .payment-field select:focus {
        border-color: #819477;
        box-shadow: 0 0 0 3px rgba(129,148,119,.15);
    }

    .payment-radio {
        display: flex;
        gap: 25px;
        flex-wrap: wrap;
        padding: 10px 0;
    }

    .payment-radio label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 13px;
    }

    .payment-radio input {
        accent-color: #536b58;
    }

    .payment-btn {
        padding: 11px 17px;
        border: 0;
        border-radius: 8px;
        cursor: pointer;
        font-family: inherit;
        font-size: 13px;
    }

    .payment-primary {
        background: #536b58;
        color: white;
    }

    .payment-primary:hover {
        background: #405645;
    }

    .payment-secondary {
        background: #edf1eb;
        color: #536b58;
    }

    .payment-danger {
        background: #e66b57;
        color: white;
    }

    .payment-table-wrap {
        overflow-x: auto;
    }

    .payment-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .payment-table th,
    .payment-table td {
        padding: 14px 12px;
        text-align: left;
        border-bottom: 1px solid #edf0ec;
        white-space: nowrap;
    }

    .payment-table th {
        color: #718875;
        background: #f7f9f6;
    }

    .payment-actions {
        display: flex;
        gap: 7px;
    }

    .payment-message {
        margin-top: 12px;
        font-size: 13px;
    }

    .payment-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        background: #edf1eb;
        color: #536b58;
        font-size: 11px;
    }

    @media(max-width: 650px) {
        .payment-page {
            padding: 18px;
        }

        .payment-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="payment-page">

    <div class="payment-header">
        <h2>Pembayaran Service</h2>
        <p class="payment-muted">
            Kelola pembayaran service pelanggan MOTORCARE.
        </p>
    </div>

    <!-- FORM PEMBAYARAN -->
    <div class="payment-panel">

        <h3 id="paymentFormTitle">Tambah Pembayaran</h3>

        <p class="payment-muted" style="margin: 7px 0 20px">
            Pilih data service dan masukkan jumlah biaya pembayaran.
        </p>

        <form id="paymentForm">

            <div class="payment-grid">

                <div class="payment-field">
                    <label>ID Servis Pelanggan</label>

                    <select name="daftar_service_id" id="serviceSelect" required>
                        <option value="">Memuat data service...</option>
                    </select>
                </div>

                <div class="payment-field">
                    <label>Jumlah Biaya Servis (Rp)</label>

                    <input
                        type="number"
                        name="jumlah_biaya_servis"
                        id="jumlahBiaya"
                        min="0"
                        step="1"
                        placeholder="Masukkan jumlah biaya servis"
                        required
                    >
                </div>

            </div>

            <div style="margin-top: 22px">

                <label style="font-size:13px; font-weight:500">
                    Jenis Pembayaran
                </label>

                <div class="payment-radio">

                    <label>
                        <input
                            type="radio"
                            name="jenis_pembayaran"
                            value="Cash"
                        >
                        Cash / Tunai
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="jenis_pembayaran"
                            value="Non Tunai"
                        >
                        Non Tunai
                    </label>

                </div>

                <p class="payment-muted">
                    Jenis pembayaran boleh dikosongkan jika belum ditentukan.
                </p>

            </div>

            <div class="payment-field" style="margin-top: 15px">
                <label>Keterangan (Opsional)</label>

                <textarea
                    name="keterangan"
                    id="paymentNote"
                    rows="2"
                    placeholder="Tambahkan keterangan pembayaran jika diperlukan"
                ></textarea>
            </div>

            <div style="margin-top: 22px; display:flex; gap:10px; flex-wrap:wrap">

                <button
                    class="payment-btn payment-primary"
                    type="submit"
                    id="paymentSubmit"
                >
                    <i class="bi bi-plus-circle"></i>
                    Simpan Pembayaran
                </button>

                <button
                    class="payment-btn payment-secondary"
                    type="button"
                    id="paymentCancel"
                    onclick="resetPaymentForm()"
                    style="display:none"
                >
                    Batal Edit
                </button>

            </div>

            <p class="payment-message" id="paymentMessage"></p>

        </form>
    </div>

    <!-- TABEL RIWAYAT PEMBAYARAN -->
    <div class="payment-panel">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px">

            <div>
                <h3>Riwayat Pembayaran</h3>
                <p class="payment-muted">
                    Daftar pembayaran yang tersimpan melalui database.
                </p>
            </div>

            <button
                class="payment-btn payment-secondary"
                onclick="loadPayments()"
            >
                <i class="bi bi-arrow-clockwise"></i>
                Refresh
            </button>

        </div>

        <div class="payment-table-wrap">

            <table class="payment-table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Service</th>
                        <th>Nama Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Jumlah Biaya</th>
                        <th>Jenis Pembayaran</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="paymentTable">

                    <tr>
                        <td colspan="8">Memuat data pembayaran...</td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>
const PAYMENT_API = '/api/pembayaran-service';
const SERVICE_API = '/api/daftar-service';

let paymentData = [];
let serviceData = [];
let paymentEditId = null;

const paymentForm = document.getElementById('paymentForm');
const paymentTable = document.getElementById('paymentTable');
const paymentMessage = document.getElementById('paymentMessage');
const serviceSelect = document.getElementById('serviceSelect');

function safePayment(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);
}

function getData(result) {
    return Array.isArray(result) ? result : (result.data ?? []);
}

function getServiceName(service) {
    const pelanggan = service?.pelanggan ?? {};
    return pelanggan.nama_lengkap
        ?? pelanggan.nama
        ?? pelanggan.name
        ?? 'Pelanggan';
}

function getVehicleName(service) {
    const kendaraan = service?.kendaraan ?? {};

    const nama = kendaraan.nama_kendaraan
        ?? kendaraan.merk
        ?? kendaraan.nama
        ?? kendaraan.tipe
        ?? 'Kendaraan';

    const plat = kendaraan.plat_nomor
        ?? kendaraan.nomor_polisi
        ?? kendaraan.no_polisi
        ?? '';

    return plat ? `${nama} - ${plat}` : nama;
}

// MENGAMBIL DATA SERVICE UNTUK DROPDOWN
async function loadServices() {

    try {

        const response = await fetch(SERVICE_API, {
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal mengambil data service');
        }

        serviceData = getData(result);

        serviceSelect.innerHTML =
            '<option value="">-- Pilih ID Service --</option>';

        serviceData.forEach(service => {

            const option = document.createElement('option');

            option.value = service.id;

            option.textContent =
                `Service #${service.id} - ${getServiceName(service)}`;

            serviceSelect.appendChild(option);

        });

        if (serviceData.length === 0) {
            serviceSelect.innerHTML =
                '<option value="">Belum ada data service</option>';
        }

    } catch (error) {

        serviceSelect.innerHTML =
            '<option value="">Gagal mengambil data service</option>';

        console.error(error);

        paymentMessage.textContent = error.message;
        paymentMessage.style.color = '#e66b57';

    }
}

// MENGAMBIL DATA PEMBAYARAN
async function loadPayments() {

    paymentTable.innerHTML =
        '<tr><td colspan="8">Memuat data pembayaran...</td></tr>';

    try {

        const response = await fetch(PAYMENT_API, {
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal mengambil pembayaran');
        }

        paymentData = getData(result);

        renderPayments();

    } catch (error) {

        paymentTable.innerHTML =
            `<tr><td colspan="8">${safePayment(error.message)}</td></tr>`;

    }
}

// MENAMPILKAN DATA PEMBAYARAN
function renderPayments() {

    if (paymentData.length === 0) {

        paymentTable.innerHTML =
            '<tr><td colspan="8">Belum ada data pembayaran.</td></tr>';

        return;
    }

    paymentTable.innerHTML = paymentData.map((p, index) => {

        const service = p.daftar_service ?? p.daftarService ?? {};

        const pelanggan = service.pelanggan ?? {};
        const kendaraan = service.kendaraan ?? {};

        const namaPelanggan = getServiceName(service);
        const kendaraanName = getVehicleName(service);

        const biaya = Number(p.jumlah_biaya_servis ?? 0);

        return `
            <tr>
                <td>${index + 1}</td>

                <td>#${safePayment(p.daftar_service_id)}</td>

                <td>${safePayment(namaPelanggan)}</td>

                <td>${safePayment(kendaraanName)}</td>

                <td>Rp ${biaya.toLocaleString('id-ID')}</td>

                <td>
                    <span class="payment-badge">
                        ${safePayment(p.jenis_pembayaran || 'Belum dipilih')}
                    </span>
                </td>

                <td>${safePayment(p.keterangan || '-')}</td>

                <td>
                    <div class="payment-actions">

                        <button
                            class="payment-btn payment-secondary"
                            onclick="editPayment(${Number(p.id)})"
                            title="Edit"
                        >
                            <i class="bi bi-pencil"></i>
                        </button>

                        <button
                            class="payment-btn payment-danger"
                            onclick="deletePayment(${Number(p.id)})"
                            title="Hapus"
                        >
                            <i class="bi bi-trash"></i>
                        </button>

                    </div>
                </td>
            </tr>
        `;

    }).join('');

}

// SIMPAN DAN UPDATE PEMBAYARAN
paymentForm.addEventListener('submit', async function(e) {

    e.preventDefault();

    const formData = new FormData(paymentForm);

    const data = Object.fromEntries(formData.entries());

    data.daftar_service_id = Number(data.daftar_service_id);
    data.jumlah_biaya_servis = Number(data.jumlah_biaya_servis);

    if (!data.jenis_pembayaran) {
        data.jenis_pembayaran = null;
    }

    if (!data.keterangan) {
        data.keterangan = null;
    }

    const url = paymentEditId
        ? `${PAYMENT_API}/${paymentEditId}`
        : PAYMENT_API;

    const method = paymentEditId ? 'PUT' : 'POST';

    const submitButton = document.getElementById('paymentSubmit');

    submitButton.disabled = true;
    paymentMessage.textContent = 'Menyimpan pembayaran...';
    paymentMessage.style.color = '#89958b';

    try {

        const response = await fetch(url, {

            method: method,

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

            throw new Error(errors || 'Gagal menyimpan pembayaran');

        }

        paymentMessage.textContent =
            result.message || 'Pembayaran berhasil disimpan!';

        paymentMessage.style.color = '#536b58';

        resetPaymentForm();

        await loadPayments();

    } catch (error) {

        paymentMessage.textContent = error.message;
        paymentMessage.style.color = '#e66b57';

    } finally {

        submitButton.disabled = false;

    }

});

// EDIT PEMBAYARAN
function editPayment(id) {

    const p = paymentData.find(item => Number(item.id) === id);

    if (!p) return;

    paymentEditId = id;

    serviceSelect.value = p.daftar_service_id;

    document.getElementById('jumlahBiaya').value =
        p.jumlah_biaya_servis ?? '';

    document.getElementById('paymentNote').value =
        p.keterangan ?? '';

    document.querySelectorAll(
        'input[name="jenis_pembayaran"]'
    ).forEach(radio => {

        radio.checked = radio.value === p.jenis_pembayaran;

    });

    document.getElementById('paymentFormTitle').textContent =
        'Edit Pembayaran';

    document.getElementById('paymentSubmit').innerHTML =
        '<i class="bi bi-check-circle"></i> Update Pembayaran';

    document.getElementById('paymentCancel').style.display =
        'inline-block';

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });

}

// HAPUS PEMBAYARAN
async function deletePayment(id) {

    if (!confirm('Yakin ingin menghapus pembayaran ini?')) return;

    try {

        const response = await fetch(`${PAYMENT_API}/${id}`, {

            method: 'DELETE',

            headers: {
                'Accept': 'application/json'
            }

        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Gagal menghapus pembayaran');
        }

        paymentMessage.textContent =
            result.message || 'Pembayaran berhasil dihapus.';

        paymentMessage.style.color = '#536b58';

        if (paymentEditId === id) {
            resetPaymentForm();
        }

        await loadPayments();

    } catch (error) {

        paymentMessage.textContent = error.message;
        paymentMessage.style.color = '#e66b57';

    }

}

// RESET FORM
function resetPaymentForm() {

    paymentForm.reset();

    paymentEditId = null;

    document.getElementById('paymentFormTitle').textContent =
        'Tambah Pembayaran';

    document.getElementById('paymentSubmit').innerHTML =
        '<i class="bi bi-plus-circle"></i> Simpan Pembayaran';

    document.getElementById('paymentCancel').style.display =
        'none';

}

// JALANKAN SAAT HALAMAN DIBUKA
async function initializePaymentPage() {

    await loadServices();

    await loadPayments();

}

initializePaymentPage();

</script>

@endsection
