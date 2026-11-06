<style>
    .category-edit-form .form-section { background: #fff; border: 1px solid #e9ecef; border-radius: 9px; padding: 18px; margin-bottom: 18px; }
    .category-edit-form .form-section-title { font-weight: 700; color: #343a40; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #eef0f2; }
    .category-edit-form .mb-3 > label { display: block; font-weight: 700; }
    .category-edit-form .mb-3 > label.optional-label::after, .category-edit-form .mb-3 > label.required-label::after { margin-right: 8px; padding: 2px 7px; border-radius: 10px; font-size: .72rem; font-weight: normal; }
    .category-edit-form .mb-3 > label.optional-label::after { content: 'اختياري'; background: #e9ecef; color: #495057; }
    .category-edit-form .mb-3 > label.required-label::after { content: 'إجباري'; background: #fde2e2; color: #b42318; }
</style>

<div class="form-section">
    <div class="form-section-title"><i class="fas fa-tags text-primary"></i> بيانات التصنيف</div>
    <div class="mb-3">
        <label class="required-label">اسم التصنيف بالإنجليزية</label>
        <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="مثال: Pain relief" value="{{ old('name_en', $category->name_en) }}" />
        @error('name_en')<small class="invalid-feedback">{{ $message }}</small>@enderror
    </div>
    <div class="mb-3">
        <label class="required-label">اسم التصنيف بالعربية</label>
        <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" placeholder="مثال: مسكنات" value="{{ old('name_ar', $category->name_ar) }}" />
        @error('name_ar')<small class="invalid-feedback">{{ $message }}</small>@enderror
    </div>
    <div class="mb-0">
        <label class="optional-label">ترتيب ظهور التصنيف في واجهات الموقع</label>
        <input type="number" min="1" name="home_display_order" class="form-control" placeholder="مثال: 1" value="{{ old('home_display_order', $category->home_display_order) }}" />
        <small class="form-text text-muted">الأصغر يظهر أولاً بعد واجهة الأدوية المتنوعة. اتركه فارغاً لعرضه في النهاية.</small>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title"><i class="fas fa-image text-success"></i> صورة التصنيف</div>
    <div class="mb-3">
    <label class="optional-label">صورة التصنيف</label>
    <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror" onchange="showImg(event)">
    <small class="form-text text-muted">اختر صورة عادية للتصنيف (JPG أو PNG أو WEBP، حتى 4MB).</small>
    @error('image')
        <small class="invalid-feedback d-block">{{ $message }}</small>
    @enderror
    <div class="mt-2">
        @if ($category->img_path)
            <img id="preview" src="{{ $category->img_path }}" alt="معاينة صورة التصنيف" style="width:100px;height:80px;object-fit:cover;border:1px solid #ddd;border-radius:6px;">
        @else
            <img id="preview" class="d-none" alt="معاينة صورة التصنيف" style="width:100px;height:80px;object-fit:cover;border:1px solid #ddd;border-radius:6px;">
        @endif
    </div>
</div>
</div>

<div class="form-section">
    <div class="form-section-title"><i class="fas fa-align-right text-info"></i> وصف التصنيف</div>
    <div class="mb-3">
            <label class="optional-label">الوصف بالإنجليزية</label>
            <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror" placeholder="وصف مختصر بالإنجليزية" rows="4">{{ old('description_en', $category->description_en) }}</textarea>
            @error('description_en')<small class="invalid-feedback">{{ $message }}</small>@enderror
    </div>
    <div class="mb-0">
            <label class="optional-label">الوصف بالعربية</label>
            <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror" placeholder="وصف مختصر بالعربية" rows="4">{{ old('description_ar', $category->description_ar) }}</textarea>
            @error('description_ar')<small class="invalid-feedback">{{ $message }}</small>@enderror
    </div>
</div>

<script>
function setupCategoryUnsavedWarning() {
    const form = document.getElementById('categoryForm');
    if (!form) return;
    let dirty = false;
    form.querySelectorAll('input, textarea, select').forEach((field) => {
        field.addEventListener('input', () => dirty = true);
        field.addEventListener('change', () => dirty = true);
    });
    form.addEventListener('submit', () => dirty = false);
    document.querySelectorAll('a[href]').forEach((link) => link.addEventListener('click', (event) => {
        if (dirty && !confirm('لديك تعديلات غير محفوظة. هل تريد المغادرة دون الحفظ؟')) event.preventDefault();
    }));
    window.addEventListener('beforeunload', (event) => {
        if (!dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });
}
</script>
