@extends('layouts.shop')

@section('title', 'Thanh toán - Hải Sản Tươi Ngon')

@section('content')
    <h2 class="shop-page-title mb-4">Thanh toán</h2>

    @if (empty($cart['items']))
        <div class="shop-panel text-center">
            <p class="mb-3">Giỏ hàng của bạn đang trống, không thể thanh toán.</p>
            <a href="{{ url('/san-pham') }}" class="btn-shop-orange">Tiếp tục mua sắm</a>
        </div>
    @else
        @php $pm = old('payment_method', 'cod'); @endphp
        <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm">
            @csrf
            <input type="hidden" name="voucher_code" id="voucherCodeField" value="">
            <div class="row g-4">
                {{-- LEFT: shipping info --}}
                <div class="col-lg-7">
                    <div class="shop-panel">
                        <h5 class="fw-bold mb-3">Thông tin giao hàng</h5>

                        <div class="mb-3">
                            <label class="shop-form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="shop-input"
                                value="{{ old('full_name', $user->full_name) }}" placeholder="Nhập họ tên người nhận">
                        </div>
                        <div class="mb-3">
                            <label class="shop-form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="shop-input" value="{{ old('phone', $user->phone) }}"
                                placeholder="Nhập số điện thoại">
                        </div>
                        <div class="mb-3">
                            <label class="shop-form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                            <textarea name="shipping_address" class="shop-input" rows="3"
                                placeholder="Nhập địa chỉ nhận hàng">{{ old('shipping_address', $user->address) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="shop-form-label">Ghi chú</label>
                            <textarea name="note" class="shop-input" rows="2"
                                placeholder="Ghi chú cho đơn hàng (tùy chọn)">{{ old('note') }}</textarea>
                        </div>

                        <div class="mb-1">
                            <label class="shop-form-label">Phương thức thanh toán</label>
                            <label class="pm-option {{ $pm === 'cod' ? 'active' : '' }}" for="pm-cod">
                                <input class="form-check-input" type="radio" name="payment_method" id="pm-cod" value="cod" {{ $pm === 'cod' ? 'checked' : '' }}>
                                <span>Thanh toán khi nhận hàng (COD)</span>
                                <span class="ms-auto text-muted">{{ icon('truck', 18) }}</span>
                            </label>
                            <label class="pm-option {{ $pm === 'bank' ? 'active' : '' }}" for="pm-bank">
                                <input class="form-check-input" type="radio" name="payment_method" id="pm-bank" value="bank" {{ $pm === 'bank' ? 'checked' : '' }}>
                                <span>Chuyển khoản ngân hàng</span>
                                <span class="ms-auto text-muted">{{ icon('credit-card', 18) }}</span>
                            </label>
                            <label
                                class="pm-option {{ $pm === 'vnpay' ? 'active' : '' }} {{ $vnpayAvailable ? '' : 'opacity-50' }}"
                                for="pm-vnpay">
                                <input class="form-check-input" type="radio" name="payment_method" id="pm-vnpay" value="vnpay"
                                    {{ $pm === 'vnpay' ? 'checked' : '' }} {{ $vnpayAvailable ? '' : 'disabled' }}>
                                <span>Thanh toán online qua VNPay <small class="text-muted d-block" style="font-size:12px">ATM
                                        nội địa, Visa/Master, QR (môi trường sandbox)</small></span>
                                <span class="badge-vnpay ms-auto">VN<span style="color:#e31d1c">PAY</span></span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ url('/gio-hang') }}" class="text-decoration-none">{{ icon('arrow-left', null, 'me-1') }}
                            Quay lại giỏ hàng</a>
                    </div>
                </div>

                {{-- RIGHT: order summary --}}
                <div class="col-lg-5">
                    <div class="shop-panel">
                        <h5 class="fw-bold mb-3">Đơn hàng của bạn</h5>

                        @foreach ($cart['items'] as $item)
                            <div class="d-flex justify-content-between align-items-center mb-2 small">
                                <span class="text-truncate me-2">{{ $item['productName'] }} <span class="text-muted">x
                                        {{ $item['quantity'] }}</span></span>
                                <span class="fw-semibold text-nowrap">{{ format_price($item['subtotal']) }}</span>
                            </div>
                        @endforeach

                        <hr>

                        {{-- Voucher box --}}
                        <div class="mb-3">
                            <label class="shop-form-label">Mã giảm giá</label>
                            <div class="d-flex gap-2">
                                <input type="text" class="shop-input" placeholder="Nhập mã giảm giá" id="voucherInput">
                                <button type="button" class="btn-shop-blue text-nowrap"
                                    id="btnApplyVoucher">{{ icon('tag', null, 'me-1') }} Áp dụng</button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 small d-none"
                                id="voucherApplied">
                                <span class="text-success">Đã áp dụng mã <strong id="voucherAppliedCode"></strong></span>
                                <button type="button" class="btn btn-link text-danger p-0 text-decoration-none small"
                                    id="btnRemoveVoucher">Xóa mã</button>
                            </div>
                        </div>

                        <hr>

                        <div class="summary-row"><span>Tạm
                                tính</span><span>{{ format_price($cart['totalAmount']) }}</span>
                        </div>
                        <div class="summary-row text-success d-none" id="discountRow"><span>Giảm giá</span><span
                                id="discountValue"></span></div>
                        <div class="summary-total"><span>Tổng thanh toán</span><span
                                id="grandTotal">{{ format_price($cart['totalAmount']) }}</span></div>

                        <button type="submit" class="btn-shop-orange w-100 mt-3" id="btnSubmitOrder"
                            data-loading-text="Đang xử lý...">ĐẶT HÀNG</button>
                    </div>
                </div>
            </div>
        </form>
    @endif
@endsection

@push('scripts')
    @if (!empty($cart['items']))
        <script>
            (function () {
                var totalAmount = {{ (float) $cart['totalAmount'] }};
                var VOUCHER_URL = @json(route('checkout.voucher'));
                var form = document.getElementById('checkoutForm');
                var input = document.getElementById('voucherInput');
                var applyBtn = document.getElementById('btnApplyVoucher');
                var codeField = document.getElementById('voucherCodeField');
                var submitBtn = document.getElementById('btnSubmitOrder');

                function setVoucher(v) {
                    var applied = !!v;
                    codeField.value = applied ? v.code : '';
                    input.disabled = applied;
                    applyBtn.disabled = applied;
                    document.getElementById('voucherApplied').classList.toggle('d-none', !applied);
                    document.getElementById('discountRow').classList.toggle('d-none', !applied);
                    if (applied) {
                        document.getElementById('voucherAppliedCode').textContent = v.code;
                        document.getElementById('discountValue').textContent = '▼ -' + formatPrice(v.discountValue);
                    }
                    document.getElementById('grandTotal').textContent = formatPrice(applied ? v.finalAmount : totalAmount);
                }

                applyBtn.addEventListener('click', function () {
                    var code = input.value.trim();
                    if (!code) { toast.warning('Vui lòng nhập mã giảm giá.'); return; }
                    applyBtn.disabled = true;
                    api(VOUCHER_URL, { method: 'POST', data: { code: code, orderAmount: totalAmount } })
                        .then(function (res) {
                            if (res && res.success && res.data && res.data.valid) {
                                setVoucher(res.data);
                                toast.success(res.message || 'Áp dụng mã giảm giá thành công.');
                            } else {
                                setVoucher(null);
                                toast.error((res && res.message) || 'Mã giảm giá không hợp lệ.');
                            }
                        })
                        .catch(function (err) { setVoucher(null); toast.error(err.message || 'Không thể áp dụng mã giảm giá.'); })
                        .finally(function () { if (!codeField.value) applyBtn.disabled = false; });
                });

                document.getElementById('btnRemoveVoucher').addEventListener('click', function () {
                    setVoucher(null);
                    input.value = '';
                });

                // Highlight phương thức thanh toán + đổi nhãn nút
                function syncPm() {
                    document.querySelectorAll('.pm-option').forEach(function (l) {
                        l.classList.toggle('active', l.querySelector('input').checked);
                    });
                    var vn = document.getElementById('pm-vnpay').checked;
                    submitBtn.textContent = vn ? 'ĐẶT HÀNG & THANH TOÁN VNPAY' : 'ĐẶT HÀNG';
                }
                document.querySelectorAll('input[name="payment_method"]').forEach(function (r) { r.addEventListener('change', syncPm); });
                syncPm();

                form.addEventListener('submit', function (e) {
                    if (!form.full_name.value.trim() || !form.phone.value.trim() || !form.shipping_address.value.trim()) {
                        e.preventDefault();
                        toast.warning('Vui lòng điền đầy đủ thông tin giao hàng.');
                    }
                });
            })();
        </script>
    @endif
@endpush