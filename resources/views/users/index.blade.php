@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="space-y-6" x-data="userManagement()">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h2>
            <button @click="openCreateModal()" class="bg-bedas-600 hover:bg-bedas-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Pengguna
            </button>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button @click="show = false">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 flex justify-between items-center">
                <span>{{ session('error') }}</span>
                <button @click="show = false">&times;</button>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Relasi Pejabat</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 rounded text-xs font-semibold font-mono border
                                        {{ $user->role === 'admin' ? 'bg-red-50 text-red-600 border-red-200' : 
                                           ($user->role === 'pptk' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'bg-gray-100 text-gray-600') }}">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($user->role === 'pptk' && $user->pejabat)
                                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded border">NIP: {{ $user->pejabat->nip }}</span>
                                        <br/>{{ $user->pejabat->nama }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="openEditModal({{ json_encode($user) }})" class="text-amber-500 hover:text-amber-700 transition" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        @if(auth()->id() !== $user->id)
                                        <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('users.destroy', $user->id) }}', title: 'Hapus Pengguna', message: 'Data akun {{ $user->name }} akan dihapus secara permanen.' })" class="text-red-500 hover:text-red-700 transition" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">Belum ada data pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showModal = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md z-50 overflow-hidden transform transition-all">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-800" x-text="isEdit ? 'Edit Pengguna' : 'Tambah Pengguna'"></h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    <form method="POST" :action="formAction" class="p-6">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                                <input type="text" name="name" x-model="form.name" class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring-bedas-200" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" x-model="form.email" class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring-bedas-200" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" name="password" x-model="form.password" class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring-bedas-200" :required="!isEdit" placeholder="Kosongkan jika tidak ingin mengubah (saat Edit)">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role Kategori</label>
                                <select name="role" x-model="form.role" class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring-bedas-200" required>
                                    <option value="admin">Admin</option>
                                    <option value="pptk">PPTK</option>
                                    <option value="camat">Camat</option>
                                </select>
                            </div>
                            
                            <div x-show="form.role === 'pptk'" x-transition>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pejabat (PPTK) Terkait</label>
                                <select name="pejabat_id" x-model="form.pejabat_id" class="w-full rounded-lg border-gray-300 focus:border-bedas-500 focus:ring-bedas-200" :required="form.role === 'pptk'">
                                    <option value="">-- Pilih PPTK --</option>
                                    @foreach($pejabats->where('jabatan', 'PPTK') as $pejabat)
                                        <option value="{{ $pejabat->id }}">{{ $pejabat->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showModal = false" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Batal</button>
                            <button type="submit" class="px-6 py-2 bg-bedas-600 text-white font-bold rounded-lg hover:bg-bedas-700" x-text="isEdit ? 'Update' : 'Simpan'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function userManagement() {
            return {
                showModal: false,
                isEdit: false,
                form: { id: '', name: '', email: '', password: '', role: 'admin', pejabat_id: '' },
                get formAction() {
                    return this.isEdit ? `{{ url('users') }}/${this.form.id}` : `{{ route('users.store') }}`;
                },
                openCreateModal() {
                    this.isEdit = false;
                    this.form = { id: '', name: '', email: '', password: '', role: 'admin', pejabat_id: '' };
                    this.showModal = true;
                },
                openEditModal(user) {
                    this.isEdit = true;
                    this.form = {
                        id: user.id,
                        name: user.name,
                        email: user.email,
                        password: '',
                        role: user.role,
                        pejabat_id: user.pejabat_id || ''
                    };
                    this.showModal = true;
                }
            }
        }
    </script>
@endsection
