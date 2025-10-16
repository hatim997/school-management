@extends('layouts.master')

@section('title', __('Product Details'))

@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.products.index') }}">{{ __('Products') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Details') }}</li>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Product Details') }}</h5>
                <div>
                    <a href="{{ route('dashboard.products.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-arrow-back"></i> {{ __('Back to List') }}
                    </a>
                </div>
            </div>
            <div class="card-body">

                {{-- User Details --}}
                <h6 class="text-uppercase text-muted fw-bold border-bottom pb-1 mb-3">{{ __('Basic Details') }}</h6>
                <dl class="row">
                    <dt class="col-sm-3">{{ __('Name') }}</dt>
                    <dd class="col-sm-9">{{ $product->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">{{ __('Main Image') }}</dt>
                    <dd class="col-sm-9"><img src="{{ asset($product->main_image) }}" alt="{{ $product->name }}" height="50px">
                    </dd>

                    <dt class="col-sm-3">{{ __('Category') }}</dt>
                    <dd class="col-sm-9">{{ $product->category ? $product->category->name : 'N/A' }}</dd>

                    <dt class="col-sm-3">{{ __('SKU') }}</dt>
                    <dd class="col-sm-9">{{ $product->sku ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">{{ __('Cost Price') }}</dt>
                    <dd class="col-sm-9">{{ \App\Helpers\Helper::formatCurrency($product->cost_price) }}</dd>

                    <dt class="col-sm-3">{{ __('Retail Price') }}</dt>
                    <dd class="col-sm-9">{{ \App\Helpers\Helper::formatCurrency($product->price) }}</dd>

                    @php
                        $profit = $product->price - $product->cost_price;
                    @endphp
                    
                    <dt class="col-sm-3">{{ __('Profit') }}</dt>
                    <dd class="col-sm-9">{{ \App\Helpers\Helper::formatCurrency($profit) }}</dd>

                    <dt class="col-sm-3">{{ __('Stock') }}</dt>
                    <dd class="col-sm-9 text-capitalize">{{ $product->stock ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">{{ __('Short Description') }}</dt>
                    <dd class="col-sm-9">{{ $product->short_description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">{{ __('Description') }}</dt>
                    <dd class="col-sm-9">{!! $product->description ?? 'N/A' !!}</dd>
                </dl>

                @if (isset($product->productImages) && count($product->productImages) > 0)
                    {{-- Product Gallery --}}
                    <h6 class="text-uppercase text-muted fw-bold border-bottom pb-1 mt-4 mb-3">{{ __('Product Gallery') }}
                    </h6>
                    <div class="row">
                        @foreach ($product->productImages as $image)
                        <div class="col-md-3 mb-4">
                            <div class="card h-100 shadow-sm">
                                <img src="{{ asset($image->image) }}" class="card-img-top" alt="Project Image"
                                    style="object-fit: contain; height: 100px;">
                                <div class="card-body text-center">
                                    <a href="{{ asset($image->image) }}"
                                        class="btn btn-icon btn-text-success waves-effect waves-light rounded-pill me-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Download') }}"
                                        download>
                                        <i class="ti ti-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- Assignees Details --}}
                @if (isset($product->vendor_id))
                    <h6 class="text-uppercase text-muted fw-bold border-bottom pb-1 mt-4 mb-3">{{ __('Vendor') }}</h6>
                    <dl class="row">
                        <dt class="col-sm-6">{{ $product->vendor->name }}</dt>
                        @if (isset($product->vendor->userShop))
                            <dt class="col-sm-6">{{ $product->vendor->userShop->shop_name }}</dt>
                        @endif
                    </dl>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
    </script>
@endsection
