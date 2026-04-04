<style>
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
            <label>Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name', $product->name) }}" />
            @error('name')
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
            <label>Description</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" placeholder=" Description" rows="4">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>


    <div class="col-md-4">
        <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" placeholder="Price" value="{{ old('price', $product->price) }}" />
            @error('price')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" placeholder="Quantity" value="{{ old('quantity', $product->quantity) }}" />
            @error('quantity')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label>Category</label>
            <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>
