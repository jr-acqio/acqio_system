jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "currency-pre": function (a) {
        a = (a === "-") ? 0 : a.replace(/[^\d\-]/g, "");
        return Globalize.parseFloat(a);
    },

    "currency-asc": function (a, b) {
        return a - b;
    },

    "currency-desc": function (a, b) {
        return b - a;
    }
});

jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "date-adv-pre": function (a) {
        if ($.trim(a) != '') {
            var frDatea = $.trim(a).split(' ');
            var frDatea2 = frDatea[0].split('/');
            if (frDatea.length > 1) {
                var frTimea = frDatea[1].split(':');
                var x = (frDatea2[2] + frDatea2[1] + frDatea2[0] + frTimea[0] + frTimea[1] + frTimea[2]) * 1;
            } else {
                var x = (frDatea2[2] + frDatea2[1] + frDatea2[0]) * 1;
            }
        } else {
            var x = 10000000000000; // = l'an 1000 ...
        }

        return x;
    },

    "date-adv-asc": function (a, b) {
        return a - b;
    },

    "date-adv-desc": function (a, b) {
        return b - a;
    }
});

jQuery.extend(jQuery.fn.dataTableExt.oSort, {
    "percent-pre": function (a) {
        a = (a === "-") ? 0 : a.replace(/[^\d\-]/g, "");
        return Globalize.parseFloat(a);
    },

    "percent-asc": function (a, b) {
        return a - b;
    },

    "percent-desc": function (a, b) {
        return b - a;
    }
});
