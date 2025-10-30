@extends('layouts.master')

@section('title', __('Checkout'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">{{ __('Checkout') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-circle fs-4 me-2"></i>
                    <div>
                        {{ session('error') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <div class="d-flex align-items-start">
                    <i class="ti ti-alert-triangle fs-4 me-2 mt-1"></i>
                    <div>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <form action="{{ route('dashboard.checkout.submit') }}" method="POST" id="payment-form">
            @csrf
            <input type="hidden" name="payment_method" id="payment_method" value="stripe">
            {{-- <input type="hidden" name="subject_id" id="subject_id" value="{{ $subject->id }}"> --}}
            <input type="hidden" name="amount" id="amount" value="{{ $subject->price }}">
            <div class="row">
                <!-- Cart left -->
                <div class="col-xl-8 mb-6 mb-xl-0">
                    <!-- Shopping bag -->
                    <h5>Selected Subject</h5>

                    @if (isset($relatedSubjects) && count($relatedSubjects) > 0)
                        <ul class="list-group mb-4">
                            @foreach ($relatedSubjects as $related)
                                <li class="list-group-item p-4 {{ $related->is_coming == 1 ? 'bg-light opacity-75' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="radio" name="subject_id" id="subject_{{ $related->id }}"
                                                value="{{ $related->id }}" class="form-check-input me-2"
                                                {{ $related->is_coming == 1 ? 'disabled' : '' }}
                                                {{ $related->id == $subject->id ? 'checked' : '' }}>

                                            <label for="subject_{{ $related->id }}" class="mb-0">
                                                <strong>{{ $related->name }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $related->duration }} weeks
                                                    • {{ $related->code }}
                                                </small>
                                            </label>
                                        </div>

                                        <div class="text-end">
                                            @if ($related->is_coming == 1)
                                                <span class="badge bg-label-warning">Coming Soon</span>
                                            @else
                                                <span class="text-primary fw-semibold">
                                                    {{ \App\Helpers\Helper::formatCurrency($related->price) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <h5>Select a Child</h5>
                    <div class="row">
                        <div class="mb-4 col-md-12">
                            <label class="form-label" for="child_id">{{ __('Children') }}</label>
                            <select id="child_id" name="child_id"
                                class="select2 form-select @error('child_id') is-invalid @enderror">
                                <option value="" selected disabled>{{ __('Select a Child') }}</option>
                                @if (isset($parentChildrens) && count($parentChildrens) > 0)
                                    @foreach ($parentChildrens as $parentChildren)
                                        <option value="{{ $parentChildren->id }}"
                                            {{ $parentChildren->id == old('child_id') ? 'selected' : '' }}>
                                            {{ $parentChildren->child->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('child_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <h5>Billing Details</h5>
                    <div class="row">
                        <div class="mb-4 col-md-6">
                            <label for="name" class="form-label">{{ __('Name') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name"
                                name="name" required placeholder="{{ __('Enter name') }}" autofocus
                                value="{{ old('name', $billing?->name) }}" />
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-6">
                            <label for="email" class="form-label">{{ __('Email') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('email') is-invalid @enderror" type="email" id="email"
                                name="email" required placeholder="{{ __('Enter email') }}"
                                value="{{ old('email', $billing?->email) }}" />
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-6">
                            <label for="phone" class="form-label">{{ __('Phone Number') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('phone') is-invalid @enderror" type="text" id="phone"
                                name="phone" required placeholder="{{ __('Enter phone number') }}"
                                value="{{ old('phone', $billing?->phone) }}" />
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-6">
                            <label for="country" class="form-label">{{ __('Country') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('country') is-invalid @enderror" type="text"
                                id="country" name="country" required placeholder="{{ __('Enter country') }}"
                                value="{{ old('country', $billing?->country) }}" />
                            @error('country')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-4">
                            <label for="state" class="form-label">{{ __('State / Province') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('state') is-invalid @enderror" type="text"
                                id="state" name="state" required placeholder="{{ __('Enter state or province') }}"
                                value="{{ old('state', $billing?->state) }}" />
                            @error('state')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-4">
                            <label for="city" class="form-label">{{ __('City') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('city') is-invalid @enderror" type="text" id="city"
                                name="city" required placeholder="{{ __('Enter city') }}"
                                value="{{ old('city', $billing?->city) }}" />
                            @error('city')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-4">
                            <label for="zip" class="form-label">{{ __('ZIP / Postal Code') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('zip') is-invalid @enderror" type="text" id="zip"
                                name="zip" required placeholder="{{ __('Enter ZIP / Postal Code') }}"
                                value="{{ old('zip', $billing?->zip) }}" />
                            @error('zip')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-4 col-md-12">
                            <label for="address" class="form-label">{{ __('Address') }}</label><span
                                class="text-danger">*</span>
                            <input class="form-control @error('address') is-invalid @enderror" type="text"
                                id="address" name="address" required placeholder="{{ __('Enter full address') }}"
                                value="{{ old('address', $billing?->address) }}" />
                            @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <h5>Payment Details</h5>
                    <div class="row">
                        <div class="col-xxl-12 col-lg-12">
                            <div class="nav-align-top">
                                <ul class="nav nav-pills card-header-pills row-gap-2 flex-wrap" id="paymentTabs"
                                    role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="pills-stripe-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-stripe" type="button" role="tab"
                                            aria-controls="pills-stripe" aria-selected="true">
                                            Stripe
                                        </button>
                                    </li>
                                    {{-- <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="pills-cc-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-cc" type="button" role="tab"
                                            aria-controls="pills-cc" aria-selected="true">
                                            Card
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="pills-paypal-tab" data-bs-toggle="pill"
                                            data-bs-target="#pills-paypal" type="button" role="tab"
                                            aria-controls="pills-paypal" aria-selected="true">
                                            Paypal
                                        </button>
                                    </li> --}}
                                </ul>
                            </div>
                            <div class="tab-content px-0 pb-0" id="paymentTabsContent">
                                <!-- Stripe Payment -->
                                <div class="tab-pane fade show active" id="pills-stripe" role="tabpanel"
                                    aria-labelledby="pills-stripe-tab">
                                    <div class="row g-6">
                                        <div class="col-md-12">
                                            <label for="card-element"
                                                class="form-label">{{ __('Credit or debit card') }}</label>
                                            <div id="card-element" class="form-control p-3" style="height:auto;"></div>
                                            <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                                        </div>
                                    </div>
                                </div>
                                {{-- <!-- Credit card -->
                                <div class="tab-pane fade show active" id="pills-cc" role="tabpanel"
                                    aria-labelledby="pills-cc-tab">
                                    <div class="row g-6">
                                        <div class="col-md-12">
                                            <label for="paymentCard"
                                                class="form-label">{{ __('Card Number') }}</label><span
                                                class="text-danger">*</span>
                                            <input class="form-control @error('paymentCard') is-invalid @enderror"
                                                type="text" id="paymentCard" name="paymentCard" required
                                                placeholder="{{ __('1356 3215 6548 7898') }}" autofocus
                                                value="{{ old('paymentCard', $billing?->card_number) }}" />
                                            @error('paymentCard')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="paymentCardName"
                                                class="form-label">{{ __('Name') }}</label><span
                                                class="text-danger">*</span>
                                            <input class="form-control @error('paymentCardName') is-invalid @enderror"
                                                type="text" id="paymentCardName" name="paymentCardName" required
                                                placeholder="{{ __('i.e. John Doe') }}" autofocus
                                                value="{{ old('paymentCardName', $billing?->card_name) }}" />
                                            @error('paymentCardName')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="paymentCardExpiryDate"
                                                class="form-label">{{ __('Exp. Date') }}</label><span
                                                class="text-danger">*</span>
                                            <input
                                                class="form-control @error('paymentCardExpiryDate') is-invalid @enderror"
                                                type="text" id="paymentCardExpiryDate" name="paymentCardExpiryDate"
                                                required placeholder="{{ __('MM/YY') }}" autofocus
                                                value="{{ old('paymentCardExpiryDate', $billing?->card_exp) }}" />
                                            @error('paymentCardExpiryDate')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="paymentCardCvv"
                                                class="form-label">{{ __('CVV Code') }}</label><span
                                                class="text-danger">*</span>
                                            <input class="form-control @error('paymentCardCvv') is-invalid @enderror"
                                                type="text" id="paymentCardCvv" name="paymentCardCvv" required
                                                placeholder="{{ __('654') }}" autofocus
                                                value="{{ old('paymentCardCvv', $billing?->card_cvv) }}" />
                                            @error('paymentCardCvv')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check form-switch mt-2">
                                                <input type="checkbox" name="cardFutureBilling" class="form-check-input"
                                                    id="cardFutureBilling" />
                                                <label for="cardFutureBilling" class="form-check-label">Save card for
                                                    future
                                                    billing?</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Paypal -->
                                <div class="tab-pane fade show" id="pills-paypal" role="tabpanel"
                                    aria-labelledby="pills-paypal-tab">
                                    <div class="row g-6">
                                        <div class="col-md-12">
                                            <p>
                                                Paypal is a type of payment method where the recipient make payment
                                                for the order from their paypal account.
                                            </p>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart right -->
                <div class="col-xl-4">
                    <div class="border rounded p-6 mb-4">
                        <!-- Price Details -->
                        <h6>Price Details</h6>
                        <dl class="row mb-0 text-heading">
                            <dt class="col-6 fw-normal">Subject Price</dt>
                            <dd class="col-6 text-end">{{ \App\Helpers\Helper::formatCurrency($subject->price) }}</dd>

                            <dt class="col-6 fw-normal">Discount</dt>
                            <dd class="col-6 text-end">{{ \App\Helpers\Helper::formatCurrency(0) }}</dd>

                            <dt class="col-6 fw-normal">Other Charges</dt>
                            <dd class="col-6 text-end">{{ \App\Helpers\Helper::formatCurrency(0) }}</dd>
                        </dl>
                        <hr class="mx-n6 my-6" />
                        <dl class="row mb-0">
                            <dt class="col-6 text-heading">Total</dt>
                            <dd class="col-6 fw-medium text-end text-heading mb-0">
                                {{ \App\Helpers\Helper::formatCurrency($subject->price) }}</dd>
                        </dl>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-next">Place Order</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe("{{ env('STRIPE_KEY') }}"); // your public key
        const elements = stripe.elements();
        const card = elements.create("card", {
            style: {
                base: {
                    fontSize: "16px"
                }
            }
        });
        card.mount("#card-element");

        card.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            displayError.textContent = event.error ? event.error.message : '';
        });

        // Form submit handler
        const form = document.getElementById('payment-form');
        if (form) {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const {
                    token,
                    error
                } = await stripe.createToken(card);
                if (error) {
                    document.getElementById('card-errors').textContent = error.message;
                } else {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'stripeToken');
                    hiddenInput.setAttribute('value', token.id);
                    form.appendChild(hiddenInput);
                    form.submit();
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            // === Default Payment Method ===
            $('#payment_method').val('stripe');

            // === When Payment Tab Changes ===
            $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                const target = $(e.target).attr('data-bs-target'); // e.g. #pills-paypal or #pills-cc
                if (target === '#pills-cc') {
                    $('#payment_method').val('card');
                } else if (target === '#pills-paypal') {
                    $('#payment_method').val('paypal');
                } else if (target === '#pills-stripe') {
                    $('#payment_method').val('stripe');
                }
            });

            // === Card Number ===
            $('#paymentCard').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // remove non-digits
                value = value.substring(0, 16); // max 16 digits
                value = value.replace(/(.{4})/g, '$1 ').trim(); // space every 4 digits
                $(this).val(value);
            });

            // === Expiry Date ===
            $('#paymentCardExpiryDate').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // only digits

                if (value.length > 4) value = value.substring(0, 4); // MMYY only

                if (value.length >= 2) {
                    let month = parseInt(value.substring(0, 2));
                    if (month > 12) month = 12; // cap month at 12
                    value = month.toString().padStart(2, '0') + '/' + value.substring(2);
                }
                $(this).val(value);
            });

            // === Expiry Validation on Blur ===
            $('#paymentCardExpiryDate').on('blur', function() {
                let val = $(this).val();
                $('#expiryError').remove(); // remove old error message

                if (!/^\d{2}\/\d{2}$/.test(val)) return;

                let [month, year] = val.split('/').map(Number);
                year = 2000 + year; // e.g., 25 => 2025
                let now = new Date();
                let expiry = new Date(year, month);

                if (expiry < now) {
                    $(this).after(
                        '<span id="expiryError" class="text-danger mt-1 d-block">Card is expired. Please use a valid date.</span>'
                    );
                }
            });

            // === CVV ===
            $('#paymentCardCvv').on('input', function() {
                let value = $(this).val().replace(/\D/g, ''); // only numbers
                value = value.substring(0, 3); // max 3 digits
                $(this).val(value);
            });

        });
    </script>
@endsection
