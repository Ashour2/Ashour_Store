{{-- Create a branch without leaving the medicine form, then add its row inline --}}
<div class="modal fade" id="addPharmacyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-clinic-medical text-success"></i> {{ __('admin.add_pharmacy') }}</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="newPharmacyErrors"></div>

                <div class="form-group">
                    <label>{{ __('admin.pharmacy_name') }} (AR) <span class="text-danger">*</span></label>
                    <input type="text" id="newPharmacyNameAr" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ __('admin.pharmacy_name') }} (EN) <span class="text-danger">*</span></label>
                    <input type="text" id="newPharmacyNameEn" class="form-control">
                </div>
                <div class="form-group">
                    <label>{{ __('admin.phone') }}</label>
                    <input type="text" id="newPharmacyPhone" dir="ltr" class="form-control">
                </div>
                <div class="form-group mb-0">
                    <label>{{ __('admin.address') }}</label>
                    <input type="text" id="newPharmacyAddress" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="saveNewPharmacy">
                    <i class="fas fa-save"></i> {{ __('admin.save') }}
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.back') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
// This partial renders inside the page content, which the layout parses before
// it loads jQuery, so everything is wired up on DOMContentLoaded instead.
document.addEventListener('DOMContentLoaded', function () {
    const save    = document.getElementById('saveNewPharmacy');
    const rows    = document.getElementById('pharmacyRows');
    const errors  = document.getElementById('newPharmacyErrors');
    const fields  = {
        name_ar: document.getElementById('newPharmacyNameAr'),
        name_en: document.getElementById('newPharmacyNameEn'),
        phone:   document.getElementById('newPharmacyPhone'),
        address: document.getElementById('newPharmacyAddress'),
    };

    if (!save) return;

    function showErrors(messages) {
        errors.textContent = '';
        messages.forEach(function (message) {
            const line = document.createElement('div');
            line.textContent = message;
            errors.appendChild(line);
        });
        errors.classList.remove('d-none');
    }

    /**
     * Append the new branch as a row in the scrollable list, already ticked so
     * the manager can type its quantity right away.
     */
    function addRow(pharmacy) {
        const note = document.getElementById('noPharmaciesNote');
        if (note) note.remove();

        const row = document.createElement('div');
        row.className = 'pharmacy-row is-picked';

        // Tick box
        const checkWrap = document.createElement('div');
        checkWrap.className = 'custom-control custom-checkbox pharmacy-row-check';

        const check = document.createElement('input');
        check.type = 'checkbox';
        check.className = 'custom-control-input pharmacy-check';
        check.id = 'pharmacy' + pharmacy.id;
        check.name = 'pharmacies[]';
        check.value = pharmacy.id;
        check.dataset.qty = 'qty' + pharmacy.id;
        check.checked = true;

        const checkLabel = document.createElement('label');
        checkLabel.className = 'custom-control-label';
        checkLabel.setAttribute('for', check.id);

        checkWrap.appendChild(check);
        checkWrap.appendChild(checkLabel);

        // Name
        const name = document.createElement('label');
        name.className = 'pharmacy-row-name mb-0';
        name.setAttribute('for', check.id);

        const line = document.createElement('span');
        line.className = 'd-flex align-items-center flex-wrap';
        line.style.gap = '6px';
        line.innerHTML = '<i class="fas fa-clinic-medical text-success"></i>';

        const title = document.createElement('span');
        title.className = 'text-truncate';
        title.textContent = pharmacy.name;
        line.appendChild(title);
        name.appendChild(line);

        if (pharmacy.address) {
            const address = document.createElement('small');
            address.className = 'text-muted d-block text-truncate';
            address.textContent = pharmacy.address;
            name.appendChild(address);
        }

        // Quantity
        const cell = document.createElement('div');
        cell.className = 'pharmacy-row-qty';

        const input = document.createElement('input');
        input.type = 'number';
        input.min = '0';
        input.max = '1000000';
        input.id = 'qty' + pharmacy.id;
        input.className = 'form-control';
        input.name = 'quantities[' + pharmacy.id + ']';
        input.placeholder = @json(__('admin.quantity'));
        cell.appendChild(input);

        row.appendChild(checkWrap);
        row.appendChild(name);
        row.appendChild(cell);
        rows.appendChild(row);

        if (window.countPickedPharmacies) window.countPickedPharmacies();

        // Clear any search and jump to the page the new branch landed on
        if (window.refreshPharmacyPager) window.refreshPharmacyPager(row);

        // Bring the new branch into view and let the manager type its quantity
        row.scrollIntoView({ block: 'nearest' });
        input.focus();
    }

    save.addEventListener('click', function () {
        save.disabled = true;
        errors.classList.add('d-none');

        const body = new FormData();
        Object.keys(fields).forEach(function (key) { body.append(key, fields[key].value); });
        body.append('is_active', '1');

        fetch(@json(route('admin.pharmacies.store')), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': @json(csrf_token()),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: body,
        })
        .then(function (response) {
            return response.json().then(function (data) { return { ok: response.ok, data: data }; });
        })
        .then(function (result) {
            if (!result.ok) {
                const messages = result.data.errors
                    ? Object.keys(result.data.errors).reduce(function (all, key) {
                        return all.concat(result.data.errors[key]);
                      }, [])
                    : [result.data.message || 'Error'];
                showErrors(messages);
                return;
            }

            addRow(result.data);
            Object.keys(fields).forEach(function (key) { fields[key].value = ''; });

            if (window.jQuery) {
                window.jQuery('#addPharmacyModal').modal('hide');
            }
        })
        .catch(function () { showErrors(['Error']); })
        .finally(function () { save.disabled = false; });
    });
});
</script>
