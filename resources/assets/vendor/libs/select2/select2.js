import $ from 'jquery';
import select2 from 'select2';

// Ensure jQuery has select2 bound
try {
  select2(window, $);
} catch (e) {
  // Already initialized or bound
}

window.jQuery = window.$ = $;

/**
 * Universal Select2 Initializer for Sneat / Bootstrap 5
 * Automatically handles dropdownParent for Modals and Offcanvases, RTL, and language strings.
 */
export function initSelect2(container = document) {
  const isRtl = $('html').attr('dir') === 'rtl' || $('html').attr('lang') === 'ar';
  const noResultsText = isRtl ? 'لا توجد نتائج مطابقة' : 'No results found';
  const searchingText = isRtl ? 'جاري البحث...' : 'Searching...';

  $(container).find('select.select2, select[data-control="select2"]').each(function () {
    const $el = $(this);
    if ($el.hasClass('select2-hidden-accessible')) {
      return; // Already initialized
    }

    // Never initialize Select2 on DataTables length menu dropdown
    if ($el.closest('.dt-length, .dataTables_length').length || $el.hasClass('dt-input') || ($el.attr('name') && $el.attr('name').includes('_length'))) {
      return;
    }

    // Modal or Offcanvas parent to prevent focus trap issue
    const $parentModal = $el.closest('.modal, .offcanvas');
    const dropdownParent = $parentModal.length ? $parentModal : null;
    const placeholder = $el.data('placeholder') || $el.find('option:first').text() || '';
    // allowClear defaults to true; set data-allow-clear="false" to disable
    const allowClear = $el.data('allow-clear') !== false;

    $el.select2({
      theme: 'bootstrap-5',
      dropdownParent: dropdownParent,
      placeholder: placeholder,
      allowClear: allowClear,
      dir: isRtl ? 'rtl' : 'ltr',
      width: '100%',
      language: {
        noResults: () => noResultsText,
        searching: () => searchingText
      }
    });
  });
}

// Auto-initialize on DOM ready and whenever a Modal or Offcanvas is shown
if (typeof document !== 'undefined') {
  document.addEventListener('DOMContentLoaded', () => {
    initSelect2();
  });

  $(document).on('show.bs.modal show.bs.offcanvas shown.bs.modal shown.bs.offcanvas', function (e) {
    initSelect2(e.target);
  });
}

window.initSelect2 = initSelect2;
export default select2;
