@extends('admin.master')

@section('content')
    @php $isPharmacist = auth()->user()->isPharmacist(); @endphp
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">All Products</h1>
        <div>
            @can('products.stock')
                @unless ($isPharmacist ?? auth()->user()->isPharmacist())
                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#bulkPharmacyModal">
                    <i class="fas fa-clinic-medical"></i> ربط كل الأدوية بالصيدليات
                </button>
                @endunless
            @endcan
            @can('products.create')
                <a href="{{ route('admin.products.import.form') }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> استيراد من Excel
                </a>
            @endcan
            @can('products.edit')
                <form class="d-inline" action="{{ route('admin.products.activate-all') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من تنشيط كل الأدوية؟ ستظهر جميعها في الموقع.');">
                    @csrf
                    @method('put')
                    <button class="btn btn-success"><i class="fas fa-eye"></i> {{ $isPharmacist ? 'تفعيل توفر أدويتي' : 'تنشيط الكل' }}</button>
                </form>
                <form class="d-inline" action="{{ route('admin.products.deactivate-all') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء تنشيط كل الأدوية؟ ستختفي جميعها من الموقع.');">
                    @csrf
                    @method('put')
                    <button class="btn btn-outline-secondary"><i class="fas fa-eye-slash"></i> {{ $isPharmacist ? 'إيقاف توفر أدويتي' : 'إلغاء تنشيط الكل' }}</button>
                </form>
            @endcan
            @can('products.view')
                <a href="{{ route('admin.products.export.excel') }}" class="btn btn-outline-primary">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </a>
                <a href="{{ route('admin.products.export.pdf') }}" target="_blank" class="btn btn-outline-danger">
                    <i class="fas fa-file-pdf"></i> تصدير PDF
                </a>
            @endcan
        </div>
    </div>
    <form method="GET" action="{{ route('admin.products.index') }}" class="mb-3" role="search">
        <div class="input-group" style="max-width: 480px;">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="ابحث باسم الدواء أو المادة الفعالة أو الباركود">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary" title="بحث عن الأدوية" aria-label="بحث عن الأدوية"><i class="fas fa-search"></i></button>
            </div>
            @if (request('q'))
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">مسح</a>
            @endif
        </div>
    </form>    @php $isPharmacist = auth()->user()->isPharmacist(); @endphp
    @if (session()->has('msg'))
        <div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
            {{ session('msg') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <table class="table table-bordered table-hover">
        <tr class="bg-dark text-white">
            <th>#</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Category</th>
            <th>الحالة</th>
            @unless ($isPharmacist)<th>{{ __('admin.available_pharmacies') }}</th>@endunless
            @unless ($isPharmacist)<th>{{ __('admin.added_by') }}</th>@endunless
            <th>Actions</th>
        </tr>
        @forelse ($products as $product)
            @php $ownStock = $isPharmacist ? $product->pharmacies->firstWhere('id', auth()->user()->pharmacy_id)?->pivot : null; @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <img width="100" src="{{ $product->img_path }}" alt="">
                </td>
                {{-- <td>{{ $product->name }}</td> --}}
                <td>{{ $product->trans_name }}</td>
                <td>${{ $ownStock?->price ?? $product->price }}</td>
                <td>{{ $ownStock?->quantity ?? $product->quantity }}</td>
                {{-- <td>{{json_decode($product->category->name,true)[app()->getLocale()] ?? ""}}</td> --}}

                <td> {{ $product->category->trans_name }}</td>
                <td>
                    @if ($isPharmacist)
                    <span class="badge {{ $ownStock?->is_available && $ownStock?->quantity > 0 ? 'badge-success' : 'badge-secondary' }}">{{ $ownStock?->is_available && $ownStock?->quantity > 0 ? 'متوفر في صيدليتي' : 'غير متوفر في صيدليتي' }}</span>
                    @unless($product->is_active)<small class="d-block text-muted">بانتظار تفعيل المدير للنشر</small>@endunless
                    @else
                    <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-secondary' }}">
                        {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                    @endif
                </td>

                @unless ($isPharmacist)
                {{-- Branches holding this medicine; click to see names + quantities --}}
                <td>
                    @if ($product->pharmacies->isEmpty())
                        <span class="badge badge-light text-muted">
                            <i class="fas fa-circle-xmark"></i> {{ __('admin.not_stocked') }}
                        </span>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-info"
                                data-toggle="modal" data-target="#phModal{{ $product->id }}">
                            <i class="fas fa-clinic-medical"></i>
                            {{ $product->pharmacies->count() }} {{ __('admin.pharmacy') }}
                            <span class="badge badge-info">{{ $product->pharmacies->sum('pivot.quantity') }}</span>
                        </button>
                    @endif
                </td>
                <td>{{ $product->addedBy->name ?: __('admin.unknown') }}</td>
                @endunless

                <td>
                    @can('products.edit')
                    <form class="d-inline" action="{{ route('admin.products.toggle-active', $product->id) }}" method="POST">
                        @csrf
                        @method('put')
                        <button type="submit" class="btn btn-sm {{ $product->is_active ? 'btn-outline-secondary' : 'btn-success' }}"
                                title="{{ $isPharmacist ? 'تغيير التوفر في صيدليتي' : ($product->is_active ? 'إخفاء من الموقع' : 'إظهار في الموقع') }}">
                            <i class="fas {{ $product->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                        </button>
                    </form>
                    @endcan
                    @unless ($isPharmacist)
                    {{-- Where this medicine is held: branch names + quantities (manager only) --}}
                    <a class="btn btn-sm btn-warning position-relative"
                       title="{{ __('admin.stock_location') }}"
                       href="{{ route('admin.products.stock.edit', $product->id) }}">
                        <i class="fas fa-map-marker-alt"></i>
                        @if ($product->pharmacies_count)
                            <span class="badge badge-light">{{ $product->pharmacies_count }}</span>
                        @endif
                    </a>
                    @endunless
                    <a class="btn btn-sm btn-primary" href="{{ route('admin.products.edit', $product->id) }}"><i
                            class="fas fa-edit"></i></a>
                    <form class="d-inline" action="{{ route('admin.products.destroy', $product->id) }}" method="POST">
                        @csrf
                        @method('delete')
                        <button onclick="return confirm('Are you sure?!')" class="btn btn-sm btn-danger"><i
                                class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $isPharmacist ? 8 : 10 }}" class="text-center">No Data Found</td>
            </tr>
        @endforelse

    </table>
    {{ $products->links() }}

    @unless ($isPharmacist)
    <div class="modal fade" id="bulkPharmacyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content" method="POST" action="{{ route('admin.products.bulk-attach-pharmacies') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">ربط كل الأدوية بالصيدليات</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">اختر الصيدليات. تُضاف كل الأدوية بكمية صفر، والروابط الموجودة لا تتكرر ولا تتغير كمياتها.</p>
                    @forelse ($pharmacies as $pharmacy)
                        <div class="custom-control custom-checkbox mb-2">
                            <input class="custom-control-input" type="checkbox" name="pharmacy_ids[]" value="{{ $pharmacy->id }}" id="bulkPharmacy{{ $pharmacy->id }}">
                            <label class="custom-control-label" for="bulkPharmacy{{ $pharmacy->id }}">{{ $pharmacy->trans_name }}</label>
                        </div>
                    @empty
                        <p class="text-danger mb-0">لا توجد صيدليات نشطة.</p>
                    @endforelse
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" {{ $pharmacies->isEmpty() ? 'disabled' : '' }}>ربط الآن</button>
                </div>
            </form>
        </div>
    </div>
    @endunless

    @unless ($isPharmacist)
    {{-- Branch breakdown for each medicine on this page --}}
    @foreach ($products as $product)
        @continue($product->pharmacies->isEmpty())
        <div class="modal fade" id="phModal{{ $product->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            {{ __('admin.available_pharmacies') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-0">
                        <p class="px-3 pt-3 mb-2 text-muted">{{ $product->trans_name }}</p>
                        <table class="table table-sm mb-0">
                            @foreach ($product->pharmacies as $pharmacy)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.pharmacies.show', $pharmacy->id) }}">
                                            <i class="fas fa-clinic-medical text-success"></i> {{ $pharmacy->trans_name }}
                                        </a>
                                        @unless ($pharmacy->is_active)
                                            <span class="badge badge-secondary">{{ __('admin.inactive') }}</span>
                                        @endunless
                                        @if ($pharmacy->address)
                                            <div class="small text-muted">{{ $pharmacy->address }}</div>
                                        @endif
                                    </td>
                                    <td class="text-right align-middle" style="width:90px">
                                        <span class="badge badge-success" style="font-size:.9rem">{{ $pharmacy->pivot->quantity }}</span>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-light font-weight-bold">
                                <td>{{ __('admin.branch_total') }}</td>
                                <td class="text-right">{{ $product->pharmacies->sum('pivot.quantity') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('admin.products.stock.edit', $product->id) }}" class="btn btn-warning">
                            <i class="fas fa-map-marker-alt"></i> {{ __('admin.edit_distribution') }}
                        </a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.back') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @endunless
@endsection

@section('title', 'Dashboard')
