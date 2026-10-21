@extends('layouts.shop')

@section('title', 'Giỏ hàng - Hải Sản Tươi Ngon')

@section('content')
<h2 class="shop-page-title mb-4">Giỏ hàng của bạn</h2>
<div id="cartRoot"></div>
@endsection

@push('scripts')
<script>
(function () {
    var cart = @json($cart);
    var URL_ITEM = @json(url('/gio-hang/items')) + '/';
    var URL_CLEAR = @json(route('cart.clear'));
    var URL_SHOP = @json(url('/san-pham'));
    var URL_CHECKOUT = @json(route('checkout'));
    var root = document.getElementById('cartRoot');
    var ic = function (n, s) { return feather.icons[n].toSvg({ width: s || '1em', height: s || '1em' }); };

    function render() {
        var items = cart.items || [];
        updateCartBadge(cart.totalItems || 0);
        if (!items.length) {
            root.innerHTML =
                '<div class="shop-empty">' + ic('shopping-cart', 56).replace('<svg', '<svg class="mb-3"') +
                '<p class="mb-3">Giỏ hàng trống</p>' +
                '<a href="' + URL_SHOP + '" class="btn-shop-orange">Tiếp tục mua sắm</a></div>';
            return;
        }
        var rows = items.map(function (item) {
            return '<div class="cart-row" data-id="' + item.id + '">' +
                '<img src="' + esc(item.productImage || '') + '" alt="' + esc(item.productName) + '" class="cart-row-img">' +
                '<div class="cart-row-info">' +
                    '<div class="cart-row-name">' + esc(item.productName) + '</div>' +
                    '<div class="cart-row-price text-muted">' + formatPrice(item.salePrice != null ? item.salePrice : item.price) + (item.unit ? ' / ' + esc(item.unit) : '') + '</div>' +
                '</div>' +
                '<div class="qty-stepper">' +
                    '<button type="button" data-act="dec" ' + (item.quantity <= 1 ? 'disabled' : '') + ' aria-label="Giảm">' + ic('minus') + '</button>' +
                    '<input type="number" min="1" value="' + item.quantity + '" data-act="input">' +
                    '<button type="button" data-act="inc" aria-label="Tăng">' + ic('plus') + '</button>' +
                '</div>' +
                '<div class="cart-row-subtotal fw-bold">' + formatPrice(item.subtotal) + '</div>' +
                '<button type="button" class="cart-row-delete btn btn-link text-danger p-0" data-act="remove" aria-label="Xóa">' + ic('trash-2') + '</button>' +
            '</div>';
        }).join('');

        root.innerHTML =
            '<div class="row g-4">' +
              '<div class="col-lg-8"><div class="shop-panel">' +
                '<div class="d-flex justify-content-between align-items-center mb-3">' +
                  '<h5 class="mb-0 fw-bold">Sản phẩm (' + items.length + ')</h5>' +
                  '<button type="button" class="btn btn-link text-danger p-0 text-decoration-none" data-act="clear">' + ic('trash-2').replace('<svg', '<svg class="me-1"') + ' Xóa tất cả</button>' +
                '</div>' + rows +
              '</div></div>' +
              '<div class="col-lg-4"><div class="shop-panel">' +
                '<h5 class="fw-bold mb-3">Tóm tắt đơn hàng</h5>' +
                '<div class="summary-row"><span>Tạm tính</span><span>' + formatPrice(cart.totalAmount) + '</span></div>' +
                '<p class="text-muted small mb-3">Phí vận chuyển và mã giảm giá sẽ được áp dụng ở bước thanh toán.</p>' +
                '<div class="summary-total"><span>Tổng</span><span>' + formatPrice(cart.totalAmount) + '</span></div>' +
                '<a href="' + URL_CHECKOUT + '" class="btn-shop-orange w-100 mt-3">Tiến hành đặt hàng</a>' +
                '<div class="text-center mt-3"><a href="' + URL_SHOP + '" class="text-decoration-none">← Tiếp tục mua sắm</a></div>' +
              '</div></div>' +
            '</div>';
    }

    function findItem(id) { return (cart.items || []).find(function (i) { return i.id === id; }); }

    function changeQty(item, newQty) {
        var q = Math.max(1, Number(newQty) || 1);
        if (q === item.quantity) return;
        api(URL_ITEM + item.id, { method: 'PUT', data: { quantity: q } })
            .then(function (data) { cart = data; render(); })
            .catch(function (err) { toast.error(err.message || 'Cập nhật giỏ hàng thất bại.'); render(); });
    }

    root.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-act]');
        if (!btn || btn.dataset.act === 'input') return;
        var act = btn.dataset.act;
        if (act === 'clear') {
            confirmDialog('Xóa toàn bộ sản phẩm trong giỏ hàng?', 'Xóa tất cả', 'danger').then(function (ok) {
                if (!ok) return;
                api(URL_CLEAR, { method: 'DELETE' }).then(function (data) {
                    cart = data; render(); toast.success('Đã xóa toàn bộ giỏ hàng.');
                });
            });
            return;
        }
        var row = btn.closest('.cart-row');
        var item = findItem(+row.dataset.id);
        if (!item) return;
        if (act === 'dec') changeQty(item, item.quantity - 1);
        if (act === 'inc') changeQty(item, item.quantity + 1);
        if (act === 'remove') {
            confirmDialog('Xóa "' + item.productName + '" khỏi giỏ hàng?', 'Xóa sản phẩm', 'danger').then(function (ok) {
                if (!ok) return;
                api(URL_ITEM + item.id, { method: 'DELETE' })
                    .then(function (data) { cart = data; render(); toast.success('Đã xóa sản phẩm khỏi giỏ hàng.'); })
                    .catch(function () { toast.error('Xóa sản phẩm thất bại.'); });
            });
        }
    });

    root.addEventListener('change', function (e) {
        if (e.target.dataset.act !== 'input') return;
        var item = findItem(+e.target.closest('.cart-row').dataset.id);
        if (item) changeQty(item, e.target.value);
    });

    render();
})();
</script>
@endpush
