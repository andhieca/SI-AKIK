@extends('layouts.app')

@section('title', 'Data Anggaran')

@section('content')
    <div x-data="anggaranData()" x-init="initData()">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Anggaran</h2>
            <div class="flex gap-3">
                <button type="button"
                    @click="$dispatch('open-delete-modal', { action: '{{ route('anggaran.deleteAll') }}', title: 'Hapus Semua Anggaran?', message: 'Seluruh data anggaran tahun ini akan dihapus secara permanen.' })"
                    class="bg-white border border-red-300 hover:bg-red-50 text-red-600 font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-200 shadow-sm">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Semua
                </button>
                <button @click="showImportModal = true"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-200 shadow-sm">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Impor Excel
                </button>
                <button @click="openCreateModal()"
                    class="bg-bedas-600 hover:bg-bedas-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-200 shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Anggaran
                </button>
            </div>
        </div>

        <!-- Success Alert -->
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm flex justify-between items-center">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-600 hover:text-green-800">&times;</button>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b-2 border-gray-100">
                        <tr>
                            <th
                                class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center w-12">
                                #</th>
                            <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">KODE
                            </th>
                            <th class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Urusan/Bidang/Program/Kegiatan/Sub Kegiatan</th>
                            <th
                                class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right w-44">
                                Anggaran (Rp)</th>
                            <th
                                class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right w-44">
                                Realisasi (Rp)</th>
                            <th
                                class="px-4 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center w-32">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($anggarans as $item)
                            @include('anggaran.partials.row', ['item' => $item, 'level' => 0])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg z-50 overflow-hidden transform transition-all">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h2 class="text-xl font-bold text-gray-800" x-text="isEdit ? 'Edit Anggaran' : 'Tambah Anggaran'">
                        </h2>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" :action="formAction" class="p-6 space-y-4">
                        @csrf
                        <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>
                        <input type="hidden" name="parent_id" x-model="form.parent_id">
                        <input type="hidden" name="tahun" x-model="form.tahun">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode</label>
                            <input type="text" x-model="form.kode" name="kode" required
                                class="w-full rounded-lg border-gray-300 focus:ring-bedas-500 focus:border-bedas-500"
                                placeholder="7.01.01...">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Uraian</label>
                            <textarea x-model="form.uraian" name="uraian" rows="3" required
                                class="w-full rounded-lg border-gray-300 focus:ring-bedas-500 focus:border-bedas-500"
                                placeholder="Nama program/kegiatan..."></textarea>
                        </div>

                        <template x-if="isEdit">
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pagu Anggaran (Rp)</label>
                                <input type="text" x-model="form.pagu" name="pagu"
                                    x-on:input="form.pagu = formatRupiah($event.target.value)"
                                    class="w-full rounded-lg border-gray-300 focus:ring-bedas-500 focus:border-bedas-500"
                                    placeholder="0">
                            </div>
                        </template>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showModal = false"
                                class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</button>
                            <button type="submit"
                                class="px-6 py-2 bg-bedas-600 text-white rounded-lg hover:bg-bedas-700 font-bold shadow-md">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Import Modal -->
        <div x-show="showImportModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showImportModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md z-50 overflow-hidden transform transition-all">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h2 class="text-xl font-bold text-gray-800">Impor Data Anggaran</h2>
                        <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('anggaran.import') }}" method="POST" enctype="multipart/form-data"
                        class="p-6 space-y-4">
                        @csrf
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Pastikan file Excel memiliki kolom: <br>
                                        <span class="font-bold">kode</span>, <span class="font-bold">uraian</span>, dan
                                        <span class="font-bold">anggaran</span>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih File (xlsx, csv)</label>
                            <input type="file" name="file" accept=".xlsx,.csv,.xls" required
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-bedas-50 file:text-bedas-700 hover:file:bg-bedas-100 transition duration-200">
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showImportModal = false"
                                class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</button>
                            <button type="submit"
                                class="px-6 py-2 bg-bedas-600 text-white rounded-lg hover:bg-bedas-700 font-bold shadow-md">Upload
                                & Proses</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function anggaranData() {
                return {
                    showModal: false,
                    showImportModal: false,
                    isEdit: false,
                    form: {
                        id: '',
                        kode: '',
                        uraian: '',
                        pagu: '',
                        tahun: '{{ $tahun }}',
                        parent_id: ''
                    },
                    get formAction() {
                        return this.isEdit ? `/anggaran/${this.form.id}` : '{{ route('anggaran.store') }}';
                    },
                    formatRupiah(value) {
                        if (!value) return '';
                        return value.toString().replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    },
                    formatNumber(value) {
                        return new Intl.NumberFormat('id-ID').format(value);
                    },
                    bubbleUpPagu(id) {
                        const row = document.getElementById(`row-${id}`);
                        if (!row) return;

                        const parentId = row.getAttribute('data-parent-id');
                        if (!parentId) return;

                        // Calculate sum of all children for this parent
                        const children = document.querySelectorAll(`tr[data-parent-id="${parentId}"]`);
                        let total = 0;
                        children.forEach(child => {
                            const paguCell = child.querySelector('td[data-pagu]');
                            total += parseFloat(paguCell.getAttribute('data-pagu') || 0);
                        });

                        // Update parent cell
                        const parentCell = document.getElementById(`pagu-${parentId}`);
                        if (parentCell) {
                            parentCell.setAttribute('data-pagu', total);
                            const span = parentCell.querySelector('span');
                            if (span) span.innerText = `Rp ${this.formatNumber(total)}`;

                            // Continue bubbling up
                            this.bubbleUpPagu(parentId);
                        }
                    },
                    async updatePagu(id, value) {
                        const numericValue = parseFloat(value.replace(/\D/g, '') || 0);

                        // Update local cell first for the bubble calculation
                        const cell = document.getElementById(`pagu-${id}`);
                        if (cell) cell.setAttribute('data-pagu', numericValue);

                        // Bubble up the sum locally
                        this.bubbleUpPagu(id);

                        // Visual feedback: saving
                        const input = event.target;
                        const originalBg = input.style.backgroundColor;
                        input.style.backgroundColor = '#fef3c7'; // yellowish

                        try {
                            const response = await fetch(`/anggaran/${id}/update-pagu`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ pagu: value })
                            });

                            if (!response.ok) throw new Error('Refresh halaman diperlukan (Sesi Berakhir)');

                            input.style.backgroundColor = '#dcfce7'; // green success
                            setTimeout(() => input.style.backgroundColor = originalBg, 1000);

                        } catch (error) {
                            alert(`Gagal menyimpan: ${error.message}`);
                            input.style.backgroundColor = '#fee2e2'; // red error
                            window.location.reload(); // back to safe state
                        }
                    },
                    openCreateModal(parentId = null, kodePrefix = '') {
                        this.isEdit = false;
                        this.form = { id: '', kode: kodePrefix, uraian: '', pagu: '', tahun: '{{ $tahun }}', parent_id: parentId };
                        this.showModal = true;
                    },
                    openEditModal(data) {
                        this.isEdit = true;
                        this.form = {
                            id: data.id,
                            kode: data.kode,
                            uraian: data.uraian,
                            pagu: data.pagu.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."),
                            tahun: data.tahun,
                            parent_id: data.parent_id
                        };
                        this.showModal = true;
                    },
                    initData() { }
                }
            }
        </script>
@endsection