<style>
    /* Scrollable pharmacy picker: branch name + its quantity on one line */
    .pharmacy-scroll {
        max-height: 320px;
        overflow-y: auto;
        background: #fff;
    }
    .pharmacy-search { max-width: 340px; }
    .pharmacy-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 12px;
        border-bottom: 1px solid #f1f1f1;
    }
    .pharmacy-row:last-child { border-bottom: 0; }
    .pharmacy-row:hover { background: #f8f9fa; }
    /* A ticked branch stands out from the ones left alone */
    .pharmacy-row.is-picked { background: #f1fbf4; }
    .pharmacy-row-check { flex: 0 0 auto; }
    .pharmacy-row-name {
        flex: 1;
        min-width: 0;
        cursor: pointer;
        margin: 0;
    }
    .pharmacy-row-qty { flex: 0 0 130px; }
    /* An unticked branch keeps its box visible but inert */
    .pharmacy-row-qty input:disabled { background: #f4f6f9; }

    .perv-img {
        width: 80px;
        height: 100px;
        object-fit: cover
    }

    .gallery-wrapper {
    display: flex;
    gap: 5px
}

.gallery-wrapper div {
    position: relative;
}

.gallery-wrapper div span {
    position: absolute;
    width: 15px;
    height: 15px;
    background: #ff8282;
    color: #fff;
    top: 5px;
    right: 5px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    border-radius: 50%;
    opacity: 0;
    cursor: pointer;
    visibility: hidden;
    transition: all .3s ease;
}

.gallery-wrapper div:hover span {
    opacity: 1;
    visibility: visible;
}

.gallery-wrapper div span:hover {
    background: #f00;
}

</style>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label>English Name</label>
            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="English Name" value="{{ old('name_en', $product->name_en) }}" />
            @error('name_en')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>
       <div class="col-md-6">
        <div class="mb-3">
            <label>Arabic Name</label>
            <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" placeholder="English Name" value="{{ old('name_ar', $product->name_ar) }}" />
            @error('name_ar')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>


    <div class="col-md-6">
        <div class="mb-3">
            <label>Image</label>
            <input type="file" onchange="showImg(event)" name="image" class="form-control @error('image') is-invalid @enderror" />
            @error('image')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
            @php
                $url = '';
                if ($product->image){
                    $url = $product->img_path;
                }
            @endphp
            <img width="80" class="perv-img" id="preview" src="{{ $url }}" alt="">
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label>Gallery</label>
            <input type="file" name="gallery[]" multiple class="form-control @error('gallery') is-invalid @enderror" />
            @error('gallery')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
            @if ($product->gallery)
            <div class="gallery-wrapper">
                @foreach ($product->gallery as $item)
                    <div>
                        <img width="80" class="perv-img" src="{{ asset('images/'.$item->path) }}" alt="">
                        <span onclick="delImg(event, {{ $item->id }})">x</span>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label>English Description</label>
            <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror" placeholder=" Description_en" rows="4">{{ old('description_en', $product->description_en) }}</textarea>
            @error('description_en')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label>Arabic Description</label>
            <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror" placeholder=" Description_ar" rows="4">{{ old('description_ar', $product->description_ar) }}</textarea>
            @error('description_ar')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>


    <div class="col-md-4">
        <div class="mb-3">
            <label>{{ __('admin.price') }}</label>
            <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" placeholder="Price" value="{{ old('price', $product->price) }}" />
            @error('price')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
            <div class="custom-control custom-switch mt-2">
                <input type="hidden" name="show_price" value="0">
                <input type="checkbox" class="custom-control-input" id="show_price" name="show_price" value="1"
                    {{ old('show_price', $product->exists ? $product->show_price : true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="show_price">{{ __('admin.show_price') }}</label>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label>{{ __('admin.quantity') }}</label>
            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" placeholder="Quantity" value="{{ old('quantity', $product->quantity) }}" />
            @error('quantity')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
            @php $sd = old('stock_display', $product->stock_display ?? 'quantity'); @endphp
            <select name="stock_display" class="form-control mt-2">
                <option value="quantity" {{ $sd == 'quantity' ? 'selected' : '' }}>{{ __('admin.stock_show_quantity') }}</option>
                <option value="available" {{ $sd == 'available' ? 'selected' : '' }}>{{ __('admin.stock_show_available') }}</option>
                <option value="hidden" {{ $sd == 'hidden' ? 'selected' : '' }}>{{ __('admin.stock_hidden') }}</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label>Category</label>
            <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->trans_name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>

    {{-- ===== Medicine (pharmacy) fields ===== --}}
    <div class="col-12"><hr><h6 class="mb-3">{{ __('admin.medicine_info') }}</h6></div>

    <div class="col-md-4">
        <div class="mb-3">
            <label>{{ __('admin.active_ingredient') }}</label>
            <input type="text" name="active_ingredient" class="form-control" value="{{ old('active_ingredient', $product->active_ingredient) }}" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label>{{ __('admin.manufacturer') }}</label>
            <input type="text" name="manufacturer" class="form-control" value="{{ old('manufacturer', $product->manufacturer) }}" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label>{{ __('admin.dosage_form') }}</label>
            <input type="text" name="dosage_form" class="form-control" placeholder="{{ __('admin.dosage_form_ph') }}" value="{{ old('dosage_form', $product->dosage_form) }}" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label>{{ __('admin.recommended_dosage') }} (EN)</label>
            <textarea name="dosage_en" class="form-control" rows="2">{{ old('dosage_en', $product->dosage_en) }}</textarea>
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label>{{ __('admin.recommended_dosage') }} (AR)</label>
            <textarea name="dosage_ar" class="form-control" rows="2">{{ old('dosage_ar', $product->dosage_ar) }}</textarea>
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label>{{ __('admin.barcode') }}</label>
            <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}" />
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label>{{ __('admin.expiry_date') }}</label>
            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', optional($product->expiry_date)->format('Y-m-d')) }}" />
        </div>
    </div>
    {{-- ===== Which pharmacies stock this medicine (manager only) ===== --}}
    @php
        $selected_ids = collect(old('pharmacies', $held->keys()->all()))->map(fn ($id) => (int) $id);
    @endphp
    <div class="col-12">
        <hr>
        <div class="d-flex align-items-center flex-wrap mb-1">
            <h6 class="mb-0 mr-2">
                <i class="fas fa-map-marker-alt text-danger"></i> {{ __('admin.belongs_to_pharmacies') }}
                <span class="badge badge-info" id="pharmacyCount">{{ $selected_ids->count() }}</span>
            </h6>
            <button type="button" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#addPharmacyModal">
                <i class="fas fa-plus"></i> {{ __('admin.add_pharmacy') }}
            </button>
            {{-- Fold the whole list away when the manager does not need it --}}
            <button type="button" class="btn btn-sm btn-outline-secondary" id="togglePharmacies"
                    data-toggle="collapse" data-target="#pharmacyBox"
                    aria-expanded="true" aria-controls="pharmacyBox">
                <i class="fas fa-eye-slash"></i> <span id="togglePharmaciesText">{{ __('admin.hide') }}</span>
            </button>
        </div>
        <p class="text-muted small">{{ __('admin.belongs_to_pharmacies_hint') }}</p>

        <div class="collapse show" id="pharmacyBox">
            {{-- Find a branch by name or address without scrolling the whole list --}}
            <div class="input-group input-group-sm mb-2 pharmacy-search">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <input type="text" id="pharmacySearch" class="form-control"
                       placeholder="{{ __('admin.search_pharmacy') }}" autocomplete="off">
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary" id="pharmacySearchClear"
                            title="{{ __('admin.reset') }}"><i class="fas fa-times"></i></button>
                </div>
            </div>

            {{-- Tick a branch, then type how many units it holds --}}
            <div class="pharmacy-scroll border rounded" id="pharmacyRows">
                @forelse ($pharmacies as $pharmacy)
                    @php
                        $checked = $selected_ids->contains($pharmacy->id);
                        $quantity = old('quantities.' . $pharmacy->id, $held[$pharmacy->id] ?? null);
                    @endphp
                    <div class="pharmacy-row {{ $checked ? 'is-picked' : '' }}">
                        <div class="custom-control custom-checkbox pharmacy-row-check">
                            <input type="checkbox" class="custom-control-input pharmacy-check"
                                   id="pharmacy{{ $pharmacy->id }}"
                                   name="pharmacies[]" value="{{ $pharmacy->id }}"
                                   data-qty="qty{{ $pharmacy->id }}"
                                   {{ $checked ? 'checked' : '' }}>
                            <label class="custom-control-label" for="pharmacy{{ $pharmacy->id }}"></label>
                        </div>
                        <label class="pharmacy-row-name mb-0" for="pharmacy{{ $pharmacy->id }}">
                            <span class="d-flex align-items-center flex-wrap" style="gap:6px">
                                <i class="fas fa-clinic-medical {{ $pharmacy->is_active ? 'text-success' : 'text-secondary' }}"></i>
                                <span class="text-truncate">{{ $pharmacy->trans_name }}</span>
                                @unless ($pharmacy->is_active)
                                    <span class="badge badge-secondary">{{ __('admin.inactive') }}</span>
                                @endunless
                            </span>
                            @if ($pharmacy->address)
                                <small class="text-muted d-block text-truncate">{{ $pharmacy->address }}</small>
                            @endif
                        </label>
                        <div class="pharmacy-row-qty">
                            <input type="number" min="0" max="1000000" id="qty{{ $pharmacy->id }}"
                                   name="quantities[{{ $pharmacy->id }}]" value="{{ $quantity }}"
                                   class="form-control @error('quantities.' . $pharmacy->id) is-invalid @enderror"
                                   placeholder="{{ __('admin.quantity') }}"
                                   {{ $checked ? '' : 'disabled' }}>
                            @error('quantities.' . $pharmacy->id)
                                <small class="invalid-feedback d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                @empty
                    <div class="p-3 text-muted" id="noPharmaciesNote">
                        <i class="fas fa-circle-info"></i> {{ __('admin.add_pharmacy_first') }}
                    </div>
                @endforelse

                {{-- Shown when a search matches nothing --}}
                <div class="p-3 text-muted d-none" id="pharmacyNoResults">
                    <i class="fas fa-magnifying-glass"></i> {{ __('admin.no_pharmacy_match') }}
                </div>
            </div>

            {{-- Only a handful of branches are on screen at a time; the rest stay
                 in the form so a branch ticked on another page is still saved. --}}
            <div class="d-flex justify-content-between align-items-center mt-2 d-none" id="pharmacyPager">
                <small class="text-muted" id="pharmacyRange"></small>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary" id="pharmacyPrev">
                        {{ __('admin.previous') }}
                    </button>
                    <button type="button" class="btn btn-light" id="pharmacyPage" disabled></button>
                    <button type="button" class="btn btn-outline-secondary" id="pharmacyNext">
                        {{ __('admin.next') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label class="d-block">{{ __('admin.requires_prescription') }}</label>
            <div class="custom-control custom-switch mt-2">
                <input type="hidden" name="requires_prescription" value="0">
                <input type="checkbox" class="custom-control-input" id="requires_prescription" name="requires_prescription" value="1" {{ old('requires_prescription', $product->requires_prescription) ? 'checked' : '' }}>
                <label class="custom-control-label" for="requires_prescription">{{ __('admin.yes_no') }}</label>
            </div>
        </div>
    </div>
</div>

{{-- The layout loads jQuery after the page content, so wire up on DOMContentLoaded --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const PER_PAGE = 5;

    const box     = document.getElementById('pharmacyBox');
    const counter = document.getElementById('pharmacyCount');
    const toggle  = document.getElementById('togglePharmacies');
    const label   = document.getElementById('togglePharmaciesText');
    const rows    = document.getElementById('pharmacyRows');
    const search  = document.getElementById('pharmacySearch');
    const clear   = document.getElementById('pharmacySearchClear');
    const pager   = document.getElementById('pharmacyPager');
    const range   = document.getElementById('pharmacyRange');
    const pageBtn = document.getElementById('pharmacyPage');
    const prev    = document.getElementById('pharmacyPrev');
    const next    = document.getElementById('pharmacyNext');
    const empty   = document.getElementById('pharmacyNoResults');

    if (!rows) return;

    const texts = {
        hide: @json(__('admin.hide')),
        show: @json(__('admin.show')),
        range: @json(__('admin.showing_range')),
        page: @json(__('admin.page_of')),
    };

    let page = 1;

    function allRows() {
        return Array.from(rows.querySelectorAll('.pharmacy-row'));
    }

    /** Rows whose branch name or address matches what is typed in the search. */
    function matchingRows() {
        const term = (search ? search.value : '').trim().toLowerCase();
        const all = allRows();

        if (!term) return all;

        return all.filter(function (row) {
            return row.textContent.toLowerCase().indexOf(term) !== -1;
        });
    }

    function fill(template, values) {
        return Object.keys(values).reduce(function (text, key) {
            return text.split(':' + key).join(values[key]);
        }, template);
    }

    /**
     * Show only the current page of matches. Rows that are hidden stay in the
     * form, so a branch ticked elsewhere is still submitted.
     */
    function render() {
        const all = allRows();
        const matches = matchingRows();
        const pages = Math.max(1, Math.ceil(matches.length / PER_PAGE));

        page = Math.min(Math.max(1, page), pages);

        const from = (page - 1) * PER_PAGE;
        const to = from + PER_PAGE;

        all.forEach(function (row) { row.classList.add('d-none'); });
        matches.slice(from, to).forEach(function (row) { row.classList.remove('d-none'); });

        if (empty) empty.classList.toggle('d-none', matches.length > 0);

        if (pager) {
            // No point showing controls for a list that fits on one page
            pager.classList.toggle('d-none', matches.length <= PER_PAGE);
        }
        if (range) {
            range.textContent = matches.length
                ? fill(texts.range, {
                    from: matches.length ? from + 1 : 0,
                    to: Math.min(to, matches.length),
                    total: matches.length,
                  })
                : '';
        }
        if (pageBtn) pageBtn.textContent = fill(texts.page, { page: page, pages: pages });
        if (prev) prev.disabled = page <= 1;
        if (next) next.disabled = page >= pages;

        rows.scrollTop = 0;
    }

    /** A branch only accepts a quantity once it has been ticked. */
    window.syncPharmacyRow = function (check) {
        const quantity = document.getElementById(check.dataset.qty);
        const row = check.closest('.pharmacy-row');

        if (quantity) {
            quantity.disabled = !check.checked;
            if (!check.checked) quantity.value = '';
        }

        if (row) row.classList.toggle('is-picked', check.checked);
    };

    window.countPickedPharmacies = function () {
        if (counter) {
            counter.textContent = rows.querySelectorAll('.pharmacy-check:checked').length;
        }
    };

    /** Re-paginate after the list changes, e.g. a branch added inline. */
    window.refreshPharmacyPager = function (showRow) {
        if (showRow && search) search.value = '';

        if (showRow) {
            const index = matchingRows().indexOf(showRow);
            if (index > -1) page = Math.floor(index / PER_PAGE) + 1;
        }

        render();
    };

    rows.addEventListener('change', function (event) {
        const check = event.target.closest('.pharmacy-check');
        if (!check) return;

        window.syncPharmacyRow(check);
        window.countPickedPharmacies();

        if (check.checked) {
            const quantity = document.getElementById(check.dataset.qty);
            if (quantity) quantity.focus();
        }
    });

    if (search) {
        search.addEventListener('input', function () { page = 1; render(); });
        // Enter inside the search box must not submit the medicine form
        search.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') event.preventDefault();
        });
    }
    if (clear) {
        clear.addEventListener('click', function () {
            if (search) search.value = '';
            page = 1;
            render();
            if (search) search.focus();
        });
    }
    if (prev) prev.addEventListener('click', function () { page -= 1; render(); });
    if (next) next.addEventListener('click', function () { page += 1; render(); });

    // Keep the hide/show button's wording in step with the collapse
    if (box && toggle && label) {
        box.addEventListener('shown.bs.collapse', function () {
            label.textContent = texts.hide;
            toggle.querySelector('i').className = 'fas fa-eye-slash';
        });
        box.addEventListener('hidden.bs.collapse', function () {
            label.textContent = texts.show;
            toggle.querySelector('i').className = 'fas fa-eye';
        });
    }

    render();
});
</script>
