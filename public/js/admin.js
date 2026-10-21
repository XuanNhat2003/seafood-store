/* Admin layout JS: sidebar submenu, thống kê nhanh trên header (cập nhật mỗi 30s) */
(function () {
  'use strict';

  // Modal đặt trong .admin-content-body (có animation transform => stacking context) sẽ bị
  // .modal-backdrop che. Chuyển tất cả modal ra <body> giống react-bootstrap (portal).
  document.querySelectorAll('.admin-content-body .modal').forEach(function (m) {
    document.body.appendChild(m);
  });

  var toggle = document.getElementById('ordersToggle');
  var sub = document.getElementById('ordersSubmenu');
  var arrow = document.getElementById('ordersArrow');
  if (toggle && sub) {
    toggle.addEventListener('click', function () {
      var collapsed = sub.classList.toggle('collapsed');
      arrow.innerHTML = feather.icons[collapsed ? 'chevron-right' : 'chevron-down']
        .toSvg({ width: '1em', height: '1em', class: 'collapse-arrow' });
    });
  }

  function fetchStats() {
    if (!window.ADMIN || !window.ADMIN.quickStatsUrl) return;
    api(window.ADMIN.quickStatsUrl).then(function (s) {
      var pending = s.pendingOrders || 0, unread = s.unreadContacts || 0;
      document.querySelectorAll('[data-qs="pendingOrders"]').forEach(function (e) { e.textContent = pending; });
      document.querySelectorAll('[data-qs="unreadContacts"]').forEach(function (e) { e.textContent = unread; });
      var badge = document.getElementById('notiCount');
      if (badge) {
        var count = pending + unread;
        badge.textContent = count;
        badge.style.display = count > 0 ? '' : 'none';
      }
    }).catch(function (e) { console.error('Failed to fetch notification count', e); });
  }
  fetchStats();
  setInterval(fetchStats, 30000);

  /**
   * Helper cho các form trong modal admin: submit qua AJAX, toast kết quả,
   * thành công => reload trang (giữ query string) và hiện toast sau reload.
   *   ajaxForm(formEl, { successMessage, onSuccess })
   */
  window.flashAfterReload = function (type, msg) {
    try { sessionStorage.setItem('__flash', JSON.stringify({ type: type, msg: msg })); } catch (e) {}
  };
  try {
    var f = sessionStorage.getItem('__flash');
    if (f) {
      sessionStorage.removeItem('__flash');
      f = JSON.parse(f);
      document.addEventListener('DOMContentLoaded', function () { toast[f.type](f.msg); });
    }
  } catch (e) {}

  window.reloadWithToast = function (msg, type) {
    flashAfterReload(type || 'success', msg);
    window.location.reload();
  };
})();
