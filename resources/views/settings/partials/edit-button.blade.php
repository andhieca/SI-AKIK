<div x-data="{ open: false }">
    <button @click="open = true" class="text-gray-400 hover:text-bedas-600 transition" title="Edit">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
            </path>
        </svg>
    </button>

    <!-- Edit Modal -->
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="open = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md z-50 overflow-hidden text-left">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Edit {{ $pejabat->jabatan }}</h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form action="{{ route('pejabat.update', $pejabat->id) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ $pejabat->nama }}"
                            class="w-full rounded-lg border-gray-300 focus:ring-bedas-500 focus:border-bedas-500"
                            required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                        <input type="text" name="nip" value="{{ $pejabat->nip }}"
                            class="w-full rounded-lg border-gray-300 focus:ring-bedas-500 focus:border-bedas-500"
                            required>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="open = false"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-bedas-600 text-white rounded-lg hover:bg-bedas-700">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>