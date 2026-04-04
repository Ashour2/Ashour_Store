<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label>English Name</label>
            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="English Name" value="{{ old('name_en', $category->name_en) }}" />
            @error('name_en')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label>Arabic Name</label>
            <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" placeholder="Arabic Name" value="{{ old('name_ar', $category->name_ar) }}" />
            @error('name_ar')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label>Image</label>
    <input type="file" onchange="showImg(event)" name="image" class="form-control @error('image') is-invalid @enderror" />
    @error('image')
        <small class="invalid-feedback">{{ $message }}</small>
    @enderror
    @php
        $url = '';
        if ($category->image){
            $url = $category->img_path;
        }
    @endphp
    <img width="80" id="preview" src="{{ $url }}" alt="">
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label>English Description</label>
            <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror" placeholder="English Description" rows="4">{{ old('description_en', $category->description_en) }}</textarea>
            @error('description_en')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label>Arabic Description</label>
            <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror" placeholder="Arabic Description" rows="4">{{ old('description_ar', $category->description_ar) }}</textarea>
            @error('description_ar')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>
