@extends('layouts.app')

@section('title', 'Websites Inventory')

@section('content')
<div class="space-y-6">

  <!-- Header Section with Actions -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-globe"></i>
        </div>
        <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Websites Inventory (Vendor Sites)</h2>
      </div>
      <p class="text-xs text-slate-500 mt-1">Manage vendor websites, SEO metrics (DA, DR, Organic Traffic), and cost pricing.</p>
    </div>

    <div class="flex items-center gap-3">
      <!-- Search Input in Websites Section -->
      <div class="relative w-64">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-xs text-slate-400"></i>
        <input 
          type="text" 
          id="websiteSearchInput" 
          onkeyup="filterWebsitesTable()" 
          placeholder="Filter by domain, niche, vendor..." 
          class="w-full bg-white border border-slate-200/90 rounded-xl pl-9 pr-3.5 py-2 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:border-indigo-500 shadow-sm transition"
        />
      </div>

      <!-- Add New Website Button -->
      <button 
        type="button" 
        onclick="openCreateWebsiteModal()" 
        class="bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-600/25 rounded-xl px-4 py-2 text-xs font-bold flex items-center gap-2 transition"
      >
        <i class="fa-solid fa-plus text-[10px]"></i>
        <span>Add New Website</span>
      </button>
    </div>
  </div>

  <!-- Website Section KPI Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
    
    <!-- Total Websites -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] flex items-center justify-between">
      <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-emerald-500 text-white shadow-md shadow-emerald-500/20 flex items-center justify-center text-base">
          <i class="fa-solid fa-globe"></i>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-500">Total Sites Available</p>
          <h3 class="text-xl font-black text-slate-800 mt-0.5 font-mono">{{ count($websites ?? []) }}</h3>
        </div>
      </div>
      <span class="inline-flex items-center text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100">
        Live Inventory
      </span>
    </div>

    <!-- High DR (>50) Sites -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] flex items-center justify-between">
      <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-indigo-500 text-white shadow-md shadow-indigo-500/20 flex items-center justify-center text-base">
          <i class="fa-solid fa-ranking-star"></i>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-500">High Authority (DR 60+)</p>
          <h3 class="text-xl font-black text-slate-800 mt-0.5 font-mono">
            {{ count(array_filter($websites ?? [], fn($w) => ($w['domain_rating'] ?? 0) >= 60)) }}
          </h3>
        </div>
      </div>
      <span class="inline-flex items-center text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg border border-indigo-100">
        Premium Sites
      </span>
    </div>

    <!-- Average Vendor Cost -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] flex items-center justify-between">
      <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-amber-500 text-white shadow-md shadow-amber-500/20 flex items-center justify-center text-base">
          <i class="fa-solid fa-dollar-sign"></i>
        </div>
        <div>
          <p class="text-xs font-medium text-slate-500">Avg. Vendor Cost</p>
          <h3 class="text-xl font-black text-slate-800 mt-0.5 font-mono">
            @php
              $prices = array_column($websites ?? [], 'vendor_price');
              $avg = count($prices) > 0 ? array_sum($prices) / count($prices) : 0;
            @endphp
            ${{ number_format($avg, 0) }}
          </h3>
        </div>
      </div>
      <span class="inline-flex items-center text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg border border-amber-100">
        Base Cost
      </span>
    </div>

  </div>

  <!-- Dedicated Websites Full Table -->
  <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] p-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-50">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-list-check text-emerald-600 text-xs"></i>
        <h3 class="text-sm font-bold text-slate-800">Available Guest Post Websites Inventory</h3>
      </div>
      <p class="text-xs text-slate-400 font-mono">Total: {{ count($websites ?? []) }} websites</p>
    </div>

    <div class="overflow-x-auto mt-2">
      <table class="min-w-full text-left text-xs divide-y divide-slate-100" id="websitesTable">
        <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
          <tr>
            <th class="py-3.5 pr-4"># ID</th>
            <th class="py-3.5 px-4">Website & Domain</th>
            <th class="py-3.5 px-4">Vendor / Seller</th>
            <th class="py-3.5 px-4">Niche</th>
            <th class="py-3.5 px-4">Metrics (DA / DR)</th>
            <th class="py-3.5 px-4">Monthly Traffic</th>
            <th class="py-3.5 px-4">Vendor Cost</th>
            <th class="py-3.5 px-4">TAT</th>
            <th class="py-3.5 px-4">Status</th>
            <th class="py-3.5 pl-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 text-slate-600" id="websitesTableBody">
          @forelse($websites ?? [] as $website)
            <tr class="hover:bg-slate-50/70 transition website-row">
              <td class="py-3.5 pr-4 font-mono font-bold text-emerald-600">#WB-{{ $website['id'] ?? '0' }}</td>
              
              <!-- Website Name & URL -->
              <td class="py-3.5 px-4">
                <div>
                  <h4 class="font-bold text-slate-800 website-name">{{ $website['website_name'] ?? 'Website' }}</h4>
                  <a href="{{ $website['website_url'] ?? '#' }}" target="_blank" class="text-[11px] font-mono text-indigo-600 hover:text-indigo-800 flex items-center gap-1 mt-0.5 website-url">
                    <span>{{ parse_url($website['website_url'] ?? '', PHP_URL_HOST) ?: $website['website_url'] }}</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                  </a>
                </div>
              </td>

              <!-- Vendor / Seller -->
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded-lg bg-purple-100 text-purple-700 font-bold flex items-center justify-center text-[10px]">
                    {{ substr($website['vendor']['name'] ?? 'VD', 0, 2) }}
                  </div>
                  <div>
                    <span class="font-bold text-slate-800 website-vendor">{{ $website['vendor']['name'] ?? 'Vendor #' . ($website['vendor_id'] ?? '') }}</span>
                    <p class="text-[10px] text-slate-400">{{ $website['vendor']['company_name'] ?? 'Independent' }}</p>
                  </div>
                </div>
              </td>

              <!-- Niche -->
              <td class="py-3.5 px-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 website-niche">
                  {{ $website['niche'] ?? 'General' }}
                </span>
              </td>

              <!-- SEO Metrics (DA / DR) -->
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-1.5 font-mono">
                  <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-bold text-[10px] border border-blue-100">
                    DA {{ $website['domain_authority'] ?? 'N/A' }}
                  </span>
                  <span class="px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold text-[10px] border border-indigo-100">
                    DR {{ $website['domain_rating'] ?? 'N/A' }}
                  </span>
                </div>
              </td>

              <!-- Monthly Traffic -->
              <td class="py-3.5 px-4 font-mono font-bold text-slate-700">
                @if(!empty($website['traffic']))
                  {{ $website['traffic'] >= 1000000 ? number_format($website['traffic'] / 1000000, 1) . 'M' : ($website['traffic'] >= 1000 ? number_format($website['traffic'] / 1000, 0) . 'K' : $website['traffic']) }}
                  <span class="text-[10px] font-normal text-slate-400">/mo</span>
                @else
                  <span class="text-slate-400">-</span>
                @endif
              </td>

              <!-- Vendor Price -->
              <td class="py-3.5 px-4 font-mono font-black text-emerald-600 text-xs">
                ${{ number_format($website['vendor_price'] ?? 0, 2) }}
              </td>

              <!-- TAT (Turnaround days) -->
              <td class="py-3.5 px-4 text-slate-600 font-medium">
                {{ !empty($website['turnaround_days']) ? $website['turnaround_days'] . ' Days' : '-' }}
              </td>

              <!-- Status -->
              <td class="py-3.5 px-4">
                @if(($website['status'] ?? 'active') === 'active')
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                    Active
                  </span>
                @else
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100">
                    {{ ucfirst($website['status'] ?? 'Paused') }}
                  </span>
                @endif
              </td>

              <!-- Actions -->
              <td class="py-3.5 pl-4 text-right whitespace-nowrap">
                <button 
                  type="button" 
                  onclick='openEditWebsiteModal(@json($website))' 
                  class="text-indigo-600 hover:text-indigo-800 font-bold mr-3 inline-flex items-center gap-1 transition"
                >
                  <i class="fa-regular fa-pen-to-square text-[11px]"></i> Edit
                </button>
                <button 
                  type="button" 
                  onclick="deleteWebsite({{ $website['id'] }}, '{{ addslashes($website['website_name']) }}')" 
                  class="text-rose-500 hover:text-rose-700 font-bold inline-flex items-center gap-1 transition"
                >
                  <i class="fa-regular fa-trash-can text-[11px]"></i> Delete
                </button>
              </td>
            </tr>
          @empty
            <tr id="emptyWebsiteRow">
              <td colspan="10" class="py-12 text-center text-slate-400">
                <div class="flex flex-col items-center justify-center">
                  <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-globe"></i>
                  </div>
                  <h4 class="font-bold text-slate-700 text-sm">No websites registered in inventory</h4>
                  <p class="text-xs text-slate-400 mt-1">Add available websites from your sellers to track guest post options.</p>
                  <button onclick="openCreateWebsiteModal()" class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                    + Add New Website
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
  function filterWebsitesTable() {
    const query = document.getElementById('websiteSearchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.website-row');

    rows.forEach(row => {
      const name = row.querySelector('.website-name')?.textContent.toLowerCase() || '';
      const url = row.querySelector('.website-url')?.textContent.toLowerCase() || '';
      const vendor = row.querySelector('.website-vendor')?.textContent.toLowerCase() || '';
      const niche = row.querySelector('.website-niche')?.textContent.toLowerCase() || '';

      if (name.includes(query) || url.includes(query) || vendor.includes(query) || niche.includes(query)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }
</script>
@endsection
