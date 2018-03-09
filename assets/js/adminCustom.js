function initializeCheckBoxes() {
    $('input[type=checkbox]:not(.ak_modal input[type=checkbox]):not(input[type=checkbox][data-toggle])').each(function () {
        if ($(this).is(':checked')) {
            $(this).val('true');
        } else {
            $(this).val('false');
        }
    });
}

function initBootstrapToggle() {
    var $input = $('input[type="checkbox"]');
    $input.bootstrapToggle();
}

$(document).ready(function () {
    var dTables = $('.ak-dataTable').DataTable({responsive: true});

    initializeCheckBoxes();
    initBootstrapToggle();

    $('.imgCont').hover(handlerImageIn, handlerImageOut);

    if (typeof tinymce !== 'undefined' && tinymce !== null) {
        tinymce.init({
            selector: '.editor',
            height: 350,
            menubar: false,
            theme: 'modern',
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code'
            ],
            toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
            // add this to the end of previous line if you want image uploading in the html editor : image
            // image_advtab: true,
            content_css: ['//fonts.googleapis.com/css?family=Lato:300,300i,400,400i', '//www.tinymce.com/css/codepen.min.css']
        });
    }

    $(':file').on('fileselect', function (event, numFiles, label) {
        var input = $(this).parents('.form-group').find('.hiddenLabel'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;

        if (input.length) {
            input.val(log);
        } else {
            if (log) alert(log);
        }
    });

    var akModals = $(".ak_modal");
    akModals.on("show.bs.modal", function (e) {
        var link = $(e.relatedTarget);
        var modal = $(this);
        if (!modal.data('modal-href')) {
            modal.data('modal-href', link.attr("href"));
        }
        if (modal.data('refresh')) {
            modal.removeData('refresh');
        }
        modal.find(".modal-content").load(modal.data('modal-href'), function (response, status, xhr) {
            if (status === "error") {
                var msg = "Sorry but there was an error: ";
                console.log(msg + xhr.status + " " + xhr.statusText);
            } else {
                // add initializations after load ajax
                initializeCheckBoxes();
                initBootstrapToggle();
            }
        });
    });

    akModals.on('hidden.bs.modal', function (e) {
        var modal = $(this);
        if (modal.data('refresh')) {
            var target = $(e.target);
            target.removeData('bs.modal')
                .find(".modal-content").html('');
            $(this).modal('show');
        }
    });


    $(document).on('submit', '.ak_modal form', function (e) {
        e.preventDefault();
        var modal = $(this).closest('.ak_modal');
        var data = $(this).serializeArray();
        data.push({name: 'isAjax', value: true});
        $.ajax({
            url: $(this).attr('action'),
            data: data,
            method: 'POST'
        }).success(function (data) {
            // Do stuff after success
        }).fail(function (xhr, textStatus, error) {
            var msg = "Sorry but there was an error: ";
            console.log(msg + xhr.status + " " + xhr.statusText + " " + error);
        }).complete(function (data) {
            modal.data('refresh', true);
            modal.modal('toggle');
        });
    });

    $(document).on('change', 'input[type=checkbox][data-toggle="toggle"]', function () {
        var checkbox = $(this);
        if (this.checked) {
            if (checkbox.data('custom-on-val')) {
                this.value = checkbox.data('custom-on-val');
            }
        } else {
            if (checkbox.data('custom-off-val')) {
                this.value = checkbox.data('custom-off-val');
            }
        }
    });

    $('.numeric').numeric({ negative : false });

    $('#parent_category').on('change', function(){
        if ($(this).val() != "1"){
            $('#parentCategoryId_input').val('0');
            $('#parentCategoryIdContainer').css('visibility', 'hidden');
        } else {
            $('#parentCategoryIdContainer').css('visibility', 'visible');
        }
    });

    $('#promotedFrom_input').datetimepicker({
        format: 'DD/MM/YYYY HH:mm',
        ignoreReadonly: true
    });

    $('#promotedTo_input').datetimepicker({
        format: 'DD/MM/YYYY HH:mm',
        ignoreReadonly: true
    });

    $(document).ready(function(){
        $('#productCategoryId_input').on('change', function(){
            $.ajax({
                type: 'POST',
                url: getContextPath() + '/admin/ajaxAction/updateSecondaryProductCategorySelect',
                data: ({currentProductCategoryId:$('#productCategoryId_input').val()}),
                success: function(data) {
                    $('#secondaryProductCategoryId_input').find('option').remove().end().append(data).val('');
                },
                error: function(){
                }
            });
        });
    });
});

function handlerImageIn() {
    var height = $(this).height();
    var btn = $(this).find('.btn');
    btn.css('visibility', 'visible').css('top', '-' + height / 3 + 'px');
}

function handlerImageOut() {
    var btn = $(this).find('.btn');
    btn.css('visibility', 'hidden').css('top', '0');
}

function previewImage(fileInput) {
    var oFReader = new FileReader();
    oFReader.readAsDataURL(fileInput.files[0]);
    var $img = $(fileInput).closest('form').find('img[data-preview]');
    oFReader.onload = function (oFREvent) {
        $img[0].src = oFREvent.target.result;
    };
}

function getContextPath() {
    return window.location.pathname.substring(0, window.location.pathname.indexOf("/",2));
}

$(document).on('change', 'input[type=checkbox]', function () {
    if ($(this).is(':checked')) {
        $(this).val('true');
    } else {
        $(this).val('false');
    }
});

$(document).on('change', ':file', function () {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
    previewImage(this);
});

(function ($) {
    /*checkbox
     * Allows only valid characters to be entered into input boxes.
     * Note: fixes value when pasting via Ctrl+V, but not when using the mouse to paste
     *      side-effect: Ctrl+A does not work, though you can still use the mouse to select (or double-click to select all)
     *
     * @name     numeric
     * @param    config      { decimal : "." , negative : true }
     * @param    callback     A function that runs if the number is not valid (fires onblur)
     * @author   Sam Collett (http://www.texotela.co.uk)
     * @example  $(".numeric").numeric();
     * @example  $(".numeric").numeric(","); // use , as separater
     * @example  $(".numeric").numeric({ decimal : "," }); // use , as separator
     * @example  $(".numeric").numeric({ negative : false }); // do not allow negative values
     * @example  $(".numeric").numeric(null, callback); // use default values, pass on the 'callback' function
     *
     */
    $.fn.numeric = function (config, callback) {
        if (typeof config === 'boolean') {
            config = {decimal: config};
        }
        config = config || {};
        // if config.negative undefined, set to true (default is to allow negative numbers)
        if (typeof config.negative == "undefined") config.negative = true;
        // set decimal point
        var decimal = (config.decimal === false) ? "" : config.decimal || ".";
        // allow negatives
        var negative = (config.negative === true) ? true : false;
        // callback function
        var callback = typeof callback == "function" ? callback : function () {
            };
        // set data and methods
        return this.data("numeric.decimal", decimal).data("numeric.negative", negative).data("numeric.callback", callback).keypress($.fn.numeric.keypress).keyup($.fn.numeric.keyup).blur($.fn.numeric.blur);
    }

    $.fn.numeric.keypress = function (e) {
        // get decimal character and determine if negatives are allowed
        var decimal = $.data(this, "numeric.decimal");
        var negative = $.data(this, "numeric.negative");
        // get the key that was pressed
        var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
        // allow enter/return key (only when in an input box)
        if (key == 13 && this.nodeName.toLowerCase() == "input") {
            return true;
        }
        else if (key == 13) {
            return false;
        }
        var allow = false;
        // allow Ctrl+A
        if ((e.ctrlKey && key == 97 /* firefox */) || (e.ctrlKey && key == 65) /* opera */) return true;
        // allow Ctrl+X (cut)
        if ((e.ctrlKey && key == 120 /* firefox */) || (e.ctrlKey && key == 88) /* opera */) return true;
        // allow Ctrl+C (copy)
        if ((e.ctrlKey && key == 99 /* firefox */) || (e.ctrlKey && key == 67) /* opera */) return true;
        // allow Ctrl+Z (undo)
        if ((e.ctrlKey && key == 122 /* firefox */) || (e.ctrlKey && key == 90) /* opera */) return true;
        // allow or deny Ctrl+V (paste), Shift+Ins
        if ((e.ctrlKey && key == 118 /* firefox */) || (e.ctrlKey && key == 86) /* opera */
            || (e.shiftKey && key == 45)) return true;
        // if a number was not pressed
        if (key < 48 || key > 57) {
            /* '-' only allowed at start and if negative numbers allowed */
            if (this.value.indexOf("-") != 0 && negative && key == 45 && (this.value.length == 0 || ($.fn.getSelectionStart(this)) == 0)) return true;
            /* only one decimal separator allowed */
            if (decimal && key == decimal.charCodeAt(0) && this.value.indexOf(decimal) != -1) {
                allow = false;
            }
            // check for other keys that have special purposes
            if (
                key != 8 /* backspace */ &&
                key != 9 /* tab */ &&
                key != 13 /* enter */ &&
                key != 35 /* end */ &&
                key != 36 /* home */ &&
                key != 37 /* left */ &&
                key != 39 /* right */ &&
                key != 46 /* del */
            ) {
                allow = false;
            }
            else {
                // for detecting special keys (listed above)
                // IE does not support 'charCode' and ignores them in keypress anyway
                if (typeof e.charCode != "undefined") {
                    // special keys have 'keyCode' and 'which' the same (e.g. backspace)
                    if (e.keyCode == e.which && e.which != 0) {
                        allow = true;
                        // . and delete share the same code, don't allow . (will be set to true later if it is the decimal point)
                        if (e.which == 46) allow = false;
                    }
                    // or keyCode != 0 and 'charCode'/'which' = 0
                    else if (e.keyCode != 0 && e.charCode == 0 && e.which == 0) {
                        allow = true;
                    }
                }
            }
            // if key pressed is the decimal and it is not already in the field
            if (decimal && key == decimal.charCodeAt(0)) {
                if (this.value.indexOf(decimal) == -1) {
                    allow = true;
                }
                else {
                    allow = false;
                }
            }
        }
        else {
            allow = true;
        }
        return allow;
    }

    $.fn.numeric.keyup = function (e) {
        var val = this.value;
        if (val.length > 0) {
            // get carat (cursor) position
            var carat = $.fn.getSelectionStart(this);
            // get decimal character and determine if negatives are allowed
            var decimal = $.data(this, "numeric.decimal");
            var negative = $.data(this, "numeric.negative");

            // prepend a 0 if necessary
            if (decimal != "") {
                // find decimal point
                var dot = val.indexOf(decimal);
                // if dot at start, add 0 before
                if (dot == 0) {
                    this.value = "0" + val;
                }
                // if dot at position 1, check if there is a - symbol before it
                if (dot == 1 && val.charAt(0) == "-") {
                    this.value = "-0" + val.substring(1);
                }
                val = this.value;
            }

            // if pasted in, only allow the following characters
            var validChars = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, '-', decimal];
            // get length of the value (to loop through)
            var length = val.length;
            // loop backwards (to prevent going out of bounds)
            for (var i = length - 1; i >= 0; i--) {
                var ch = val.charAt(i);
                // remove '-' if it is in the wrong place
                if (i != 0 && ch == "-") {
                    val = val.substring(0, i) + val.substring(i + 1);
                }
                // remove character if it is at the start, a '-' and negatives aren't allowed
                else if (i == 0 && !negative && ch == "-") {
                    val = val.substring(1);
                }
                var validChar = false;
                // loop through validChars
                for (var j = 0; j < validChars.length; j++) {
                    // if it is valid, break out the loop
                    if (ch == validChars[j]) {
                        validChar = true;
                        break;
                    }
                }
                // if not a valid character, or a space, remove
                if (!validChar || ch == " ") {
                    val = val.substring(0, i) + val.substring(i + 1);
                }
            }
            // remove extra decimal characters
            var firstDecimal = val.indexOf(decimal);
            if (firstDecimal > 0) {
                for (var i = length - 1; i > firstDecimal; i--) {
                    var ch = val.charAt(i);
                    // remove decimal character
                    if (ch == decimal) {
                        val = val.substring(0, i) + val.substring(i + 1);
                    }
                }
            }
            // set the value and prevent the cursor moving to the end
            this.value = val;
            $.fn.setSelection(this, carat);
        }
    }

    $.fn.numeric.blur = function () {
        var decimal = $.data(this, "numeric.decimal");
        var callback = $.data(this, "numeric.callback");
        var val = this.value;
        if (val != "") {
            var re = new RegExp("^\\d+$|\\d*" + decimal + "\\d+");
            if (!re.exec(val)) {
                callback.apply(this);
            }
        }
    }

    $.fn.removeNumeric = function () {
        return this.data("numeric.decimal", null).data("numeric.negative", null).data("numeric.callback", null).unbind("keypress", $.fn.numeric.keypress).unbind("blur", $.fn.numeric.blur);
    }

// Based on code from http://javascript.nwbox.com/cursor_position/ (Diego Perini <dperini@nwbox.com>)
    $.fn.getSelectionStart = function (o) {
        if (o.createTextRange) {
            var r = document.selection.createRange().duplicate();
            r.moveEnd('character', o.value.length);
            if (r.text == '') return o.value.length;
            return o.value.lastIndexOf(r.text);
        } else return o.selectionStart;
    }

// set the selection, o is the object (input), p is the position ([start, end] or just start)
    $.fn.setSelection = function (o, p) {
        // if p is number, start and end are the same
        if (typeof p == "number") p = [p, p];
        // only set if p is an array of length 2
        if (p && p.constructor == Array && p.length == 2) {
            if (o.createTextRange) {
                var r = o.createTextRange();
                r.collapse(true);
                r.moveStart('character', p[0]);
                r.moveEnd('character', p[1]);
                r.select();
            }
            else if (o.setSelectionRange) {
                o.focus();
                o.setSelectionRange(p[0], p[1]);
            }
        }
    }

})(jQuery);