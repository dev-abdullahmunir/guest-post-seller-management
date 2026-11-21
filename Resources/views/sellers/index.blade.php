@extends('layouts.app')

@section('title', 'Sellers Management')

@section('content')
<div class="space-y-6">

  <!-- Header Section with Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-handshake"></i>
        </div>
        <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Sellers Directory (Vendors CRM)</h2>
      </div>
      <p class="text-xs text-slate-500 mt-1">Manage guest post sellers, webmasters, publisher agencies, and contact details.</p>
    </div>

    <div class="flex items-center gap-3">
      <!-- Search Input in Sellers Section -->
      <div class="relative w-64">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-xs text-slate-400"></i>
        <input 
          type="text" 
          id="sellerSearchInput" 
          onkeyup="filterSellersTable()" 
          placeholder="Filter sellers by name, company..." 
          class="w-full bg-white border border-slate-200/90 rounded-xl pl-9 pr-3.5 py-2 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:border-indigo-500 shadow-sm transition"
        />
      </div>

      <!-- Add New Seller Button -->
      <button 
        type="button" 
        onclick="openCreateSellerModal()" 
        class="bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-600/25 rounded-xl px-4 py-2 text-xs font-bold flex items-center gap-2 transition"
      >
        <i class="fa-solid fa-plus text-[10px]"></i>
        <span>Add New Seller</span>
      </button>
    </div>
  </div>

  <!-- Seller Section KPI Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
    
    <!-- Total Active Sellers -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] flex items-center justify-between">
      <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-purple-500 text-white shadow-md shadow-purple-500/20 flex items-center justify-center text-base">
          <i class="fa-solid fa-handshake"></i>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-500">Total Sellers / Vendors</p>
          <h3 class="text-xl font-black text-slate-800 mt-0.5 font-mono">{{ count($sellers ?? []) }}</h3>
        </div>
      </div>
      <span class="inline-flex items-center text-[11px] font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-lg border border-purple-100">
        Active Network
      </span>
    </div>

    <!-- Publisher Companies -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] flex items-center justify-between">
      <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-indigo-500 text-white shadow-md shadow-indigo-500/20 flex items-center justify-center text-base">
          <i class="fa-solid fa-building"></i>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-500">Agencies & Publishers</p>
          <h3 class="text-xl font-black text-slate-800 mt-0.5 font-mono">
            {{ count(array_filter($sellers ?? [], fn($s) => !empty($s['company_name']))) }}
          </h3>
        </div>
      </div>
      <span class="inline-flex items-center text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">
        Verified
      </span>
    </div>

    <!-- Global Countries -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] flex items-center justify-between">
      <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-emerald-500 text-white shadow-md shadow-emerald-500/20 flex items-center justify-center text-base">
          <i class="fa-solid fa-earth-americas"></i>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-500">Global Locations</p>
          <h3 class="text-xl font-black text-slate-800 mt-0.5 font-mono">
            {{ count(array_unique(array_filter(array_column($sellers ?? [], 'country')))) ?: 'Global' }}
          </h3>
        </div>
      </div>
      <span class="inline-flex items-center text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100">
        Worldwide
      </span>
    </div>

  </div>

  <!-- Dedicated Sellers Full Table -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] p-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-50">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-list-check text-purple-600 text-xs"></i>
        <h3 class="text-sm font-bold text-slate-800">All Registered Sellers (Webmasters)</h3>
      </div>
      <p class="text-xs text-slate-400 font-mono">Total: {{ count($sellers ?? []) }} vendors</p>
    </div>

    <div class="overflow-x-auto mt-2">
      <table class="min-w-full text-left text-xs divide-y divide-slate-100" id="sellersTable">
        <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
          <tr>
            <th class="py-3.5 pr-4"># ID</th>
            <th class="py-3.5 px-4">Seller Name</th>
            <th class="py-3.5 px-4">Company / Publication</th>
            <th class="py-3.5 px-4">Email</th>
            <th class="py-3.5 px-4">Phone</th>
            <th class="py-3.5 px-4">Country</th>
            <th class="py-3.5 px-4">Notes</th>
            <th class="py-3.5 pl-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 text-slate-600" id="sellersTableBody">
          @forelse($sellers ?? [] as $seller)
            <tr class="hover:bg-slate-50/70 transition seller-row">
              <td class="py-3.5 pr-4 font-mono font-bold text-purple-600">#SL-{{ $seller['id'] ?? '0' }}</td>
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-2.5">
                  <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 font-bold flex items-center justify-center text-xs">
                    {{ substr($seller['name'] ?? 'SL', 0, 2) }}
                  </div>
                  <div>
                    <h4 class="font-bold text-slate-800 seller-name">{{ $seller['name'] ?? 'Seller' }}</h4>
                  </div>
                </div>
              </td>
              <td class="py-3.5 px-4 font-medium text-slate-800 seller-company">{{ $seller['company_name'] ?? '-' }}</td>
              <td class="py-3.5 px-4 font-mono text-slate-600 seller-email">{{ $seller['email'] ?? '-' }}</td>
              <td class="py-3.5 px-4 font-mono text-slate-500">{{ $seller['phone'] ?? '-' }}</td>
              <td class="py-3.5 px-4 text-slate-600 font-medium">{{ $seller['country'] ?? '-' }}</td>
              <td class="py-3.5 px-4 text-slate-400 truncate max-w-[220px]" title="{{ $seller['notes'] ?? '' }}">
                {{ $seller['notes'] ?? '-' }}
              </td>
              <td class="py-3.5 pl-4 text-right whitespace-nowrap">
                <button 
                  type="button" 
                  onclick='openEditSellerModal(@json($seller))' 
                  class="text-indigo-600 hover:text-indigo-800 font-bold mr-3 inline-flex items-center gap-1 transition"
                >
                  <i class="fa-regular fa-pen-to-square text-[11px]"></i> Edit
                </button>
                <button 
                  type="button" 
                  onclick="deleteSeller({{ $seller['id'] }}, '{{ addslashes($seller['name']) }}')" 
                  class="text-rose-500 hover:text-rose-700 font-bold inline-flex items-center gap-1 transition"
                >
                  <i class="fa-regular fa-trash-can text-[11px]"></i> Delete
                </button>
              </td>
            </tr>
          @empty
            <tr id="emptySellerRow">
              <td colspan="8" class="py-12 text-center text-slate-400">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-handshake"></i>
                  </div>
                  <h4 class="font-bold text-slate-700 text-sm">No sellers registered yet</h4>
                  <p class="text-xs text-slate-400 mt-1">Add your first guest post vendor or webmaster contact.</p>
                  <button onclick="openCreateSellerModal()" class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                    + Add New Seller
                  </button>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
  function filterSellersTable() {
    const query = document.getElementById('sellerSearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.seller-row');

    rows.forEach(row => {
      const name = row.querySelector('.seller-name')?.textContent.toLowerCase() || '';
      const email = row.querySelector('.seller-email')?.textContent.toLowerCase() || '';
      const company = row.querySelector('.seller-company')?.textContent.toLowerCase() || '';

      if (name.includes(query) || email.includes(query) || company.includes(query)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }
</script>
@endsection
