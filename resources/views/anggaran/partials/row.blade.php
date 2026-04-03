@php 
                                $level = $level ?? 0;
    $indent = $level * 1.5;
    $hasChildren = $item->children->count() > 0;
    $realisasi = $item->getRealisasi();
@endphp

<tr id="row-{{ $item->id }}" data-parent-id="{{ $item->parent_id }}"
    class="{{ $level > 3 ? 'bg-green-50/50' : '' }} hover:bg-gray-100 transition duration-150 border-b border-gray-100">
    <td class="px-4 py-3 text-center">
        {{-- Icons removed as per user request --}}
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm font-mono {{ $level < 3 ? 'font-bold' : '' }}"
        style="padding-left: {{ 1.5 + $indent }}rem">
        {{ $item->kode }}
    </td>
    <td
        class="px-4 py-3 text-sm {{ $level < 3 ? 'font-bold uppercase' : ($hasChildren ? 'font-medium' : 'italic text-gray-600') }}">
        {{ $item->uraian }}
    </td>
    <td id="pagu-{{ $item->id }}" data-pagu="{{ $hasChildren ? $item->getPaguRecursive() : $item->pagu }}"
        class="px-4 py-3 whitespace-nowrap text-sm text-right {{ $level < 3 ? 'font-bold' : '' }}">
        @if(!$hasChildren)
            <div class="flex items-center justify-end">
                <span class="mr-1 text-gray-500">Rp</span>
                <input type="text" value="{{ number_format($item->pagu, 0, ',', '.') }}"
                    x-on:blur="updatePagu({{ $item->id }}, $event.target.value)"
                    x-on:input="$event.target.value = formatRupiah($event.target.value)"
                    class="w-32 text-right border-none bg-transparent hover:bg-white focus:bg-white focus:ring-1 focus:ring-bedas-500 rounded p-1 transition duration-200 font-semibold text-gray-800">
            </div>
        @else
            <span class="font-bold">Rp {{ number_format($item->getPaguRecursive(), 0, ',', '.') }}</span>
        @endif
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-gray-700">
        Rp {{ number_format($realisasi, 0, ',', '.') }}
    </td>
    <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
        <div class="flex items-center justify-center space-x-2">
            @if($level < 5) {{-- Arbitrary limit --}}
                <button @click="openCreateModal({{ $item->id }}, '{{ $item->kode }}.')"
                    class="text-green-500 hover:text-green-700" title="Tambah Sub">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </button>
            @endif
            <button @click="openEditModal({{ json_encode($item) }})" class="text-amber-500 hover:text-amber-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
            </button>
            <button type="button"
                @click="$dispatch('open-delete-modal', { action: '{{ route('anggaran.destroy', $item->id) }}', title: 'Hapus Item Anggaran?', message: 'Item ini dan semua turunannya akan dihapus secara permanen.' })"
                class="text-red-500 hover:text-red-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
            </button>
        </div>
    </td>
</tr>

@if($hasChildren)
    @foreach($item->children as $child)
        @include('anggaran.partials.row', ['item' => $child, 'level' => $level + 1])
    @endforeach
@endif