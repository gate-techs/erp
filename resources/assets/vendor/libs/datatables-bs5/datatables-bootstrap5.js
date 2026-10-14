import $ from 'jquery';
import DataTable from 'datatables.net-bs5';

try {
  DataTable(window, $);
} catch (e) {
  // Already initialized
}

window.DataTable = DataTable;
window.jQuery = window.$ = $;

// Locale-aware texts, picked from <html lang="...">
const dtTranslations = {
  ar: {
    sEmptyTable: 'لا توجد بيانات متاحة',
    sZeroRecords: 'لم يعثر على أي نتائج مطابقة',
    sInfo: 'عرض _START_ إلى _END_ من _TOTAL_ سجل',
    sInfoEmpty: 'عرض 0 إلى 0 من 0 سجل',
    sInfoFiltered: '(تمت التصفية من _MAX_ سجل)',
    sSearchPlaceholder: 'بحث...'
  },
  en: {
    sEmptyTable: 'No data available in table',
    sZeroRecords: 'No matching records found',
    sInfo: 'Showing _START_ to _END_ of _TOTAL_ entries',
    sInfoEmpty: 'Showing 0 to 0 of 0 entries',
    sInfoFiltered: '(filtered from _MAX_ total entries)',
    sSearchPlaceholder: 'Search...'
  }
};
const dtLocale = (document.documentElement.lang || 'en').split('-')[0].toLowerCase();
const dtTexts = dtTranslations[dtLocale] || dtTranslations.en;

// Global DataTables Defaults
if ($.fn && $.fn.dataTable) {
  $.extend(true, $.fn.dataTable.defaults, {
    processing: true,
    responsive: true,
    pagingType: 'simple_numbers',
    dom:
      "<'row dt-header-row mx-0 px-4 pt-4 pb-3 align-items-center justify-content-between g-3'<'col-auto'l><'col-12 col-md-7 col-lg-6'f>>" +
      "<'table-responsive'tr>" +
      "<'row dt-footer-row mx-0 px-4 py-3 border-top align-items-center justify-content-between'<'col-auto'i><'col-auto'p>>",
    language: {
      sProcessing: '',
      sLengthMenu: '_MENU_',
      lengthMenu: '_MENU_',
      sSearch: '',
      ...dtTexts,
      oPaginate: {
        sPrevious: '<i class="icon-base bx bx-chevron-left"></i>',
        sNext: '<i class="icon-base bx bx-chevron-right"></i>'
      }
    }
  });

  // Unified pagination look on every table: some pages override the labels with
  // text / « » / manually RTL-swapped icons. Labels are read on each draw, so
  // normalising them before the first draw is enough (RTL mirroring is done in CSS).
  $(document).on('preInit.dt', function (e, settings) {
    if (settings && settings.language && settings.language.paginate) {
      settings.language.paginate.previous = '<i class="icon-base bx bx-chevron-left"></i>';
      settings.language.paginate.next = '<i class="icon-base bx bx-chevron-right"></i>';
    }
    ensureOwnTableScroller(settings);

    // "processing" does not bubble to document in DataTables 3 (preInit/init do), so bind per table.
    // preInit fires before the first ajax request, so the initial load gets the spinner too.
    const tableEl = settings && (settings.table || settings.nTable);
    if (tableEl && !tableEl.dataset.dtLoaderBound) {
      tableEl.dataset.dtLoaderBound = '1';
      $(tableEl).on('processing.dt', function (ev, s, processing) {
        toggleBodyLoader(s, processing);
      });
    }
  });

  // Only the table scrolls horizontally; search / length / info / pagination stay fixed.
  // The global dom wraps the table in .table-responsive, but pages with a custom dom put the
  // table directly in the DataTables container, so an outer wrapper scrolled everything
  // (pagination included). Give such tables their own scroller.
  function ensureOwnTableScroller(settings) {
    // DataTables 2+/3 renamed nTable/nTableWrapper to table/tableWrapper
    const table = settings && (settings.table || settings.nTable);
    const container = settings && (settings.tableWrapper || settings.nTableWrapper);
    if (!table || !container || $(table).closest('.dt-scroll').length) return;
    if (table.parentNode === container) {
      $(table).wrap('<div class="table-responsive"></div>');
    }
  }
  $(document).on('init.dt', function (e, settings) {
    ensureOwnTableScroller(settings);
  });

  // Global listener: circular spinner shown in place of the table data while loading
  // (rows fade out underneath; styles: .dt-body-loader in resources/css/app.css)
  const LOADER_MIN_BODY = 140; // px of data area kept while loading so the spinner has room

  function toggleBodyLoader(settings, processing) {
    try {
      const tableEl = settings && (settings.table || settings.nTable);
      const container = settings && (settings.tableWrapper || settings.nTableWrapper);
      if (!tableEl || !container) return;

      const $container = $(container);
      const scroller = tableEl.parentElement; // table's own horizontal scroller (or the container)

      let loader = $container.children('.dt-body-loader');
      if (!processing) {
        $container.removeClass('dt-is-loading');
        loader.removeClass('is-loading');
        if (scroller !== container) scroller.style.minHeight = '';
        return;
      }

      if ($container.css('position') === 'static') $container.css('position', 'relative');
      if (!loader.length) {
        loader = $('<div class="dt-body-loader" role="status" aria-live="polite"><span class="dt-body-spinner"></span></div>');
        $container.append(loader);
      }

      // Data area = table scroller minus the header row; keep a minimum height for empty tables
      const theadHeight = $(tableEl).children('thead').outerHeight() || 0;
      if (scroller !== container && scroller.offsetHeight - theadHeight < LOADER_MIN_BODY) {
        scroller.style.minHeight = theadHeight + LOADER_MIN_BODY + 'px';
      }
      const containerRect = container.getBoundingClientRect();
      const scrollerRect = scroller.getBoundingClientRect();
      const top = scrollerRect.top - containerRect.top + theadHeight;
      const height = Math.max(scrollerRect.height - theadHeight, LOADER_MIN_BODY);

      // Spinner sits in the middle of short data areas, near the top of tall ones (stays visible)
      loader.css({ top: top + 'px', height: height + 'px', paddingTop: Math.min(height / 2 - 20, 120) + 'px' });
      $container.addClass('dt-is-loading');
      loader.addClass('is-loading');
    } catch (err) {
      // Never break the table because of the loading indicator
    }
  }
}

export default DataTable;
export { DataTable };
