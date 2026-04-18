<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CLT Toolbox - Supplier Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-slate-100 min-h-screen" x-data="supplierApp()" x-init="load()">

<header class="bg-slate-900 text-white shadow">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <h1 class="text-xl font-bold">CLT Toolbox — Supplier Manager</h1>
        <div class="text-sm opacity-70">API: <code>/api/suppliers</code></div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-6 py-8">

    <!-- Toolbar -->
    <div class="flex flex-wrap gap-3 mb-4 items-center">
        <input x-model.debounce.400ms="search" @input="load()" type="text" placeholder="Search supplier..."
               class="border rounded px-3 py-2 w-64 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        <button @click="openCreate()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">+ New Supplier</button>
        <div class="ml-auto text-sm text-slate-600" x-show="meta.total !== null">
            Total: <span x-text="meta.total"></span>
        </div>
    </div>

    <!-- Flash -->
    <div x-show="flash.msg" x-cloak x-transition
         :class="flash.type === 'error' ? 'bg-red-100 text-red-800 border-red-300' : 'bg-green-100 text-green-800 border-green-300'"
         class="border rounded px-4 py-2 mb-4">
        <span x-text="flash.msg"></span>
    </div>

    <!-- Supplier Table -->
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-200">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Code</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Layups</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="s in suppliers" :key="s.id">
                    <tr class="border-t hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium" x-text="s.name"></td>
                        <td class="px-4 py-3 text-slate-600" x-text="s.code || '-'"></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded"
                                  :class="s.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-slate-200'"
                                  x-text="s.status"></span>
                        </td>
                        <td class="px-4 py-3" x-text="s.layups_count ?? '-'"></td>
                        <td class="px-4 py-3 text-right space-x-1">
                            <button @click="openDetail(s.id)" class="text-blue-600 hover:underline text-xs">Detail</button>
                            <button @click="openEdit(s)" class="text-amber-600 hover:underline text-xs">Edit</button>
                            <button @click="exportSupplier(s)" class="text-indigo-600 hover:underline text-xs">Export</button>
                            <button @click="openImport(s)" class="text-purple-600 hover:underline text-xs">Import</button>
                            <button @click="destroy(s)" class="text-red-600 hover:underline text-xs">Delete</button>
                        </td>
                    </tr>
                </template>
                <tr x-show="suppliers.length === 0"><td colspan="5" class="px-4 py-6 text-center text-slate-500">No suppliers</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Create/Edit Supplier Modal -->
    <div x-show="modal.supplier" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-40" @click.self="modal.supplier = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-lg font-bold mb-4" x-text="form.id ? 'Edit Supplier' : 'New Supplier'"></h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Name *</label>
                    <input x-model="form.name" type="text" class="border rounded w-full px-3 py-2">
                    <p class="text-red-600 text-xs mt-1" x-show="errors.name" x-text="errors.name?.[0]"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Code</label>
                    <input x-model="form.code" type="text" placeholder="SUP-2026-001" class="border rounded w-full px-3 py-2">
                    <p class="text-red-600 text-xs mt-1" x-show="errors.code" x-text="errors.code?.[0]"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select x-model="form.status" class="border rounded w-full px-3 py-2">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-6">
                <button @click="modal.supplier = false" class="px-4 py-2 border rounded">Cancel</button>
                <button @click="save()" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </div>
    </div>

    <!-- Detail Modal (nested layups + layers) -->
    <div x-show="modal.detail" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-40" @click.self="modal.detail = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-lg font-bold" x-text="detail.name"></h2>
                    <p class="text-sm text-slate-500" x-text="detail.code"></p>
                </div>
                <button @click="modal.detail = false" class="text-slate-500 hover:text-black text-2xl leading-none">&times;</button>
            </div>

            <div class="mb-4">
                <button @click="openLayupCreate()" class="text-xs bg-blue-600 text-white px-3 py-1 rounded">+ Add Layup</button>
            </div>

            <template x-for="lu in detail.layups" :key="lu.id">
                <div class="border rounded mb-3">
                    <div class="bg-slate-100 px-4 py-2 flex justify-between items-center">
                        <div>
                            <span class="font-semibold" x-text="lu.name"></span>
                            <span class="text-xs text-slate-500 ml-2" x-text="'ply: ' + lu.ply_count"></span>
                            <span class="text-xs px-2 py-0.5 rounded ml-2"
                                  :class="lu.status === 'Active' ? 'bg-green-200' : 'bg-slate-300'" x-text="lu.status"></span>
                        </div>
                        <div class="space-x-2 text-xs">
                            <button @click="openLayerCreate(lu)" class="text-blue-600 hover:underline">+ Layer</button>
                            <button @click="openLayupEdit(lu)" class="text-amber-600 hover:underline">Edit</button>
                            <button @click="destroyLayup(lu)" class="text-red-600 hover:underline">Delete</button>
                        </div>
                    </div>
                    <table class="w-full text-xs">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Order</th>
                                <th class="px-3 py-2 text-left">Thickness</th>
                                <th class="px-3 py-2 text-left">Width</th>
                                <th class="px-3 py-2 text-left">Angle</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="lr in lu.layers" :key="lr.id">
                                <tr class="border-t">
                                    <td class="px-3 py-2" x-text="lr.layer_order"></td>
                                    <td class="px-3 py-2" x-text="lr.thickness"></td>
                                    <td class="px-3 py-2" x-text="lr.width"></td>
                                    <td class="px-3 py-2" x-text="lr.angle + '°'"></td>
                                    <td class="px-3 py-2 text-right space-x-2">
                                        <button @click="openLayerEdit(lu, lr)" class="text-amber-600 hover:underline">Edit</button>
                                        <button @click="destroyLayer(lu, lr)" class="text-red-600 hover:underline">Delete</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="!lu.layers || lu.layers.length === 0"><td colspan="5" class="px-3 py-2 text-center text-slate-400">No layers</td></tr>
                        </tbody>
                    </table>
                </div>
            </template>
            <div x-show="!detail.layups || detail.layups.length === 0" class="text-center text-slate-500 py-4">No layups</div>
        </div>
    </div>

    <!-- Layup Modal -->
    <div x-show="modal.layup" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="modal.layup = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-lg font-bold mb-4" x-text="layupForm.id ? 'Edit Layup' : 'New Layup'"></h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Name *</label>
                    <input x-model="layupForm.name" class="border rounded w-full px-3 py-2">
                    <p class="text-red-600 text-xs mt-1" x-show="errors.name" x-text="errors.name?.[0]"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Ply Count</label>
                    <input x-model.number="layupForm.ply_count" type="number" class="border rounded w-full px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea x-model="layupForm.description" class="border rounded w-full px-3 py-2" rows="2"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select x-model="layupForm.status" class="border rounded w-full px-3 py-2">
                        <option>Active</option><option>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-6">
                <button @click="modal.layup = false" class="px-4 py-2 border rounded">Cancel</button>
                <button @click="saveLayup()" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </div>
    </div>

    <!-- Layer Modal -->
    <div x-show="modal.layer" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="modal.layer = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <h2 class="text-lg font-bold mb-4" x-text="layerForm.id ? 'Edit Layer' : 'New Layer'"></h2>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Layer Order *</label>
                    <input x-model.number="layerForm.layer_order" type="number" class="border rounded w-full px-3 py-2">
                    <p class="text-red-600 text-xs mt-1" x-show="errors.layer_order" x-text="errors.layer_order?.[0]"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Thickness *</label>
                    <input x-model.number="layerForm.thickness" type="number" step="0.01" class="border rounded w-full px-3 py-2">
                    <p class="text-red-600 text-xs mt-1" x-show="errors.thickness" x-text="errors.thickness?.[0]"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Width *</label>
                    <input x-model.number="layerForm.width" type="number" step="0.01" class="border rounded w-full px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Angle *</label>
                    <select x-model.number="layerForm.angle" class="border rounded w-full px-3 py-2">
                        <option :value="0">0°</option>
                        <option :value="45">45°</option>
                        <option :value="90">90°</option>
                        <option :value="-45">-45°</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-6">
                <button @click="modal.layer = false" class="px-4 py-2 border rounded">Cancel</button>
                <button @click="saveLayer()" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div x-show="modal.import" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-40" @click.self="modal.import = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-start mb-4">
                <h2 class="text-lg font-bold">Import to: <span x-text="importCtx.supplierName"></span></h2>
                <button @click="modal.import = false" class="text-slate-500 text-2xl leading-none">&times;</button>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Strategy</label>
                    <select x-model="importForm.strategy" class="border rounded w-full px-3 py-2">
                        <option value="skip">skip (default)</option>
                        <option value="overwrite">overwrite</option>
                        <option value="reject">reject</option>
                        <option value="duplicate">duplicate</option>
                        <option value="manual">manual</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" x-model="importForm.dry_run"> Dry run (preview only)
                    </label>
                </div>
                <div class="flex items-end gap-2">
                    <input type="file" accept=".json" @change="loadFile($event)" class="text-xs">
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium mb-1">Payload JSON</label>
                <textarea x-model="importForm.payloadText" rows="10"
                    class="border rounded w-full px-3 py-2 font-mono text-xs"
                    placeholder='{"name":"...","layups":[{"name":"LU-001","layers":[{"layer_order":1,"thickness":0.25,"width":100,"angle":0}]}]}'></textarea>
            </div>

            <!-- Conflicts Section: GitHub-style single-conflict navigation with field-level diff -->
            <div x-show="conflicts.length > 0" x-cloak class="border border-amber-300 bg-amber-50 rounded p-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-amber-800">Resolusi Konflik</h3>
                    <div class="flex items-center gap-2 text-sm">
                        <button @click="conflictIndex = Math.max(0, conflictIndex - 1)"
                                :disabled="conflictIndex === 0"
                                class="px-2 py-1 border rounded disabled:opacity-40">&larr; Sebelumnya</button>
                        <span class="font-medium text-slate-700">
                            <span x-text="conflictIndex + 1"></span> dari <span x-text="conflicts.length"></span> konflik
                        </span>
                        <button @click="conflictIndex = Math.min(conflicts.length - 1, conflictIndex + 1)"
                                :disabled="conflictIndex >= conflicts.length - 1"
                                class="px-2 py-1 border rounded disabled:opacity-40">Berikutnya &rarr;</button>
                    </div>
                </div>

                <template x-if="conflicts[conflictIndex]">
                    <div class="bg-white rounded p-3 text-xs">
                        <div class="font-semibold mb-2 text-slate-800">
                            Layup: <span x-text="conflicts[conflictIndex].layup_name"></span>
                            &nbsp;/&nbsp; Layer order: <span x-text="conflicts[conflictIndex].layer_order"></span>
                        </div>

                        <!-- Field-by-field diff table -->
                        <table class="w-full border-collapse mb-3">
                            <thead>
                                <tr class="bg-slate-100">
                                    <th class="text-left p-2 border">Field</th>
                                    <th class="text-left p-2 border">Existing</th>
                                    <th class="text-left p-2 border">Incoming</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="field in diffFields(conflicts[conflictIndex])" :key="field">
                                    <tr>
                                        <td class="p-2 border font-medium" x-text="field"></td>
                                        <td class="p-2 border"
                                            :class="isDifferent(conflicts[conflictIndex], field) ? 'bg-red-50 text-red-700 font-semibold' : ''"
                                            x-text="formatValue(conflicts[conflictIndex].existing[field])"></td>
                                        <td class="p-2 border"
                                            :class="isDifferent(conflicts[conflictIndex], field) ? 'bg-green-50 text-green-700 font-semibold' : ''"
                                            x-text="formatValue(conflicts[conflictIndex].incoming[field])"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div x-show="importForm.strategy === 'manual'" class="flex gap-3 items-center pt-2 border-t">
                            <span class="text-xs font-medium text-slate-600">Keputusan:</span>
                            <label class="text-xs flex items-center gap-1 cursor-pointer">
                                <input type="radio"
                                       :name="'res-'+conflictIndex"
                                       :checked="currentResolution() === 'keep_existing'"
                                       @change="setResolution(conflicts[conflictIndex], 'keep_existing')"> Keep Existing
                            </label>
                            <label class="text-xs flex items-center gap-1 cursor-pointer">
                                <input type="radio"
                                       :name="'res-'+conflictIndex"
                                       :checked="currentResolution() === 'accept_incoming'"
                                       @change="setResolution(conflicts[conflictIndex], 'accept_incoming')"> Accept Incoming
                            </label>
                            <span x-show="currentResolution()" class="text-xs text-green-700 ml-2">&#10003; tersimpan</span>
                        </div>
                        <div x-show="importForm.strategy !== 'manual'" class="text-xs text-slate-500 italic pt-2 border-t">
                            Pakai strategi <b>manual</b> untuk memilih per-konflik.
                        </div>
                    </div>
                </template>
            </div>

            <!-- Summary -->
            <div x-show="importSummary" x-cloak class="border border-green-300 bg-green-50 rounded p-4 mb-4 text-sm">
                <h3 class="font-bold text-green-800 mb-2">Summary</h3>
                <pre x-text="JSON.stringify(importSummary, null, 2)" class="text-xs"></pre>
            </div>

            <div class="flex gap-2 justify-end">
                <button @click="modal.import = false" class="px-4 py-2 border rounded">Close</button>
                <button @click="runImport()" class="bg-purple-600 text-white px-4 py-2 rounded">Run Import</button>
            </div>
        </div>
    </div>

</main>

<script>
function supplierApp() {
    return {
        suppliers: [],
        meta: { total: null },
        search: '',
        flash: { msg: '', type: 'success' },
        errors: {},
        modal: { supplier: false, detail: false, layup: false, layer: false, import: false },
        form: { id: null, name: '', code: '', status: 'Active' },
        detail: { id: null, name: '', code: '', layups: [] },
        layupForm: { id: null, supplier_id: null, name: '', ply_count: 5, description: '', status: 'Active' },
        layerForm: { id: null, supplier_id: null, layup_id: null, layer_order: 1, thickness: 0.25, width: 100, angle: 0 },
        importCtx: { supplierId: null, supplierName: '' },
        importForm: { strategy: 'skip', dry_run: false, payloadText: '', resolutions: [] },
        conflicts: [],
        conflictIndex: 0,
        importSummary: null,

        csrf() { return document.querySelector('meta[name=csrf-token]').content; },

        async fetch(url, opts = {}) {
            opts.headers = Object.assign({ 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf() }, opts.headers || {});
            const res = await fetch(url, opts);
            let body = null;
            try { body = await res.json(); } catch {}
            return { ok: res.ok, status: res.status, body };
        },

        notify(msg, type = 'success') {
            this.flash = { msg, type };
            setTimeout(() => { this.flash.msg = ''; }, 4000);
        },

        async load() {
            const q = this.search ? `?q=${encodeURIComponent(this.search)}` : '';
            const { body } = await this.fetch('/api/suppliers' + q);
            const page = body?.data || {};
            this.suppliers = page.data || [];
            this.meta.total = page.total ?? 0;
        },

        openCreate() {
            this.form = { id: null, name: '', code: '', status: 'Active' };
            this.errors = {};
            this.modal.supplier = true;
        },

        openEdit(s) {
            this.form = { id: s.id, name: s.name, code: s.code || '', status: s.status };
            this.errors = {};
            this.modal.supplier = true;
        },

        async save() {
            this.errors = {};
            const isEdit = !!this.form.id;
            const url = isEdit ? `/api/suppliers/${this.form.id}` : '/api/suppliers';
            const method = isEdit ? 'PUT' : 'POST';
            const { ok, status, body } = await this.fetch(url, { method, body: JSON.stringify(this.form) });
            if (!ok) {
                if (status === 422) this.errors = body.errors || {};
                this.notify(body?.message || 'Save failed', 'error');
                return;
            }
            this.modal.supplier = false;
            this.notify(isEdit ? 'Supplier updated' : 'Supplier created');
            this.load();
        },

        async destroy(s) {
            if (!confirm(`Delete supplier "${s.name}"? Layups + layers will be deleted.`)) return;
            const { ok, body } = await this.fetch(`/api/suppliers/${s.id}`, { method: 'DELETE' });
            if (!ok) { this.notify(body?.message || 'Delete failed', 'error'); return; }
            this.notify('Supplier deleted');
            this.load();
        },

        async openDetail(id) {
            const { ok, body } = await this.fetch(`/api/suppliers/${id}`);
            if (!ok) { this.notify('Failed to load detail', 'error'); return; }
            this.detail = body.data;
            this.modal.detail = true;
        },

        async reloadDetail() {
            if (!this.detail.id) return;
            const { body } = await this.fetch(`/api/suppliers/${this.detail.id}`);
            this.detail = body.data;
        },

        openLayupCreate() {
            this.layupForm = { id: null, supplier_id: this.detail.id, name: '', ply_count: 5, description: '', status: 'Active' };
            this.errors = {};
            this.modal.layup = true;
        },

        openLayupEdit(lu) {
            this.layupForm = { id: lu.id, supplier_id: this.detail.id, name: lu.name, ply_count: lu.ply_count, description: lu.description || '', status: lu.status };
            this.errors = {};
            this.modal.layup = true;
        },

        async saveLayup() {
            this.errors = {};
            const f = this.layupForm;
            const isEdit = !!f.id;
            const url = isEdit ? `/api/suppliers/${f.supplier_id}/layups/${f.id}` : `/api/suppliers/${f.supplier_id}/layups`;
            const method = isEdit ? 'PUT' : 'POST';
            const { ok, status, body } = await this.fetch(url, { method, body: JSON.stringify(f) });
            if (!ok) {
                if (status === 422) this.errors = body.errors || {};
                this.notify(body?.message || 'Save failed', 'error');
                return;
            }
            this.modal.layup = false;
            this.notify('Layup saved');
            this.reloadDetail();
        },

        async destroyLayup(lu) {
            if (!confirm(`Delete layup "${lu.name}"?`)) return;
            const { ok } = await this.fetch(`/api/suppliers/${this.detail.id}/layups/${lu.id}`, { method: 'DELETE' });
            if (ok) { this.notify('Layup deleted'); this.reloadDetail(); }
        },

        openLayerCreate(lu) {
            this.layerForm = { id: null, supplier_id: this.detail.id, layup_id: lu.id, layer_order: (lu.layers?.length || 0) + 1, thickness: 0.25, width: 100, angle: 0 };
            this.errors = {};
            this.modal.layer = true;
        },

        openLayerEdit(lu, lr) {
            this.layerForm = { id: lr.id, supplier_id: this.detail.id, layup_id: lu.id, layer_order: lr.layer_order, thickness: parseFloat(lr.thickness), width: parseFloat(lr.width), angle: lr.angle };
            this.errors = {};
            this.modal.layer = true;
        },

        async saveLayer() {
            this.errors = {};
            const f = this.layerForm;
            const isEdit = !!f.id;
            const url = isEdit
                ? `/api/suppliers/${f.supplier_id}/layups/${f.layup_id}/layers/${f.id}`
                : `/api/suppliers/${f.supplier_id}/layups/${f.layup_id}/layers`;
            const method = isEdit ? 'PUT' : 'POST';
            const { ok, status, body } = await this.fetch(url, { method, body: JSON.stringify(f) });
            if (!ok) {
                if (status === 422) this.errors = body.errors || {};
                this.notify(body?.message || 'Save failed', 'error');
                return;
            }
            this.modal.layer = false;
            this.notify('Layer saved');
            this.reloadDetail();
        },

        async destroyLayer(lu, lr) {
            if (!confirm(`Delete layer order ${lr.layer_order}?`)) return;
            const { ok } = await this.fetch(`/api/suppliers/${this.detail.id}/layups/${lu.id}/layers/${lr.id}`, { method: 'DELETE' });
            if (ok) { this.notify('Layer deleted'); this.reloadDetail(); }
        },

        async exportSupplier(s) {
            const res = await fetch(`/api/suppliers/${s.id}/export`);
            const body = await res.json();
            const blob = new Blob([JSON.stringify(body.data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `supplier-${s.id}-${s.name.replace(/\s+/g, '_')}.json`;
            a.click();
            URL.revokeObjectURL(url);
            this.notify('Exported');
        },

        openImport(s) {
            this.importCtx = { supplierId: s.id, supplierName: s.name };
            this.importForm = { strategy: 'skip', dry_run: false, payloadText: '', resolutions: [] };
            this.conflicts = [];
            this.conflictIndex = 0;
            this.importSummary = null;
            this.modal.import = true;
        },

        loadFile(e) {
            const f = e.target.files[0];
            if (!f) return;
            const reader = new FileReader();
            reader.onload = ev => {
                try {
                    const obj = JSON.parse(ev.target.result);
                    // Accept raw exported data OR full import body
                    if (obj.payload) this.importForm.payloadText = JSON.stringify(obj.payload, null, 2);
                    else this.importForm.payloadText = JSON.stringify(obj, null, 2);
                } catch { this.notify('Invalid JSON file', 'error'); }
            };
            reader.readAsText(f);
        },

        diffFields(c) {
            if (!c) return [];
            const keys = new Set([...Object.keys(c.existing || {}), ...Object.keys(c.incoming || {})]);
            return [...keys];
        },
        isDifferent(c, field) {
            if (!c) return false;
            return JSON.stringify(c.existing?.[field]) !== JSON.stringify(c.incoming?.[field]);
        },
        formatValue(v) {
            if (v === null || v === undefined) return '—';
            return String(v);
        },
        currentResolution() {
            const c = this.conflicts[this.conflictIndex];
            if (!c) return null;
            const r = this.importForm.resolutions.find(r => r.layup_name === c.layup_name && r.layer_order === c.layer_order);
            return r?.action || null;
        },
        setResolution(c, action) {
            const idx = this.importForm.resolutions.findIndex(r => r.layup_name === c.layup_name && r.layer_order === c.layer_order);
            const entry = { layup_name: c.layup_name, layer_order: c.layer_order, action };
            if (idx >= 0) this.importForm.resolutions[idx] = entry;
            else this.importForm.resolutions.push(entry);
        },

        async runImport() {
            let payload;
            try { payload = JSON.parse(this.importForm.payloadText); }
            catch { this.notify('Payload invalid JSON', 'error'); return; }

            const body = {
                strategy: this.importForm.strategy,
                dry_run: this.importForm.dry_run,
                payload,
            };
            if (this.importForm.strategy === 'manual' && this.importForm.resolutions.length) {
                body.resolutions = this.importForm.resolutions;
            }

            const res = await this.fetch(`/api/suppliers/${this.importCtx.supplierId}/import`, {
                method: 'POST',
                body: JSON.stringify(body),
            });

            if (res.status === 409) {
                this.conflicts = res.body?.data?.conflicts || [];
                this.conflictIndex = 0;
                this.importSummary = null;
                this.notify('Conflicts detected — resolve below', 'error');
                return;
            }
            if (!res.ok) {
                this.notify(res.body?.message || 'Import failed', 'error');
                return;
            }
            this.conflicts = [];
            this.importSummary = res.body?.data?.summary || res.body?.data;
            this.notify(this.importForm.dry_run ? 'Dry run complete' : 'Import complete');
            if (!this.importForm.dry_run) this.load();
        },
    };
}
</script>
</body>
</html>
