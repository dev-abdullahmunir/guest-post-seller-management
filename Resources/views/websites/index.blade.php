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

      <!-- Bulk Import Websites (Excel/CSV) Button -->
      <button 
        type="button" 
        onclick="openBulkImportModal()" 
        class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/90 shadow-xs rounded-xl px-4 py-2 text-xs font-bold flex items-center gap-2 transition hover:border-slate-300"
      >
        <i class="fa-solid fa-file-excel text-emerald-600 text-sm"></i>
        <span>Bulk Import (Excel/CSV)</span>
      </button>

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

<!-- Bulk Import Websites Modal (Smart Approach 1) -->
  <div id="bulkImportModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-200">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all scale-95 duration-200 max-h-[90vh] flex flex-col" id="bulkImportModalCard">
      
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/25">
            <i class="fa-solid fa-file-excel text-base"></i>
          </div>
          <div>
            <h3 class="text-base font-black text-slate-800">Bulk Import Websites Inventory</h3>
            <p class="text-xs text-slate-500">Upload vendor spreadsheets (.xlsx, .csv) with smart column matching and live preview.</p>
          </div>
        </div>
        <button type="button" onclick="closeBulkImportModal()" class="w-8 h-8 rounded-xl bg-slate-100 text-slate-400 hover:text-slate-600 hover:bg-slate-200/70 flex items-center justify-center transition">
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <div class="p-6 overflow-y-auto space-y-6 flex-1">
        
        <!-- Step 1: Configuration (Vendor & Duplicate Policy) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-100">
          
          <!-- Select Vendor -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">Assign Vendor / Seller <span class="text-rose-500">*</span></label>
            <select id="bulkImportVendorId" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 font-semibold focus:outline-none focus:border-indigo-600 transition shadow-2xs">
              @foreach($sellers as $seller)
                <option value="{{ $seller['id'] }}">{{ $seller['name'] }} ({{ $seller['company_name'] ?: 'Independent Publisher' }})</option>
              @endforeach
            </select>
            <p class="text-[10px] text-slate-400">All imported sites will be linked to this vendor unless specified in the sheet.</p>
          </div>

          <!-- Duplicate Handling Strategy -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">Duplicate Handling Strategy</label>
            <div class="space-y-1.5 pt-1">
              <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="radio" name="duplicate_action" value="update" checked class="text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5" />
                <span class="text-xs font-semibold text-slate-700">Update Existing (Refresh metrics & cost price)</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="radio" name="duplicate_action" value="skip" class="text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5" />
                <span class="text-xs font-semibold text-slate-700">Skip Duplicates (Only import brand new websites)</span>
              </label>
            </div>
          </div>

        </div>

        <!-- Step 2: Drag & Drop Upload Zone -->
        <div 
          id="bulkDropzone"
          onclick="document.getElementById('bulkExcelFileInput').click()"
          ondragover="handleBulkDragOver(event)"
          ondragleave="handleBulkDragLeave(event)"
          ondrop="handleBulkDrop(event)"
          class="border-2 border-dashed border-slate-200 hover:border-emerald-500 bg-slate-50/50 hover:bg-emerald-50/30 rounded-3xl p-8 text-center cursor-pointer transition group"
        >
          <input 
            type="file" 
            id="bulkExcelFileInput" 
            accept=".csv, .xlsx, .xls" 
            class="hidden" 
            onchange="handleBulkFileSelect(event)"
          />
          <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center mx-auto text-2xl group-hover:scale-110 transition shadow-xs">
            <i class="fa-solid fa-cloud-arrow-up"></i>
          </div>
          <h4 class="text-sm font-black text-slate-800 mt-3 group-hover:text-emerald-700 transition">
            Choose Excel (.xlsx) or CSV file, or drag and drop
          </h4>
          <p class="text-xs text-slate-400 mt-1">Supports standard vendor files with 10 to 5,000+ websites at once.</p>
          
          <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-center gap-3 text-xs">
            <span class="text-slate-400 font-medium">Formats: <strong>.xlsx, .xls, .csv</strong></span>
            <span class="text-slate-200 hidden sm:inline">•</span>
            <a 
              href="/dummy_50_websites.csv" 
              download="dummy_50_websites.csv"
              onclick="event.stopPropagation()" 
              class="text-emerald-700 hover:text-emerald-900 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 font-bold inline-flex items-center gap-1.5 transition shadow-2xs hover:bg-emerald-100"
              title="Download 50 ready-to-test websites"
            >
              <i class="fa-solid fa-file-csv text-emerald-600"></i>
              <span>Download 50 Demo Sites (.csv)</span>
            </a>
            <span class="text-slate-200 hidden sm:inline">•</span>
            <button
              type="button"
              onclick="event.stopPropagation(); load50DemoWebsitesDirectly()"
              class="text-indigo-700 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-200 font-bold inline-flex items-center gap-1.5 transition shadow-2xs hover:bg-indigo-100"
              title="1-Click Load 50 Demo Websites"
            >
              <i class="fa-solid fa-bolt text-indigo-600"></i>
              <span>1-Click Load 50 Demo Sites</span>
            </button>
          </div>
        </div>

        <!-- Step 3: Smart Column Auto-Mapping & Live Preview (Hidden until file selected) -->
        <div id="bulkMappingSection" class="hidden space-y-5">
          
          <!-- File Details Banner -->
          <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2.5">
              <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
              <span class="font-extrabold text-slate-800" id="bulkFileNameDisplay">vendor_sites.xlsx</span>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-emerald-700 border border-emerald-200 shadow-2xs" id="bulkRowCountDisplay">
                0 Websites Detected
              </span>
            </div>
            <button 
              type="button" 
              onclick="resetBulkImportFile()" 
              class="text-rose-600 hover:text-rose-800 font-bold inline-flex items-center gap-1 transition"
            >
              <i class="fa-solid fa-arrow-rotate-left"></i> Change File
            </button>
          </div>

          <!-- Smart Column Mapping Selectors -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-sliders text-indigo-600"></i> Smart Column Matcher
              </h4>
              <span class="text-[11px] text-slate-400">System auto-detected matching columns. Verify or change below:</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
              <!-- Website URL -->
              <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-700">Website URL / Domain <span class="text-rose-500">*</span></label>
                <select id="map_website_url" onchange="renderBulkPreviewTable()" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs font-medium focus:outline-none focus:border-indigo-600 transition"></select>
              </div>

              <!-- Website Name -->
              <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-700">Website Name</label>
                <select id="map_website_name" onchange="renderBulkPreviewTable()" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs font-medium focus:outline-none focus:border-indigo-600 transition"></select>
              </div>

              <!-- Niche -->
              <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-700">Niche / Category</label>
                <select id="map_niche" onchange="renderBulkPreviewTable()" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs font-medium focus:outline-none focus:border-indigo-600 transition"></select>
              </div>

              <!-- DA -->
              <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-700">Domain Authority (DA)</label>
                <select id="map_domain_authority" onchange="renderBulkPreviewTable()" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs font-medium focus:outline-none focus:border-indigo-600 transition"></select>
              </div>

              <!-- DR -->
              <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-700">Domain Rating (DR)</label>
                <select id="map_domain_rating" onchange="renderBulkPreviewTable()" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs font-medium focus:outline-none focus:border-indigo-600 transition"></select>
              </div>

              <!-- Monthly Traffic -->
              <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-700">Monthly Traffic</label>
                <select id="map_traffic" onchange="renderBulkPreviewTable()" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs font-medium focus:outline-none focus:border-indigo-600 transition"></select>
              </div>

              <!-- Vendor Price -->
              <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-700">Cost Price ($)</label>
                <select id="map_vendor_price" onchange="renderBulkPreviewTable()" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs font-medium focus:outline-none focus:border-indigo-600 transition"></select>
              </div>

              <!-- TAT -->
              <div class="space-y-1">
                <label class="block text-[11px] font-bold text-slate-700">Turnaround (Days)</label>
                <select id="map_turnaround_days" onchange="renderBulkPreviewTable()" class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs font-medium focus:outline-none focus:border-indigo-600 transition"></select>
              </div>
            </div>
          </div>

          <!-- Live Preview Table (All Uploaded Rows with Scroll & Sticky Header) -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-table text-emerald-600"></i> Live Data Preview
              </h4>
              <span id="bulkPreviewCountBadge" class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200"></span>
            </div>
            <div class="max-h-80 overflow-y-auto overflow-x-auto rounded-xl border border-slate-200/90 shadow-2xs custom-scroll">
              <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200 text-[11px] sticky top-0 z-10 shadow-2xs">
                  <tr>
                    <th class="py-2.5 px-3 bg-slate-50">#</th>
                    <th class="py-2.5 px-3 bg-slate-50">Website / Domain</th>
                    <th class="py-2.5 px-3 bg-slate-50">Name</th>
                    <th class="py-2.5 px-3 bg-slate-50">Niche</th>
                    <th class="py-2.5 px-3 bg-slate-50">DA</th>
                    <th class="py-2.5 px-3 bg-slate-50">DR</th>
                    <th class="py-2.5 px-3 bg-slate-50">Traffic</th>
                    <th class="py-2.5 px-3 bg-slate-50">Cost Price</th>
                    <th class="py-2.5 px-3 bg-slate-50">TAT</th>
                  </tr>
                </thead>
                <tbody id="bulkPreviewTableBody" class="divide-y divide-slate-100 font-medium text-slate-700 bg-white">
                  <!-- Populated by JS -->
                </tbody>
              </table>
            </div>
          </div>

        </div>

        <!-- Step 4: Progress Bar & Result Alert (During & After Import) -->
        <div id="bulkProgressSection" class="hidden space-y-4">
          <div class="space-y-1.5">
            <div class="flex items-center justify-between text-xs font-bold">
              <span id="bulkProgressStatusText" class="text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-spinner fa-spin text-indigo-600"></i> Processing Import...
              </span>
              <span id="bulkProgressPercent" class="text-indigo-600 font-mono">100%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
              <div id="bulkProgressBar" class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300 w-full"></div>
            </div>
          </div>

          <!-- Result Summary Box -->
          <div id="bulkResultSummaryBox" class="hidden p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs space-y-3">
            <div class="flex items-center gap-2.5">
              <div class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                <i class="fa-solid fa-check"></i>
              </div>
              <div>
                <h4 class="font-black text-slate-800 text-sm" id="bulkResultTitle">Import Completed Successfully!</h4>
                <p class="text-slate-600" id="bulkResultMessage">Websites have been parsed and loaded into inventory.</p>
              </div>
            </div>

            <!-- Stats Counters Grid -->
            <div class="grid grid-cols-3 gap-2.5 pt-1 text-center">
              <div class="p-2.5 rounded-xl bg-white border border-emerald-100 shadow-2xs">
                <p class="text-[10px] font-bold uppercase text-slate-400">Newly Added</p>
                <h5 class="text-base font-black text-emerald-600 font-mono" id="bulkStatAdded">0</h5>
              </div>
              <div class="p-2.5 rounded-xl bg-white border border-emerald-100 shadow-2xs">
                <p class="text-[10px] font-bold uppercase text-slate-400">Updated</p>
                <h5 class="text-base font-black text-indigo-600 font-mono" id="bulkStatUpdated">0</h5>
              </div>
              <div class="p-2.5 rounded-xl bg-white border border-emerald-100 shadow-2xs">
                <p class="text-[10px] font-bold uppercase text-slate-400">Skipped</p>
                <h5 class="text-base font-black text-slate-500 font-mono" id="bulkStatSkipped">0</h5>
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50">
        <button 
          type="button" 
          onclick="closeBulkImportModal()" 
          class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition"
        >
          Cancel
        </button>

        <button 
          type="button" 
          id="startBulkImportBtn" 
          onclick="executeBulkImport()" 
          disabled
          class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-200 disabled:text-slate-400 text-white text-xs font-bold flex items-center gap-2 shadow-md shadow-emerald-600/20 transition disabled:shadow-none cursor-not-allowed disabled:cursor-not-allowed"
        >
          <i class="fa-solid fa-cloud-arrow-up"></i>
          <span id="startBulkImportBtnText">Start Bulk Import</span>
        </button>
      </div>

    </div>
  </div>

<script>
  let rawImportRows = [];
  let detectedHeaders = [];

  function openBulkImportModal() {
    const modal = document.getElementById('bulkImportModal');
    const card = document.getElementById('bulkImportModalCard');
    resetBulkImportFile();
    modal.classList.remove('hidden');
    setTimeout(() => card.classList.remove('scale-95'), 10);
  }

  function closeBulkImportModal() {
    const modal = document.getElementById('bulkImportModal');
    const card = document.getElementById('bulkImportModalCard');
    card.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 150);
  }

  function handleBulkDragOver(e) {
    e.preventDefault();
    document.getElementById('bulkDropzone').classList.add('border-emerald-500', 'bg-emerald-50/30');
  }

  function handleBulkDragLeave(e) {
    e.preventDefault();
    document.getElementById('bulkDropzone').classList.remove('border-emerald-500', 'bg-emerald-50/30');
  }

  function handleBulkDrop(e) {
    e.preventDefault();
    handleBulkDragLeave(e);
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      processBulkSpreadsheetFile(e.dataTransfer.files[0]);
    }
  }

  function handleBulkFileSelect(e) {
    if (e.target.files && e.target.files[0]) {
      processBulkSpreadsheetFile(e.target.files[0]);
    }
  }

  function resetBulkImportFile() {
    rawImportRows = [];
    detectedHeaders = [];
    document.getElementById('bulkExcelFileInput').value = '';
    document.getElementById('bulkDropzone').classList.remove('hidden');
    document.getElementById('bulkMappingSection').classList.add('hidden');
    document.getElementById('bulkProgressSection').classList.add('hidden');
    document.getElementById('bulkResultSummaryBox').classList.add('hidden');
    const btn = document.getElementById('startBulkImportBtn');
    btn.disabled = true;
    btn.classList.add('cursor-not-allowed');
    btn.classList.remove('cursor-pointer');
    document.getElementById('startBulkImportBtnText').textContent = 'Start Bulk Import';
  }

  async function load50DemoWebsitesDirectly() {
    try {
      const response = await fetch('/dummy_50_websites.csv');
      const csvText = await response.text();
      const workbook = XLSX.read(csvText, { type: 'string' });
      const firstSheetName = workbook.SheetNames[0];
      const worksheet = workbook.Sheets[firstSheetName];
      const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: '' });

      if (!jsonData || jsonData.length === 0) {
        alert('Could not load demo websites.');
        return;
      }

      rawImportRows = jsonData;
      detectedHeaders = Object.keys(jsonData[0] || {});

      document.getElementById('bulkFileNameDisplay').textContent = 'dummy_50_websites.csv (Pre-Loaded)';
      document.getElementById('bulkRowCountDisplay').textContent = `${jsonData.length} Websites Detected`;

      setupSmartColumnMapping(detectedHeaders);
      renderBulkPreviewTable();

      document.getElementById('bulkDropzone').classList.add('hidden');
      document.getElementById('bulkMappingSection').classList.remove('hidden');

      const btn = document.getElementById('startBulkImportBtn');
      btn.disabled = false;
      btn.classList.remove('cursor-not-allowed');
      btn.classList.add('cursor-pointer');
      document.getElementById('startBulkImportBtnText').textContent = `Import ${jsonData.length} Websites`;
    } catch (err) {
      alert('Error loading demo websites: ' + err.message);
    }
  }

  function processBulkSpreadsheetFile(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      try {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const firstSheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheetName];
        
        // Convert to array of objects
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: '' });
        
        if (!jsonData || jsonData.length === 0) {
          alert('Uploaded file is empty or could not be read.');
          return;
        }

        rawImportRows = jsonData;
        detectedHeaders = Object.keys(jsonData[0] || {});

        // Display File Info
        document.getElementById('bulkFileNameDisplay').textContent = file.name;
        document.getElementById('bulkRowCountDisplay').textContent = `${jsonData.length} Websites Detected`;

        // Setup Column Mapping Selectors
        setupSmartColumnMapping(detectedHeaders);

        // Render Preview Table
        renderBulkPreviewTable();

        // Switch to Mapping Step
        document.getElementById('bulkDropzone').classList.add('hidden');
        document.getElementById('bulkMappingSection').classList.remove('hidden');
        
        const btn = document.getElementById('startBulkImportBtn');
        btn.disabled = false;
        btn.classList.remove('cursor-not-allowed');
        btn.classList.add('cursor-pointer');
        document.getElementById('startBulkImportBtnText').textContent = `Import ${jsonData.length} Websites`;

      } catch (err) {
        console.error(err);
        alert('Error parsing spreadsheet file: ' + err.message);
      }
    };
    reader.readAsArrayBuffer(file);
  }

  // Keywords for smart auto-matching
  const columnKeywords = {
    website_url: ['url', 'domain', 'website', 'site', 'link', 'web', 'address'],
    website_name: ['name', 'title', 'blog', 'publisher', 'site_name'],
    niche: ['niche', 'category', 'topic', 'industry', 'vertical', 'genre'],
    domain_authority: ['da', 'moz', 'authority'],
    domain_rating: ['dr', 'ahrefs', 'rating'],
    traffic: ['traffic', 'visitors', 'monthly', 'visits', 'volume'],
    vendor_price: ['price', 'cost', 'rate', 'fee', 'charge', 'vendor'],
    turnaround_days: ['tat', 'turnaround', 'days', 'time', 'delivery']
  };

  function setupSmartColumnMapping(headers) {
    const fields = [
      'website_url', 'website_name', 'niche', 
      'domain_authority', 'domain_rating', 'traffic', 
      'vendor_price', 'turnaround_days'
    ];

    fields.forEach(field => {
      const select = document.getElementById(`map_${field}`);
      if (!select) return;

      select.innerHTML = '<option value="">-- Ignore Field --</option>';
      
      let bestMatch = '';

      headers.forEach(h => {
        const option = document.createElement('option');
        option.value = h;
        option.textContent = h;
        select.appendChild(option);

        const cleanH = h.toLowerCase().trim().replace(/[^a-z0-9]/g, '');
        const keywords = columnKeywords[field] || [];
        
        for (const kw of keywords) {
          if (cleanH === kw || cleanH.includes(kw)) {
            bestMatch = h;
            break;
          }
        }
      });

      if (bestMatch) {
        select.value = bestMatch;
      }
    });
  }

  function renderBulkPreviewTable() {
    const tbody = document.getElementById('bulkPreviewTableBody');
    tbody.innerHTML = '';

    const count = rawImportRows.length;
    const badge = document.getElementById('bulkPreviewCountBadge');
    if (badge) {
      badge.textContent = `Showing all ${count} websites`;
    }

    const mapUrl = document.getElementById('map_website_url').value;
    const mapName = document.getElementById('map_website_name').value;
    const mapNiche = document.getElementById('map_niche').value;
    const mapDA = document.getElementById('map_domain_authority').value;
    const mapDR = document.getElementById('map_domain_rating').value;
    const mapTraffic = document.getElementById('map_traffic').value;
    const mapPrice = document.getElementById('map_vendor_price').value;
    const mapTAT = document.getElementById('map_turnaround_days').value;

    for (let i = 0; i < count; i++) {
      const row = rawImportRows[i];
      const tr = document.createElement('tr');
      tr.className = 'hover:bg-slate-50/70 transition';

      const urlVal = row[mapUrl] || '-';
      const nameVal = row[mapName] || (urlVal !== '-' ? urlVal.replace(/^https?:\/\//i, '').replace(/^www\./i, '').split('.')[0] : '-');
      const nicheVal = row[mapNiche] || 'General';
      const daVal = row[mapDA] || '-';
      const drVal = row[mapDR] || '-';
      const trafficVal = row[mapTraffic] ? Number(row[mapTraffic]).toLocaleString() : '-';
      const priceVal = row[mapPrice] ? '$' + Number(row[mapPrice]).toFixed(2) : '$0.00';
      const tatVal = (row[mapTAT] || '3') + ' Days';

      tr.innerHTML = `
        <td class="py-2.5 px-3 text-slate-400 font-mono text-[11px]">${i + 1}</td>
        <td class="py-2.5 px-3 font-bold text-slate-900 truncate max-w-[150px]">${urlVal}</td>
        <td class="py-2.5 px-3 text-slate-700">${nameVal}</td>
        <td class="py-2.5 px-3 text-slate-600"><span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 font-bold">${nicheVal}</span></td>
        <td class="py-2.5 px-3 font-mono font-bold text-indigo-600">${daVal}</td>
        <td class="py-2.5 px-3 font-mono font-bold text-purple-600">${drVal}</td>
        <td class="py-2.5 px-3 font-mono text-slate-700">${trafficVal}</td>
        <td class="py-2.5 px-3 font-mono font-bold text-emerald-600">${priceVal}</td>
        <td class="py-2.5 px-3 text-slate-500">${tatVal}</td>
      `;
      tbody.appendChild(tr);
    }
  }

  async function executeBulkImport() {
    const mapUrl = document.getElementById('map_website_url').value;
    if (!mapUrl) {
      alert('Please map the Website URL / Domain column.');
      return;
    }

    const vendorId = document.getElementById('bulkImportVendorId').value;
    const duplicateAction = document.querySelector('input[name="duplicate_action"]:checked')?.value || 'skip';

    const mapName = document.getElementById('map_website_name').value;
    const mapNiche = document.getElementById('map_niche').value;
    const mapDA = document.getElementById('map_domain_authority').value;
    const mapDR = document.getElementById('map_domain_rating').value;
    const mapTraffic = document.getElementById('map_traffic').value;
    const mapPrice = document.getElementById('map_vendor_price').value;
    const mapTAT = document.getElementById('map_turnaround_days').value;

    const payloadWebsites = rawImportRows.map(row => {
      return {
        website_url: row[mapUrl],
        website_name: row[mapName] || '',
        niche: row[mapNiche] || 'General',
        domain_authority: row[mapDA] || null,
        domain_rating: row[mapDR] || null,
        traffic: row[mapTraffic] || null,
        vendor_price: row[mapPrice] || 0,
        turnaround_days: row[mapTAT] || 3,
        vendor_id: vendorId,
      };
    });

    // UI Progress State
    document.getElementById('bulkMappingSection').classList.add('hidden');
    document.getElementById('bulkProgressSection').classList.remove('hidden');
    const submitBtn = document.getElementById('startBulkImportBtn');
    submitBtn.disabled = true;

    try {
      const response = await fetch('/api/websites/bulk-import', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          vendor_id: vendorId,
          duplicate_action: duplicateAction,
          websites: payloadWebsites
        })
      });

      const result = await response.json();

      if (response.ok && result.status === 'success') {
        document.getElementById('bulkProgressStatusText').innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600"></i> Import Completed!';
        document.getElementById('bulkResultSummaryBox').classList.remove('hidden');
        document.getElementById('bulkStatAdded').textContent = result.imported || 0;
        document.getElementById('bulkStatUpdated').textContent = result.updated || 0;
        document.getElementById('bulkStatSkipped').textContent = result.skipped || 0;
        document.getElementById('bulkResultMessage').textContent = result.message;

        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-rotate-right"></i> Refresh Inventory';
        submitBtn.onclick = () => window.location.reload();
      } else {
        alert(result.message || 'Error importing websites.');
        resetBulkImportFile();
      }
    } catch (err) {
      console.error(err);
      alert('Network error during bulk import.');
      resetBulkImportFile();
    }
  }

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
