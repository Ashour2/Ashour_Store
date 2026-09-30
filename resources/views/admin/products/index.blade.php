@extends('admin.master')

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">All Products</h1>
    @php $isPharmacist = auth()->user()->isPharmacist(); @endphp
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
            @unless ($isPharmacist)<th>{{ __('admin.available_pharmacies') }}</th>@endunless
            @unless ($isPharmacist)<th>{{ __('admin.added_by') }}</th>@endunless
            <th>Actions</th>
        </tr>
        @forelse ($products as $product)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <img width="100" src="{{ $product->img_path }}" alt="">
                </td>
                {{-- <td>{{ $product->name }}</td> --}}
                <td>{{ $product->trans_name }}</td>
                <td>${{ $product->price }}</td>
                <td>{{ $product->quantity }}</td>
                {{-- <td>{{json_decode($product->category->name,true)[app()->getLocale()] ?? ""}}</td> --}}

                <td> {{ $product->category->trans_name }}</td>

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
                <td colspan="{{ $isPharmacist ? 7 : 9 }}" class="text-center">No Data Found</td>
            </tr>
        @endforelse

    </table>
    {{ $products->links() }}

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
