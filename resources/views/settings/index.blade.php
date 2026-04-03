@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="space-y-8" x-data="{ activeTab: 'pejabat', showAddModal: false }">

        <!-- Success Alert -->
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm relative">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20">
                        <title>Close</title>
                        <path
                            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Data Pejabat Struktural Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-bedas-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    Data Pejabat Struktural
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Camat -->
                @if($camat)
                    <div class="bg-blue-50 rounded-lg p-5 border border-blue-100 hover:shadow-md transition duration-200">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span
                                    class="inline-block px-2 py-1 text-xs font-semibold tracking-wide text-blue-800 bg-blue-200 rounded-full mb-2">
                                    CAMAT
                                </span>
                                <h4 class="text-gray-900 font-bold text-lg">{{ $camat->nama }}</h4>
                                <p class="text-gray-500 text-sm">NIP. {{ $camat->nip }}</p>
                            </div>
                            @include('settings.partials.edit-button', ['pejabat' => $camat])
                        </div>
                    </div>
                @endif

                <!-- Bendahara -->
                @if($bendahara)
                    <div class="bg-amber-50 rounded-lg p-5 border border-amber-100 hover:shadow-md transition duration-200">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span
                                    class="inline-block px-2 py-1 text-xs font-semibold tracking-wide text-amber-800 bg-amber-200 rounded-full mb-2">
                                    BENDAHARA
                                </span>
                                <h4 class="text-gray-900 font-bold text-lg">{{ $bendahara->nama }}</h4>
                                <p class="text-gray-500 text-sm">NIP. {{ $bendahara->nip }}</p>
                            </div>
                            @include('settings.partials.edit-button', ['pejabat' => $bendahara])
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Data PPTK Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-bedas-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    Data PPTK
                </h3>
                <button @click="showAddModal = true"
                    class="text-sm bg-bedas-600 hover:bg-bedas-700 text-white py-2 px-4 rounded-lg flex items-center gap-2 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah PPTK
                </button>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($pptks as $pptk)
                    <div
                        class="bg-bedas-50 rounded-lg p-5 border border-bedas-100 hover:shadow-md transition duration-200 relative group">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span
                                    class="inline-block px-2 py-1 text-xs font-semibold tracking-wide text-bedas-800 bg-bedas-200 rounded-full mb-2">
                                    PPTK
                                </span>
                                <h4 class="text-gray-900 font-bold text-lg">{{ $pptk->nama }}</h4>
                                <p class="text-gray-500 text-sm">NIP. {{ $pptk->nip }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                @include('settings.partials.edit-button', ['pejabat' => $pptk])

                                <!-- Delete Button for PPTK -->
                                <button type="button"
                                    @click="$dispatch('open-delete-modal', { action: '{{ route('pejabat.destroy', $pptk->id) }}', title: 'Hapus PPTK?', message: 'Data pejabat ini akan dihapus dari daftar PPTK.' })"
                                    class="text-red-400 hover:text-red-600 transition p-1" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-8 text-center text-gray-400 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <p>Belum ada data PPTK.</p>
                    </div>
                @endforelse
            </div>
        </div>


        <!-- Add PPTK Modal -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showAddModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-md z-50 overflow-hidden transform transition-all">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-800">Tambah PPTK Baru</h3>
                        <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <form action="{{ route('pejabat.store') }}" method="POST" class="p-6">
                        @csrf
                        <input type="hidden" name="jabatan" value="PPTK">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama"
                                class="w-full rounded-lg border-gray-300 focus:ring-bedas-500 focus:border-bedas-500"
                                required placeholder="Gelar dan Nama">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                            <input type="text" name="nip"
                                class="w-full rounded-lg border-gray-300 focus:ring-bedas-500 focus:border-bedas-500"
                                required placeholder="NIP tanpa spasi">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="showAddModal = false"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit"
                                class="px-4 py-2 bg-bedas-600 text-white rounded-lg hover:bg-bedas-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection