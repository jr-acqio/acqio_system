/* Set the defaults for DataTables initialisation */
if ($.fn.dataTable) {
    $.extend(true, $.fn.dataTable.defaults, {
        "sDom": "<'row dt-rt'<'col-sm-6'l><'col-sm-6'f>r>t<'row dt-rb'<'col-sm-4'i><'col-sm-8'p>>",
        "sPaginationType": "bootstrap",
    });
}

function initDataTableHelper() {
    if ($.fn.dataTable) {
        $('[data-provide="datatable"]').each(function () {
            $(this).addClass('dataTable-helper');
            var defaultOptions = {
                paginate: false,
                search: false,
                info: false,
                lengthChange: false,
                displayRows: 10
            },
                dataOptions = $(this).data(),
                helperOptions = $.extend(defaultOptions, dataOptions),
                $thisTable,
                tableConfig = {};

            tableConfig.iDisplayLength = helperOptions.displayRows;
            tableConfig.bFilter = true;
            tableConfig.bSort = true;
            tableConfig.bPaginate = false;
            tableConfig.bLengthChange = false;
            tableConfig.bInfo = false;

            if (helperOptions.paginate) { tableConfig.bPaginate = true; }
            if (helperOptions.lengthChange) { tableConfig.bLengthChange = true; }
            if (helperOptions.info) { tableConfig.bInfo = true; }
            if (helperOptions.search) { $(this).parent().removeClass('datatable-hidesearch'); }

            tableConfig.aaSorting = [];
            tableConfig.aoColumns = [];

            $(this).find('thead tr th').each(function (index, value) {
                var sortable = ($(this).data('sortable') === true) ? true : false;
                var sType = ($(this).data('stype')) ? $(this).data('stype') : '';

                tableConfig.aoColumns.push({ 'bSortable': sortable, 'sType': sType });



                if ($(this).data('direction')) {
                    tableConfig.aaSorting.push([index, $(this).data('direction')]);
                }
            });

            // Create the datatable
            $thisTable = $(this).dataTable(tableConfig);

            if (!helperOptions.search) {
                $thisTable.parent().find('.dataTables_filter').remove();
            }

            var filterableCols = $thisTable.find('thead th').filter('[data-filterable="true"]');

            if (filterableCols.length > 0) {
                var columns = $thisTable.fnSettings().aoColumns,
                    $row, th, $col, showFilter;

                $row = $('<tr>', { cls: 'dataTable-filter-row' }).appendTo($thisTable.find('thead'));

                for (var i = 0; i < columns.length; i++) {
                    $col = $(columns[i].nTh.outerHTML);
                    showFilter = ($col.data('filterable') === true) ? 'show' : 'hide';

                    th = '<th class="' + $col.prop('class') + '">';
                    th += '<input type="text" class="form-control input-sm ' + showFilter + '" placeholder="' + $col.text() + '">';
                    th += '</th>';
                    $row.append(th);
                }

                $row.find('th').removeClass('sorting sorting_disabled sorting_asc sorting_desc sorting_asc_disabled sorting_desc_disabled');

                $thisTable.find('thead input').keyup(function () {
                    $thisTable.fnFilter(this.value, $thisTable.oApi._fnVisibleToColumnIndex(
                        $thisTable.fnSettings(), $thisTable.find('thead input[type=text]').index(this)));
                });

                $thisTable.addClass('datatable-columnfilter');
            }
        });

        $('.dataTables_filter input').prop('placeholder', 'Search...');
    }
}

(function ($, Globalize) {

    // Tell the validator that we want numbers parsed using Globalize

    $.validator.methods.number = function (value, element) {
        var classes = $(element).attr('class');
        if (typeof classes != 'undefined') {
            var ignore = $(element).attr('class').indexOf("data-val-ignore");
            if (ignore != -1) {
                return true;
            }
        }

        var val = Globalize.parseFloat(value);
        return this.optional(element) || ($.isNumeric(val));
    };

    $.validator.methods.min = function (value, element, param) {
        var classes = $(element).attr('class');
        if (typeof classes != 'undefined') {
            var ignore = $(element).attr('class').indexOf("data-val-ignore");
            if (ignore != -1) {
                return true;
            }
        }

        var val = Globalize.parseFloat(value);
        return this.optional(element) || val >= param;
    };

    $.validator.methods.max = function (value, element, param) {
        var classes = $(element).attr('class');
        if (typeof classes != 'undefined') {
            var ignore = $(element).attr('class').indexOf("data-val-ignore");
            if (ignore != -1) {
                return true;
            }
        }

        var val = Globalize.parseFloat(value);
        return this.optional(element) || val <= param;
    };

    $.validator.methods.range = function (value, element, param) {
        var classes = $(element).attr('class');
        if (typeof classes != 'undefined') {
            var ignore = $(element).attr('class').indexOf("data-val-ignore");
            if (ignore != -1) {
                return true;
            }
        }

        var val = Globalize.parseFloat(value);
        return this.optional(element) || (val >= param[0] && val <= param[1]);
    };

    // Tell the validator that we want dates parsed using Globalize

    $.validator.methods.date = function (value, element) {
        var classes = $(element).attr('class');
        if (typeof classes != 'undefined') {
            var ignore = $(element).attr('class').indexOf("data-val-ignore");
            if (ignore != -1) {
                return true;
            }
        }

        var val = Globalize.parseDate(value);
        return this.optional(element) || (val);
    };

}(jQuery, Globalize));


$(function () {
    initDataTableHelper();
    //$('.datetimepicker').datetimepicker({ format: 'dd/mm/yyyy' });
    if ($.fn.datetimepicker) {
        $('.ui-datetimepicker').datetimepicker({ language: 'pt-BR', pickTime: false, pickDate: true });
    }
    $('.show-tooltip').tooltip({ placement: 'top' });
});

$(function () {
    $('.main_doc_person_pattern').inputmask({ mask: "999.999.999-99" });
    $('.main_doc_corporation_pattern').inputmask({ mask: "99.999.999/9999-99" });
    $('.zip_code_pattern').inputmask({ mask: "99999-999" });
    $('.date_pattern').inputmask({ mask: "99/99/9999" });
    $('.datetime_pattern').inputmask({ mask: "99/99/9999 99:99:99" });
});

jQuery.fn.preventDoubleSubmission = function () {
    $(this).on('submit', function (e) {
        var $form = $(this);

        if ($form.data('submitted') === true) {
            // Previously submitted - don't submit again
            e.preventDefault();
        } else {
            // Mark it so that the next submit can be ignored
            if ($form.valid()) {
                $form.data('submitted', true);
            }
        }
    });

    // Keep chainability
    return this;
};

$(function(){
    $(".safe-form").preventDoubleSubmission();
})