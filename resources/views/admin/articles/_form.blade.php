<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label>{{ __('admin.title') }} (EN)</label>
            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $article->name_en) }}">
            @error('name_en')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label>{{ __('admin.title') }} (AR)</label>
            <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $article->name_ar) }}">
            @error('name_ar')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label>{{ __('admin.content') }} (EN)</label>
            <textarea name="description_en" rows="8" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $article->description_en) }}</textarea>
            @error('description_en')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label>{{ __('admin.content') }} (AR)</label>
            <textarea name="description_ar" rows="8" class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', $article->description_ar) }}</textarea>
            @error('description_ar')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label>خط المقال في الموقع</label>
            <select name="font_family" class="form-control">
                <option value="cairo" @selected(old('font_family', $article->font_family ?: 'cairo') === 'cairo')>Cairo — واضح عربي</option>
                <option value="tajawal" @selected(old('font_family', $article->font_family) === 'tajawal')>Tajawal — ناعم</option>
                <option value="serif" @selected(old('font_family', $article->font_family) === 'serif')>Serif — مقالات مطوّلة</option>
            </select>
        </div>
        <div class="mb-3">
            <label>حجم الخط في الموقع</label>
            <select name="font_size" class="form-control">
                @foreach (['small' => 'صغير', 'medium' => 'متوسط', 'large' => 'كبير', 'xlarge' => 'كبير جداً'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('font_size', $article->font_size ?: 'medium') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>{{ __('admin.image') }}</label>
            <input type="file" onchange="showImg(event)" name="image" class="form-control @error('image') is-invalid @enderror">
            @error('image')<small class="invalid-feedback">{{ $message }}</small>@enderror
            @php
                $url = ($article->image && $article->image->path && $article->image->path !== 'no-image.png') ? $article->img_path : '';
            @endphp
            <img width="120" style="margin-top:8px;border-radius:8px;{{ $url ? '' : 'display:none' }}" id="preview" src="{{ $url }}" alt="">
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="d-block">{{ __('admin.status') }}</label>
            <div class="custom-control custom-switch mt-2">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" class="custom-control-input" id="is_published" name="is_published" value="1"
                    {{ old('is_published', $article->exists ? $article->is_published : true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="is_published">{{ __('admin.published') }}</label>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function showImg(e) {
        const file = e.target.files[0];
        if (file) {
            const p = document.getElementById('preview');
            p.src = URL.createObjectURL(file);
            p.style.display = 'inline-block';
        }
    }
</script>
@endsection
