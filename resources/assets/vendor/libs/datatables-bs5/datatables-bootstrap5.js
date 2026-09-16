import $ from 'jquery';
import DataTable from 'datatables.net-bs5';

try {
  DataTable(window, $);
} catch (e) {
  // Already initialized
}

window.DataTable = DataTable;
window.jQuery = window.$ = $;

// Global DataTables Defaults
if ($.fn && $.fn.dataTable) {
  $.extend(true, $.fn.dataTable.defaults, {
    processing: true,
    responsive: true,
    pagingType: 'simple_numbers',
    dom:
      "<'row mx-0 px-4 pt-4 pb-3 align-items-center justify-content-between g-3'<'col-auto'l><'col-12 col-md-7 col-lg-6'f>>" +
      "<'table-responsive'tr>" +
      "<'row mx-0 px-4 py-3 border-top align-items-center justify-content-between'<'col-auto'i><'col-auto'p>>",
    language: {
      sProcessing: '',
      sLengthMenu: '_MENU_',
      lengthMenu: '_MENU_',
      sZeroRecords: 'لم يعثر على أي نتائج مطابقة',
      sInfo: 'عرض _START_ إلى _END_ من _TOTAL_ سجل',
      sInfoEmpty: 'عرض 0 إلى 0 من 0 سجل',
      sInfoFiltered: '(تمت التصفية من _MAX_ سجل)',
      sSearch: '',
      sSearchPlaceholder: 'بحث...',
      oPaginate: {
        sPrevious: '<i class="icon-base bx bx-chevron-left"></i>',
        sNext: '<i class="icon-base bx bx-chevron-right"></i>'
      }
    }
  });

  // Global listener: Animated Linear Progress Bar directly under thead
  $(document).on('processing.dt', function (e, settings, processing) {
    try {
      const table = $(settings.nTable);
      if (!table.length) return;

      const thead = table.find('thead');
      const container = table.parent();

      if (container.css('position') === 'static') {
        container.css('position', 'relative');
      }

      let loader = container.children('.dt-header-loader');
      if (!loader.length) {
        loader = $('<div class="dt-header-loader"><div class="dt-header-loader-bar"></div></div>');
        table.before(loader);
      }

      // Calculate exact bottom position of thead
      const theadPos = thead.position();
      const theadHeight = thead.outerHeight() || 42;
      const topPos = theadPos ? (theadPos.top + theadHeight) : theadHeight;

      loader.css({
        top: topPos + 'px',
        width: (table.outerWidth() || container.width()) + 'px'
      });

      if (processing) {
        table.addClass('is-dt-loading');
        loader.addClass('is-loading');
      } else {
        table.removeClass('is-dt-loading');
        loader.removeClass('is-loading');
      }
    } catch (err) {
      // Ignore
    }
  });
}

export default DataTable;
export { DataTable };
