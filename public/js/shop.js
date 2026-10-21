/* Storefront JS: dropdown tài khoản, thêm vào giỏ hàng (thay CartContext) */
(function () {
  'use strict';

  // ── Dropdown tài khoản ở header ──
  var btn = document.getElementById('userMenuBtn');
  var menu = document.getElementById('userMenu');
  if (btn && menu) {
    btn.addEventListener('click', function (e) { e.stopPropagation(); menu.classList.toggle('show'); });
    document.addEventListener('mousedown', function (e) {
      var wrap = document.getElementById('userMenuWrap');
      if (wrap && !wrap.contains(e.target)) menu.classList.remove('show');
    });
  }

  // ── Mega menu: chạm để mở/đóng (thiết bị cảm ứng không có hover) ──
  var catBtn = document.querySelector('.shop-nav-cat');
  var mega = document.querySelector('.shop-mega');
  if (catBtn && mega) {
    catBtn.addEventListener('click', function (e) { e.stopPropagation(); mega.classList.toggle('show'); });
    document.addEventListener('click', function (e) {
      if (!e.target.closest('.shop-nav-cat-wrap')) mega.classList.remove('show');
    });
  }

  // ── Mega menu: đóng khi click một mục ──
  document.querySelectorAll('.shop-mega a').forEach(function (a) {
    a.addEventListener('click', function () {
      var m = a.closest('.shop-mega');
      m.classList.remove('show');
      m.style.display = 'none';
      setTimeout(function () { m.style.display = ''; }, 300);
    });
  });

  window.updateCartBadge = function (count) {
    var b = document.getElementById('cartBadge');
    if (!b) return;
    b.textContent = count;
    b.style.display = count > 0 ? '' : 'none';
  };

  /**
   * Thêm vào giỏ hàng. Trả về Promise<{ok, needLogin?, message?}>
   * (giống CartContext.addItem)
   */
  window.addToCart = function (productId, quantity) {
    quantity = quantity || 1;
    if (!window.SHOP.isAuthenticated) {
      return Promise.resolve({ ok: false, needLogin: true });
    }
    return api(window.SHOP.cartAddUrl, { method: 'POST', data: { productId: productId, quantity: quantity } })
      .then(function (cart) {
        updateCartBadge(cart.totalItems || 0);
        return { ok: true, cart: cart };
      })
      .catch(function (err) {
        if (err.status === 401) return { ok: false, needLogin: true };
        return { ok: false, message: err.message || 'Không thể thêm vào giỏ hàng.' };
      });
  };

  window.handleAddResult = function (res) {
    if (res.needLogin) {
      toast.info('Vui lòng đăng nhập để mua hàng.');
      setTimeout(function () { window.location.href = window.SHOP.loginUrl; }, 600);
      return false;
    }
    if (res.ok) { toast.success('Đã thêm vào giỏ hàng!'); return true; }
    toast.error(typeof res.message === 'string' ? res.message : 'Không thể thêm vào giỏ hàng.');
    return false;
  };

  // ── Nút "Thêm vào giỏ hàng" trên ProductCard ──
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-add-to-cart]');
    if (!b) return;
    e.preventDefault();
    if (b.disabled) return;
    b.disabled = true;
    addToCart(parseInt(b.dataset.addToCart, 10), 1)
      .then(handleAddResult)
      .finally(function () { b.disabled = false; });
  });
})();
