@extends('layouts.manager')
@section('title', $supplier->name)

@section('content')
@php($canManage = auth()->user()->canManage())
@include('manager.partials.breadcrumb', ['items' => [
    ['label' => 'Suppliers', 'url' => route('suppliers.index')],
    ['label' => $supplier->name],
]])

<div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-700 text-lg font-semibold flex items-center justify-center">
                {{ mb_strtoupper(mb_substr($supplier->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-semibold text-gray-900">{{ $supplier->name }}</h1>
                    @include('manager.partials.status-badge', ['status' => $supplier->status])
                </div>
                <div class="text-sm text-gray-500 mt-1">
                    @if ($supplier->code) Code: {{ $supplier->code }} &middot; @endif
                    ID #{{ $supplier->id }}
                </div>
            </div>
        </div>
        @if ($canManage)
        <div class="flex gap-2">
            <a href="{{ route('suppliers.edit', $supplier) }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Edit</a>
            @include('manager.partials.delete-confirm', [
                'action' => route('suppliers.destroy', $supplier),
                'title' => 'Delete supplier',
                'message' => 'Are you sure you want to delete "'.$supplier->name.'"? This will remove all its layups and cannot be undone.',
                'triggerClass' => 'px-3 py-2 bg-white border border-red-300 text-red-700 text-sm font-medium rounded-md hover:bg-red-50',
                'triggerLabel' => 'Delete',
            ])
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-100">
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Primary Contact</div>
            <div class="mt-1 text-sm text-gray-900">{{ $supplier->primary_contact ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Location</div>
            <div class="mt-1 text-sm text-gray-900">{{ $supplier->location ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Material Certifications</div>
            <div class="mt-1 text-sm text-gray-900">{{ $supplier->material_certifications ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Last Audit Date</div>
            <div class="mt-1 text-sm text-gray-900">{{ $supplier->last_audit_date?->format('M d, Y') ?? '—' }}</div>
        </div>
    </div>
</div>

<div
    class="bg-white rounded-lg border border-gray-200 shadow-sm"
    x-data="supplierImportModal({
        importUrl: '{{ url('/api/suppliers/'.$supplier->id.'/import') }}',
        csrf: '{{ csrf_token() }}',
    })"
>
    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
        <div>
            <h2 class="font-semibold text-gray-900">Associated Layups</h2>
            <p class="text-xs text-gray-500">{{ $supplier->layups_count }} total</p>
        </div>
        <div class="flex gap-2">
            @if ($canManage)
                <button type="button" @click="open = true"
                    class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                    Import
                </button>
            @endif
            <a href="{{ route('suppliers.download', $supplier) }}"
               class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Export
            </a>
            @if ($canManage)
                <a href="{{ route('layups.create', ['supplier_id' => $supplier->id]) }}" class="px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">Add Layup</a>
            @endif
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Spec Code</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Name</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Ply Count</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Layers</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Grade</th>
                    <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase text-xs tracking-wide">Status</th>
                    <th class="px-5 py-3 text-right font-medium text-gray-500 uppercase text-xs tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($layups as $layup)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-600">{{ $layup->specification_code ?? '—' }}</td>
                        <td class="px-5 py-3"><a href="{{ route('layups.show', $layup) }}" class="font-medium text-gray-900 hover:text-emerald-700">{{ $layup->name }}</a></td>
                        <td class="px-5 py-3 text-gray-600">{{ $layup->ply_count ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $layup->layers_count }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $layup->grade ?? '—' }}</td>
                        <td class="px-5 py-3">@include('manager.partials.status-badge', ['status' => $layup->status])</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('layups.show', $layup) }}" class="text-xs text-gray-600 hover:text-emerald-700 px-2 py-1">View</a>
                            @if ($canManage)
                                <a href="{{ route('layups.edit', $layup) }}" class="text-xs text-gray-600 hover:text-emerald-700 px-2 py-1">Edit</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-500">No layups yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Toast --}}
    <div x-show="toast.visible" x-cloak x-transition
         class="fixed top-6 right-6 z-50 max-w-sm rounded-md shadow-lg border px-4 py-3 text-sm"
         :class="toast.type === 'error'
            ? 'bg-red-50 border-red-200 text-red-800'
            : (toast.type === 'warning'
                ? 'bg-amber-50 border-amber-200 text-amber-900'
                : 'bg-emerald-50 border-emerald-200 text-emerald-800')">
        <div class="font-medium" x-text="toast.title"></div>
        <div class="text-xs mt-0.5" x-text="toast.body"></div>
    </div>

    {{-- Import modal --}}
    <div x-show="open" x-cloak
         class="fixed inset-0 z-40 flex items-center justify-center px-4"
         @keydown.escape.window="open = false; clearConflicts(); file = null;">
        <div class="absolute inset-0 bg-gray-900/50" @click="open = false; clearConflicts(); file = null;"></div>

        <div class="relative bg-white w-full rounded-lg shadow-xl border border-gray-200 max-h-[90vh] overflow-y-auto"
             :class="conflicts.length ? 'max-w-3xl' : 'max-w-lg'">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900">Import Layup Data</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Upload a CSV or JSON snapshot to merge into {{ $supplier->name }}.</p>
                </div>
                <button type="button" @click="open = false; clearConflicts(); file = null;" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="conflicts.length ? resubmitWithResolutions() : submitImport()" class="px-5 py-4 space-y-4">
                {{-- Drag & drop zone --}}
                <div
                    class="relative border-2 border-dashed rounded-md px-4 py-8 text-center cursor-pointer transition"
                    :class="dragOver ? 'border-emerald-500 bg-emerald-50' : 'border-gray-300 hover:border-gray-400'"
                    @click="$refs.fileInput.click()"
                    @dragover.prevent="dragOver = true"
                    @dragleave.prevent="dragOver = false"
                    @drop.prevent="handleDrop($event)">
                    <input type="file" x-ref="fileInput" class="hidden"
                           accept=".csv,.json,application/json,text/csv"
                           @change="handleFile($event.target.files[0])">
                    <svg class="mx-auto w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.9A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <div class="mt-2 text-sm text-gray-600" x-show="!file">
                        <span class="font-medium text-emerald-700">Click to upload</span> or drag and drop
                    </div>
                    <div class="text-xs text-gray-500 mt-1" x-show="!file">CSV or JSON, max 10MB</div>
                    <div class="mt-2 text-sm text-gray-900 font-medium" x-show="file" x-text="file && file.name"></div>
                    <div class="text-xs text-gray-500" x-show="file" x-text="file && (Math.round(file.size / 102.4) / 10) + ' KB'"></div>
                </div>

                {{-- Conflict resolution panel --}}
                <section x-show="conflicts.length" x-cloak
                         role="region" aria-label="Conflict resolution"
                         class="border border-amber-300 bg-amber-50/40 rounded-md">
                    <header class="px-4 py-3 border-b border-amber-200 flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <div class="font-semibold text-amber-900 text-sm">
                                <span x-text="conflicts.length"></span> conflict(s) detected — choose how to resolve each
                            </div>
                            <div class="text-xs text-amber-800/80 mt-0.5">Red fields differ between existing and imported data.</div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="applyAll('keep_existing')"
                                class="px-2.5 py-1.5 text-xs font-medium rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Apply all: Keep Existing
                            </button>
                            <button type="button" @click="applyAll('accept_incoming')"
                                class="px-2.5 py-1.5 text-xs font-medium rounded-md bg-white border border-amber-400 text-amber-800 hover:bg-amber-50">
                                Apply all: Accept Incoming
                            </button>
                        </div>
                    </header>

                    <ul class="divide-y divide-amber-200">
                        <template x-for="c in conflicts" :key="c.layup_name + ':' + c.layer_order">
                            <li class="p-4">
                                <div class="text-sm font-medium text-gray-900 mb-2">
                                    <span x-text="c.layup_name"></span>
                                    <span class="text-gray-500"> — Layer </span>
                                    <span x-text="c.layer_order"></span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    {{-- Existing --}}
                                    <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
                                        <div class="text-[10px] font-semibold uppercase tracking-wide text-gray-500 mb-2">Existing</div>
                                        <dl class="text-xs space-y-1">
                                            <template x-for="field in ['thickness','width','angle','grade']" :key="'ex-'+field">
                                                <div class="flex justify-between gap-3">
                                                    <dt class="text-gray-500 capitalize" x-text="field"></dt>
                                                    <dd class="font-medium"
                                                        :class="c.existing[field] !== c.incoming[field] ? 'text-red-700' : 'text-gray-900'"
                                                        x-text="c.existing[field] ?? '—'"></dd>
                                                </div>
                                            </template>
                                        </dl>
                                    </div>
                                    {{-- Incoming --}}
                                    <div class="rounded-md border border-amber-300 bg-amber-50 p-3">
                                        <div class="text-[10px] font-semibold uppercase tracking-wide text-amber-800 mb-2">Importing</div>
                                        <dl class="text-xs space-y-1">
                                            <template x-for="field in ['thickness','width','angle','grade']" :key="'in-'+field">
                                                <div class="flex justify-between gap-3">
                                                    <dt class="text-amber-900/70 capitalize" x-text="field"></dt>
                                                    <dd class="font-medium"
                                                        :class="c.existing[field] !== c.incoming[field] ? 'text-red-700' : 'text-gray-900'"
                                                        x-text="c.incoming[field] ?? '—'"></dd>
                                                </div>
                                            </template>
                                        </dl>
                                    </div>
                                </div>

                                <fieldset class="mt-3">
                                    <legend class="sr-only">Resolution for <span x-text="c.layup_name"></span> layer <span x-text="c.layer_order"></span></legend>
                                    <div class="flex flex-wrap gap-4 text-xs">
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="radio"
                                                   :name="'res-'+c.layup_name+'-'+c.layer_order"
                                                   :checked="resolutions[c.layup_name+':'+c.layer_order] === 'keep_existing'"
                                                   @change="setResolution(c, 'keep_existing')"
                                                   class="text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                            <span class="text-gray-800">Keep existing</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="radio"
                                                   :name="'res-'+c.layup_name+'-'+c.layer_order"
                                                   :checked="resolutions[c.layup_name+':'+c.layer_order] === 'accept_incoming'"
                                                   @change="setResolution(c, 'accept_incoming')"
                                                   class="text-amber-600 focus:ring-amber-500 border-gray-300">
                                            <span class="text-gray-800">Accept incoming</span>
                                        </label>
                                    </div>
                                </fieldset>
                            </li>
                        </template>
                    </ul>
                </section>

                <div x-show="!conflicts.length">
                    <label for="strategy-select" class="block text-xs font-medium text-gray-700 uppercase tracking-wide mb-1">Conflict Resolution Strategy</label>
                    <select id="strategy-select" x-model="strategy" class="w-full border border-gray-300 rounded-md text-sm px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="skip">Skip conflicts (keep existing)</option>
                        <option value="overwrite">Overwrite all (accept incoming)</option>
                        <option value="reject">Reject import on any conflict</option>
                        <option value="duplicate">Create duplicate layup with suffix</option>
                    </select>
                </div>

                <label class="flex items-start gap-2 cursor-pointer" x-show="!conflicts.length">
                    <input type="checkbox" x-model="dryRun" class="mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <div>
                        <div class="text-sm font-medium text-gray-900">Run as Dry Run</div>
                        <div class="text-xs text-gray-500">Simulate the import process without saving changes to the database.</div>
                    </div>
                </label>

                <div x-show="errorMsg" x-cloak class="text-sm text-red-700" x-text="errorMsg"></div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                    <template x-if="conflicts.length">
                        <button type="button" @click="clearConflicts()"
                                class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                            Cancel resolutions
                        </button>
                    </template>
                    <button type="button" @click="open = false; clearConflicts(); file = null;"
                            class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                        Close
                    </button>
                    <button type="submit" :disabled="submitting || !file || (conflicts.length > 0 && Object.keys(resolutions).length !== conflicts.length)"
                            class="px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!submitting && !conflicts.length">Confirm Import</span>
                        <span x-show="!submitting && conflicts.length">Apply Resolutions</span>
                        <span x-show="submitting">Working…</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function supplierImportModal(config) {
        return {
            open: false,
            file: null,
            dragOver: false,
            strategy: 'skip',
            dryRun: false,
            submitting: false,
            conflicts: [],
            resolutions: {},
            cachedPayload: null,
            errorMsg: '',
            toast: { visible: false, type: 'success', title: '', body: '' },

            conflictKey(c) { return c.layup_name + ':' + c.layer_order; },
            setResolution(c, action) { this.resolutions[this.conflictKey(c)] = action; },
            applyAll(action) {
                const next = {};
                this.conflicts.forEach(c => { next[this.conflictKey(c)] = action; });
                this.resolutions = next;
            },
            clearConflicts() {
                this.conflicts = [];
                this.resolutions = {};
                this.cachedPayload = null;
                this.errorMsg = '';
            },

            handleDrop(e) {
                this.dragOver = false;
                const f = e.dataTransfer.files && e.dataTransfer.files[0];
                if (f) this.handleFile(f);
            },

            handleFile(f) {
                this.errorMsg = '';
                if (!f) return;
                if (f.size > 10 * 1024 * 1024) {
                    this.errorMsg = 'File exceeds 10MB limit.';
                    return;
                }
                // Fresh file invalidates any cached payload / conflicts from an earlier attempt.
                this.conflicts = [];
                this.resolutions = {};
                this.cachedPayload = null;
                this.file = f;
            },

            showToast(type, title, body) {
                this.toast = { visible: true, type, title, body: body || '' };
                setTimeout(() => { this.toast.visible = false; }, 4500);
            },

            async readFileAsText(f) {
                return new Promise((resolve, reject) => {
                    const r = new FileReader();
                    r.onload = () => resolve(r.result);
                    r.onerror = () => reject(r.error);
                    r.readAsText(f);
                });
            },

            csvToPayload(text) {
                // Minimal CSV parser: header row required with columns
                // layup_name, layer_order, thickness, width, angle, grade, specification_code, ply_count
                const lines = text.split(/\r?\n/).filter(l => l.trim().length);
                if (lines.length < 2) throw new Error('CSV must include a header row and at least one data row.');
                const headers = lines[0].split(',').map(h => h.trim().toLowerCase());
                const layupsMap = {};
                for (let i = 1; i < lines.length; i++) {
                    const cols = lines[i].split(',').map(c => c.trim());
                    const row = {};
                    headers.forEach((h, idx) => { row[h] = cols[idx]; });
                    const name = row.layup_name || row.name;
                    if (!name) continue;
                    if (!layupsMap[name]) {
                        layupsMap[name] = {
                            name: name,
                            specification_code: row.specification_code || null,
                            ply_count: row.ply_count ? parseInt(row.ply_count, 10) : null,
                            grade: row.grade || null,
                            status: row.status || 'Active',
                            layers: [],
                        };
                    }
                    if (row.layer_order) {
                        layupsMap[name].layers.push({
                            layer_order: parseInt(row.layer_order, 10),
                            thickness: parseFloat(row.thickness),
                            width: parseFloat(row.width),
                            angle: parseFloat(row.angle),
                            grade: row.grade || null,
                        });
                    }
                }
                return { layups: Object.values(layupsMap) };
            },

            async buildPayload() {
                const text = await this.readFileAsText(this.file);
                const name = (this.file.name || '').toLowerCase();
                if (name.endsWith('.json') || text.trim().startsWith('{')) {
                    const parsed = JSON.parse(text);
                    // Accept either a full export document or a { payload: {...} } wrapper.
                    if (parsed.payload) return parsed.payload;
                    return parsed;
                }
                return this.csvToPayload(text);
            },

            async submitImport() {
                if (!this.file || this.submitting) return;
                this.submitting = true;
                this.errorMsg = '';
                this.conflicts = [];
                this.resolutions = {};

                try {
                    const payload = await this.buildPayload();
                    this.cachedPayload = payload;
                    const res = await fetch(config.importUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': config.csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            strategy: this.strategy,
                            dry_run: this.dryRun,
                            payload: payload,
                        }),
                    });

                    const body = await res.json().catch(() => ({}));

                    if (res.status === 409 || (body && body.data && body.data.status === 'conflict')) {
                        const conflicts = (body.data && body.data.conflicts) || [];
                        this.conflicts = conflicts;
                        const res0 = {};
                        conflicts.forEach(c => { res0[this.conflictKey(c)] = 'keep_existing'; });
                        this.resolutions = res0;
                        this.showToast('warning', 'Conflicts detected', 'Review each row and choose a resolution.');
                        return;
                    }

                    if (!res.ok) {
                        this.errorMsg = (body && (body.message || body.error)) || ('Import failed (HTTP '+ res.status +')');
                        this.showToast('error', 'Import failed', this.errorMsg);
                        return;
                    }

                    const summary = (body && body.data && body.data.summary) || {};
                    const parts = [];
                    if (summary.created_layups) parts.push(summary.created_layups + ' layups created');
                    if (summary.updated_layups) parts.push(summary.updated_layups + ' updated');
                    if (summary.created_layers) parts.push(summary.created_layers + ' layers added');
                    const detail = parts.length ? parts.join(', ') : (body.message || 'Import complete');

                    this.showToast('success', this.dryRun ? 'Dry run complete' : 'Import applied', detail);
                    if (!this.dryRun) {
                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        this.open = false;
                    }
                } catch (err) {
                    this.errorMsg = err.message || 'Unexpected error parsing the file.';
                    this.showToast('error', 'Import failed', this.errorMsg);
                } finally {
                    this.submitting = false;
                }
            },

            async resubmitWithResolutions() {
                if (this.submitting || !this.cachedPayload || !this.conflicts.length) return;
                const missing = this.conflicts.find(c => !this.resolutions[this.conflictKey(c)]);
                if (missing) {
                    this.errorMsg = 'Please choose a resolution for every conflict.';
                    return;
                }

                this.submitting = true;
                this.errorMsg = '';

                const resolutionsArr = this.conflicts.map(c => ({
                    layup_name: c.layup_name,
                    layer_order: c.layer_order,
                    action: this.resolutions[this.conflictKey(c)],
                }));

                try {
                    const res = await fetch(config.importUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': config.csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            strategy: 'manual',
                            dry_run: this.dryRun,
                            payload: this.cachedPayload,
                            resolutions: resolutionsArr,
                        }),
                    });

                    const body = await res.json().catch(() => ({}));

                    if (!res.ok) {
                        this.errorMsg = (body && (body.message || body.error)) || ('Import failed (HTTP '+ res.status +')');
                        this.showToast('error', 'Resolution failed', this.errorMsg);
                        return;
                    }

                    const summary = (body && body.data && body.data.summary) || {};
                    const parts = [];
                    if (summary.created_layups) parts.push(summary.created_layups + ' layups created');
                    if (summary.updated_layups) parts.push(summary.updated_layups + ' updated');
                    if (summary.created_layers) parts.push(summary.created_layers + ' layers added');
                    const detail = parts.length ? parts.join(', ') : (body.message || 'Resolutions applied');

                    this.showToast('success', this.dryRun ? 'Dry run complete' : 'Resolutions applied', detail);
                    this.clearConflicts();

                    if (!this.dryRun) {
                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        this.open = false;
                    }
                } catch (err) {
                    this.errorMsg = err.message || 'Unexpected error submitting resolutions.';
                    this.showToast('error', 'Resolution failed', this.errorMsg);
                } finally {
                    this.submitting = false;
                }
            },
        };
    }
</script>
@endsection
