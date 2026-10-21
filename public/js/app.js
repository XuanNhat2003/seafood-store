/* =====================================================================
 * Hải Sản Tươi Ngon — JS dùng chung (thay cho React context/toastify)
 *  - toast.success/error/info/warning  (giao diện y hệt react-toastify)
 *  - confirmDialog(message, title, type) -> Promise<boolean>
 *  - api(url, {method, data})            fetch JSON có CSRF
 *  - form[data-confirm]                  hỏi xác nhận trước khi submit
 *  - [data-image-upload]                 ô chọn ảnh: tải lên hoặc dán URL
 * ===================================================================== */
(function () {
  'use strict';

  var CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  var BASE = (document.querySelector('meta[name="base-url"]') || {}).content || '';

  function esc(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }
  window.esc = esc;

  /* ---------------------------------------------------------------
   * Định dạng (giống utils/format.js)
   * ------------------------------------------------------------- */
  window.formatPrice = function (price) {
    if (!price && price !== 0) return '0 ₫';
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
  };
  window.formatCurrency = window.formatPrice;

  /* ---------------------------------------------------------------
   * Toast (react-toastify v10 markup, position top-right, Slide, 3s)
   * ------------------------------------------------------------- */
  var ICONS = {
    info: 'M12 0a12 12 0 1012 12A12.013 12.013 0 0012 0zm.25 5a1.5 1.5 0 11-1.5 1.5 1.5 1.5 0 011.5-1.5zm2.25 13.5h-4a1 1 0 010-2h.75a.25.25 0 00.25-.25v-4.5a.25.25 0 00-.25-.25h-.75a1 1 0 010-2h1a2 2 0 012 2v4.75a.25.25 0 00.25.25h.75a1 1 0 110 2z',
    warning: 'M23.32 17.191L15.438 2.184C14.728.833 13.416 0 11.996 0c-1.42 0-2.733.833-3.443 2.184L.533 17.448a4.744 4.744 0 000 4.368C1.243 23.167 2.555 24 3.975 24h16.05C22.22 24 24 22.044 24 19.632c0-.904-.251-1.746-.68-2.44zm-9.622 1.46c0 1.033-.724 1.823-1.698 1.823s-1.698-.79-1.698-1.822v-.043c0-1.028.724-1.822 1.698-1.822s1.698.79 1.698 1.822v.043zm.039-12.285l-.84 8.06c-.057.581-.408.943-.897.943-.49 0-.84-.367-.896-.942l-.84-8.065c-.057-.624.25-1.095.779-1.095h1.91c.528.005.84.476.784 1.1z',
    success: 'M12 0a12 12 0 1012 12A12.014 12.014 0 0012 0zm6.927 8.2l-6.845 9.289a1.011 1.011 0 01-1.43.188l-4.888-3.908a1 1 0 111.25-1.562l4.076 3.261 6.227-8.451a1 1 0 111.61 1.183z',
    error: 'M11.983 0a12.206 12.206 0 00-8.51 3.653A11.8 11.8 0 000 12.207 11.779 11.779 0 0011.8 24h.214A12.111 12.111 0 0024 11.791 11.766 11.766 0 0011.983 0zM10.5 16.542a1.476 1.476 0 011.449-1.53h.027a1.527 1.527 0 011.523 1.47 1.475 1.475 0 01-1.449 1.53h-.027a1.529 1.529 0 01-1.523-1.47zM11 12.5v-6a1 1 0 012 0v6a1 1 0 11-2 0z'
  };
  var AUTO_CLOSE = 3000;

  function getContainer() {
    var root = document.querySelector('.Toastify');
    if (!root) {
      root = document.createElement('div');
      root.className = 'Toastify';
      document.body.appendChild(root);
    }
    var c = root.querySelector('.Toastify__toast-container');
    if (!c) {
      c = document.createElement('div');
      c.className = 'Toastify__toast-container Toastify__toast-container--top-right';
      root.appendChild(c);
    }
    return c;
  }

  function showToast(type, message) {
    if (message == null || message === '') return;
    var c = getContainer();
    var el = document.createElement('div');
    el.className = 'Toastify__toast Toastify__toast-theme--light Toastify__toast--' + type +
      ' Toastify__toast--close-on-click Toastify--animate Toastify__slide-enter--top-right';
    el.innerHTML =
      '<div role="alert" class="Toastify__toast-body">' +
        '<div class="Toastify__toast-icon Toastify--animate-icon Toastify__zoom-enter">' +
          '<svg viewBox="0 0 24 24" width="100%" height="100%" fill="var(--toastify-icon-color-' + type + ')"><path d="' + ICONS[type] + '"></path></svg>' +
        '</div>' +
        '<div>' + esc(message) + '</div>' +
      '</div>' +
      '<button class="Toastify__close-button Toastify__close-button--light" type="button" aria-label="close">' +
        '<svg aria-hidden="true" viewBox="0 0 14 16"><path fill-rule="evenodd" d="M7.71 8.23l3.75 3.75-1.48 1.48-3.75-3.75-3.75 3.75L1 11.98l3.75-3.75L1 4.48 2.48 3l3.75 3.75L9.98 3l1.48 1.48-3.75 3.75z"></path></svg>' +
      '</button>' +
      '<div class="Toastify__progress-bar--wrp" data-hidden="false">' +
        '<div class="Toastify__progress-bar--bg Toastify__progress-bar-theme--light Toastify__progress-bar--' + type + '"></div>' +
        '<div role="progressbar" aria-hidden="false" aria-label="notification timer" class="Toastify__progress-bar Toastify__progress-bar--animated Toastify__progress-bar-theme--light Toastify__progress-bar--' + type + '" style="animation-duration:' + AUTO_CLOSE + 'ms;animation-play-state:running"></div>' +
      '</div>';

    // newestOnTop
    c.insertBefore(el, c.firstChild);

    var closed = false;
    function close() {
      if (closed) return;
      closed = true;
      el.classList.remove('Toastify__slide-enter--top-right');
      el.classList.add('Toastify--animate', 'Toastify__slide-exit--top-right');
      var done = function () {
        el.style.height = el.scrollHeight + 'px';
        el.style.transition = 'all 300ms';
        requestAnimationFrame(function () {
          el.style.height = '0'; el.style.padding = '0'; el.style.margin = '0';
          setTimeout(function () { el.remove(); }, 300);
        });
      };
      el.addEventListener('animationend', done, { once: true });
      setTimeout(function () { if (el.isConnected) done(); }, 800);
    }
    el.addEventListener('animationend', function (e) {
      if (e.target === el) el.classList.remove('Toastify--animate', 'Toastify__slide-enter--top-right');
    }, { once: true });

    var bar = el.querySelector('.Toastify__progress-bar');
    bar.addEventListener('animationend', close);
    el.addEventListener('mouseenter', function () { bar.style.animationPlayState = 'paused'; });
    el.addEventListener('mouseleave', function () { bar.style.animationPlayState = 'running'; });
    el.addEventListener('click', close);
  }

  window.toast = {
    success: function (m) { showToast('success', m); },
    error: function (m) { showToast('error', m); },
    info: function (m) { showToast('info', m); },
    warning: function (m) { showToast('warning', m); },
    warn: function (m) { showToast('warning', m); }
  };

  /* ---------------------------------------------------------------
   * Confirm dialog (port ConfirmContext.jsx)
   * ------------------------------------------------------------- */
  var confirmEl = null, confirmModal = null, confirmResolve = null;

  function featherSvg(name, size, color) {
    if (window.feather && feather.icons[name]) {
      return feather.icons[name].toSvg({ width: size, height: size, style: 'color:' + color });
    }
    return '';
  }

  window.confirmDialog = function (message, title, type) {
    title = title || 'Xác nhận hành động';
    type = type || 'primary';
    var map = {
      danger: ['trash-2', '#ef4444', 'rgba(239, 68, 68, 0.08)', 'rgba(239, 68, 68, 0.2)'],
      deactivate: ['power', '#ef4444', 'rgba(239, 68, 68, 0.08)', 'rgba(239, 68, 68, 0.2)'],
      warning: ['alert-triangle', '#f59e0b', 'rgba(245, 158, 11, 0.08)', 'rgba(245, 158, 11, 0.2)'],
      success: ['check-circle', '#10b981', 'rgba(16, 185, 129, 0.08)', 'rgba(16, 185, 129, 0.2)'],
      info: ['info', '#0ea5e9', 'rgba(14, 165, 233, 0.08)', 'rgba(14, 165, 233, 0.2)'],
      primary: ['help-circle', '#2563eb', 'rgba(37, 99, 235, 0.08)', 'rgba(37, 99, 235, 0.2)']
    };
    var m = map[type] || map.primary;
    var isDanger = type === 'danger' || type === 'deactivate';

    if (!confirmEl) {
      confirmEl = document.createElement('div');
      confirmEl.className = 'modal fade';
      confirmEl.tabIndex = -1;
      confirmEl.style.zIndex = 1060;
      confirmEl.innerHTML =
        '<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content modal-content-custom">' +
        '<div class="modal-body p-4 text-center">' +
          '<div class="cf-icon mx-auto d-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;border-radius:50%;box-shadow:inset 0 2px 4px rgba(0,0,0,0.02)"></div>' +
          '<h5 class="cf-title fw-bold mb-2 font-display" style="font-size:16.5px;color:var(--text-dark)"></h5>' +
          '<p class="cf-message text-muted mb-4 px-1" style="font-size:13px;line-height:1.6"></p>' +
          '<div class="d-flex gap-2 justify-content-center">' +
            '<button type="button" class="cf-cancel btn btn-light w-50 py-2 border-0 fw-semibold" style="height:40px;border-radius:10px;background-color:#f1f5f9;color:#475569;font-size:13px;transition:all 0.15s ease">Hủy bỏ</button>' +
            '<button type="button" class="cf-ok btn text-white w-50 py-2 fw-semibold" style="height:40px;border-radius:10px;border:none;font-size:13px;transition:all 0.15s ease">Đồng ý</button>' +
          '</div>' +
        '</div></div></div>';
      document.body.appendChild(confirmEl);
      confirmModal = new bootstrap.Modal(confirmEl);
      confirmEl.querySelector('.cf-cancel').addEventListener('click', function () { confirmModal.hide(); });
      confirmEl.querySelector('.cf-ok').addEventListener('click', function () {
        var r = confirmResolve; confirmResolve = null;
        confirmModal.hide();
        if (r) r(true);
      });
      confirmEl.addEventListener('hidden.bs.modal', function () {
        var r = confirmResolve; confirmResolve = null;
        if (r) r(false);
      });
    }

    var ic = confirmEl.querySelector('.cf-icon');
    ic.style.backgroundColor = m[2];
    ic.style.border = '2px solid ' + m[3];
    ic.innerHTML = featherSvg(m[0], 26, m[1]);
    confirmEl.querySelector('.cf-title').textContent = title;
    confirmEl.querySelector('.cf-message').textContent = message;
    var ok = confirmEl.querySelector('.cf-ok');
    ok.style.background = isDanger ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : 'var(--accent-gradient)';
    ok.style.boxShadow = isDanger ? '0 4px 12px rgba(239, 68, 68, 0.2)' : '0 4px 12px rgba(14, 165, 233, 0.2)';

    return new Promise(function (resolve) {
      confirmResolve = resolve;
      confirmModal.show();
    });
  };

  /* ---------------------------------------------------------------
   * Fetch JSON có CSRF
   * ------------------------------------------------------------- */
  window.api = function (url, opts) {
    opts = opts || {};
    var method = (opts.method || 'GET').toUpperCase();
    var headers = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF };
    var body;
    if (opts.formData) {
      body = opts.formData;
    } else if (opts.data !== undefined) {
      headers['Content-Type'] = 'application/json';
      body = JSON.stringify(opts.data);
    }
    return fetch(url, { method: method, headers: headers, body: body, credentials: 'same-origin' })
      .then(function (res) {
        return res.text().then(function (txt) {
          var json = null;
          try { json = txt ? JSON.parse(txt) : {}; } catch (e) { json = { message: txt }; }
          if (res.status === 401 && json && json.redirect) {
            window.location.href = json.redirect;
          }
          if (!res.ok) {
            var msg = (json && json.message) || 'Đã có lỗi xảy ra.';
            if (json && json.errors) {
              var first = Object.values(json.errors)[0];
              if (first && first[0]) msg = first[0];
            }
            if (res.status === 419) msg = 'Phiên làm việc đã hết hạn, vui lòng tải lại trang.';
            var err = new Error(msg);
            err.status = res.status; err.data = json;
            throw err;
          }
          return json;
        });
      });
  };

  /* ---------------------------------------------------------------
   * form[data-confirm="message"] data-confirm-title data-confirm-type
   * ------------------------------------------------------------- */
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.matches || !form.matches('form[data-confirm]')) return;
    if (form.dataset.confirmed === '1') return;
    e.preventDefault();
    confirmDialog(form.dataset.confirm, form.dataset.confirmTitle, form.dataset.confirmType).then(function (ok) {
      if (!ok) return;
      form.dataset.confirmed = '1';
      if (form.requestSubmit) form.requestSubmit(); else form.submit();
      setTimeout(function () { form.dataset.confirmed = ''; }, 500);
    });
  }, true);

  // Chống submit 2 lần + hiển thị trạng thái "đang xử lý"
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (e.defaultPrevented || !form.matches) return;
    var btn = form.querySelector('[data-loading-text]');
    if (btn) {
      setTimeout(function () {
        btn.disabled = true;
        btn.dataset.originalHtml = btn.innerHTML;
        btn.innerHTML = btn.dataset.loadingText;
      }, 0);
    }
  });

  /* ---------------------------------------------------------------
   * Image upload field (port ImageUploadField.jsx)
   *   <div data-image-upload data-name="image_url" data-value="..." data-label="Hình ảnh" data-height="150">
   * ------------------------------------------------------------- */
  function initImageUpload(root) {
    if (root.dataset.inited) return;
    root.dataset.inited = '1';
    var name = root.dataset.name || 'image_url';
    var label = root.dataset.label === undefined ? 'Hình ảnh' : root.dataset.label;
    var height = parseInt(root.dataset.height || '150', 10);
    var uploadUrl = root.dataset.uploadUrl || (BASE + '/admin/upload');

    root.innerHTML =
      (label ? '<label class="form-label">' + esc(label) + '</label>' : '') +
      '<div class="image-upload-box"></div>' +
      '<input type="file" accept="image/*" class="d-none iu-file">' +
      '<input type="hidden" class="iu-value" name="' + esc(name) + '">' +
      '<div class="d-flex gap-2 mt-2">' +
        '<button type="button" class="btn btn-sm btn-outline-primary flex-fill iu-pick">' + featherSvg('upload-cloud', 14, 'currentColor') + ' Tải ảnh lên</button>' +
        '<button type="button" class="btn btn-sm btn-outline-secondary flex-fill iu-toggle-url">' + featherSvg('link', 14, 'currentColor') + ' Dán URL</button>' +
      '</div>' +
      '<input type="text" class="form-control mt-2 iu-url d-none" placeholder="https://...">';

    var box = root.querySelector('.image-upload-box');
    var file = root.querySelector('.iu-file');
    var hidden = root.querySelector('.iu-value');
    var urlInput = root.querySelector('.iu-url');
    var pickBtn = root.querySelector('.iu-pick');
    var uploading = false;

    function displayUrl(v) {
      if (!v) return '';
      if (/^(https?:)?\/\//i.test(v) || v.indexOf('data:') === 0) return v;
      return BASE + '/' + v.replace(/^\/+/, '');
    }

    function render() {
      var v = hidden.value;
      if (v) {
        box.innerHTML = '<div class="image-upload-preview"><img src="' + esc(displayUrl(v)) + '" alt="preview" style="max-height:' + height + 'px">' +
          '<button type="button" class="image-upload-remove" title="Xóa ảnh">' + featherSvg('x', 16, 'currentColor') + '</button></div>';
        box.querySelector('.image-upload-remove').addEventListener('click', function () { set(''); });
      } else {
        box.innerHTML = '<div class="image-upload-drop" style="height:' + height + 'px">' +
          (uploading
            ? '<span class="spinner-border spinner-border-sm text-primary mb-2" role="status"></span><span class="text-muted small">Đang tải lên...</span>'
            : featherSvg('upload-cloud', 28, '').replace('<svg', '<svg class="text-primary mb-2"') +
              '<span class="fw-semibold" style="font-size:13px">Nhấn để chọn ảnh</span>' +
              '<span class="text-muted" style="font-size:11px">JPG, PNG, WEBP — tối đa 5MB</span>') +
          '</div>';
        box.querySelector('.image-upload-drop').addEventListener('click', function () { file.click(); });
      }
    }

    function set(v) {
      hidden.value = v || '';
      urlInput.value = v || '';
      render();
      root.dispatchEvent(new CustomEvent('image-change', { detail: hidden.value, bubbles: true }));
    }

    root.setValue = set;
    root.getValue = function () { return hidden.value; };

    pickBtn.addEventListener('click', function () { file.click(); });
    root.querySelector('.iu-toggle-url').addEventListener('click', function () { urlInput.classList.toggle('d-none'); });
    // Chỉ xem trước khi ngừng gõ 400ms (tránh tải ảnh với URL gõ dở)
    var urlTimer = null;
    urlInput.addEventListener('input', function () {
      hidden.value = urlInput.value;
      clearTimeout(urlTimer);
      urlTimer = setTimeout(render, 400);
    });

    file.addEventListener('change', function () {
      var f = file.files && file.files[0];
      if (!f) return;
      if (!f.type || f.type.indexOf('image/') !== 0) {
        toast.warning('Vui lòng chọn tệp hình ảnh hợp lệ.');
        file.value = '';
        return;
      }
      var fd = new FormData();
      fd.append('file', f);
      uploading = true; pickBtn.disabled = true; render();
      api(uploadUrl, { method: 'POST', formData: fd })
        .then(function (res) { uploading = false; set(res.url); toast.success('Tải ảnh lên thành công!'); })
        .catch(function (err) { uploading = false; render(); toast.error(err.message || 'Tải ảnh thất bại!'); })
        .finally(function () { pickBtn.disabled = false; file.value = ''; });
    });

    set(root.dataset.value || '');
  }
  window.initImageUploads = function (scope) {
    (scope || document).querySelectorAll('[data-image-upload]').forEach(initImageUpload);
  };

  /* ---------------------------------------------------------------
   * Khởi tạo
   * ------------------------------------------------------------- */
  document.addEventListener('DOMContentLoaded', function () {
    window.initImageUploads();
    // Thông báo flash từ server (session)
    var flash = window.__FLASH || {};
    ['success', 'error', 'info', 'warning'].forEach(function (k) {
      if (flash[k]) {
        (Array.isArray(flash[k]) ? flash[k] : [flash[k]]).forEach(function (m) { showToast(k, m); });
      }
    });
  });
})();
