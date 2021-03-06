/**
 * Create a modal dialog and return it.
 */
function createDialog(id, options)
{
    var modal  = $('<div></div>').attr('id', id).addClass('modal fade');
    var dialog = $('<div></div>').addClass('modal-dialog');
    var content = $('<div></div>').addClass('modal-content');
    dialog.append(content);
    modal.append(dialog);


    // Add the modal header, but only if we allow a close button and/or have a title:
    if(options.allowClose || options.title)
    {
        var header  = $('<div></div>').addClass('modal-header');

        if(options.allowClose)
        {
            header.append('<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>');
        }

        if(options.title)
        {
            header.append($('<h4></h4>').text(options.title));
        }

        content.append(header);
    }

    // Add the modal body:
    content.append($('<div></div>').addClass('modal-body'));

    if(options.body)
    {
        content.find('.modal-body').append(options.body);
    }

    // If we have been passed a button, add it as the modal footer:
    if(options.button)
    {
        var footer = $('<div></div>').addClass('modal-footer');
        footer.append(options.button);

        content.append(footer);
    }

    // Show immediately?
    if(options.show)
    {
        modal.modal();
    }

    $('body').append(modal);

    return modal;

}


window.pageEditor = Class.extend({
    id: null,
    content: {},
    page: {},

    triggerSaveDetails: function () {
        $('.page-save-notice').addClass('alert-warning').removeClass('alert-success').text('Saving...').fadeIn('fast');

        var self = this;
        var form = $('#details form');
        var serialized = form.serializeArray();
        var details = {};

        for (var i in serialized) {
            var name = serialized[i].name;

            if (name.substring(name.length - 2) == '[]') {
                name = name.substring(0, name.length - 2);

                if (!details[name]) {
                    details[name] = [];
                }

                details[name].push(serialized[i].value);
            } else {
                details[name] = serialized[i].value;
            }
        }

        self.page = details;
        self.saveMetaData();
    },

    triggerSaveContent: function (form) {
        $('.page-save-notice').addClass('alert-warning').removeClass('alert-success').text('Saving...').fadeIn('fast');

        var self = this;
        var formId = form.attr('id').replace('block_', '');
        var serialized = form.serializeArray();
        var content = {};

        for (var i in serialized) {
            var name = serialized[i].name;

            if (name.substring(name.length - 2) == '[]') {
                name = name.substring(0, name.length - 2);

                if (!content[name]) {
                    content[name] = [];
                }

                content[name].push(serialized[i].value);
            } else {
                content[name] = serialized[i].value;
            }
        }

        self.content[formId] = content;
        self.saveContent();
    },

    saveContent: function () {
        var self = this;

        $.post('/'+window.adminUri+'/page/save/' + this.id, {content: JSON.stringify(this.content)}, function (response) {
            response = JSON.parse(response);

            if (self.content_id != response.content_id) {
                document.getElementById('page-preview').contentWindow.location.reload();
            }

            $('.page-save-notice').addClass('alert-success').removeClass('alert-warning').text('Saved.').fadeOut('slow');
        });
    },

    saveMetaData: function() {
        $('.pace').removeClass('hide');

        $.post('/'+window.adminUri+'/page/save/' + this.id, {page: this.page}, function () {
            document.getElementById('page-preview').contentWindow.location.reload();
            $('.page-save-notice').addClass('alert-success').removeClass('alert-warning').text('Saved.').fadeOut('slow');
        });
    }

});


function textElement(id, label, value)
{
    var section = $('<div></div>').addClass('form-group');
    var label1 = $('<label></label>').text(label);
    section.append(label1);

    var input = $('<input>').addClass('form-control').attr('id', id).attr('type', 'text').val(value);
    section.append(input);

    return section;
}

function selectElement(id, label, options, value)
{
    var section = $('<div></div>').addClass('form-group');
    var label1 = $('<label></label>').text(label);
    section.append(label1);

    var input = $('<select></select>').attr('id', id).attr('type', 'text').addClass('form-control');

    for (var i in options) {
        input.append($('<option></option>').val(i).text(options[i]));
    }

    input.val(value);

    section.append(input);
    input.css('width', '100%');
    input.select2();

    return section;
}

function imagePicker(id, label, value)
{
    var section = $('<div></div>').addClass('form-group');
    var input = $('<input>').attr('id', id).attr('type', 'text').addClass('form-control');
    input.css('width', '100%');

    var img = $('<img>');
    section.append(img);

    if (value) {
        img.attr('src', '/media/render/' + value + '/160/90');
    }

    section.append('<br /><br />');
    section.append(input);

    input.select2({
        placeholder: "Search for an image",
        minimumInputLength: 1,
        width: '100%',
        ajax: {
            url: '/'+window.adminUri+'/media/autocomplete/images',
            dataType: 'json',
            data: function(term) {
                return {
                    q: term
                };
            },
            results: function(data) {
                return data;
            }
        }
    });

    input.on('change', function () {
        img.attr('src', '/media/render/' + $(this).val() + '/160/90');
    });

    input.val(value);

    return section;
}

function pagePicker(id, label, value)
{
    var section = $('<section></section>').addClass('control-group');
    var label2 = $('<label></label>').addClass('input');
    var input = $('<input>').attr('id', id).attr('type', 'text');
    input.css('width', '100%');
    section.append(label2);

    var img = $('<img>');
    label2.append(img);
    label2.append('<br /><br />');
    label2.append(input);
    input.css('width', '100%');

    input.select2({
        placeholder: "Search for a page",
        minimumInputLength: 1,
        width: '560px',
        ajax: {
            url: '/'+window.adminUri+'/page/autocomplete',
            dataType: 'json',
            data: function(term) {
                return {
                    q: term
                };
            },
            results: function(data) {
                return data;
            }
        }
    });

    input.val(value);

    return section;
}

$(document).ready(function () {
    $('.btn-delete').on('click', function () {
        return confirm('Are you sure?');
    });

    $('.select2').select2();

    $('.octo-image-picker').each(function () {
        var input = $(this);
        var img = $('<img>');
        img.insertAfter(input).css({'margin': '10px 0'}).hide();

        input.select2({
            placeholder: "Search for an image",
            allowClear: true,
            minimumInputLength: 1,
            width: '100%',
            initSelection : function(element, callback) {

                if (input.val()) {
                    img.attr('src', '/media/render/' + input.val() + '/160/90');
                    img.show();

                    $.getJSON('/'+window.adminUri+'/media/autocomplete/images?q=' + input.val(), function (data) {
                        if (data.results[0]) {
                            callback(data.results[0]);
                        }
                    });
                }

            },
            ajax: {
                url: '/'+window.adminUri+'/media/autocomplete/images',
                dataType: 'json',
                data: function(term) {
                    return {
                        q: term
                    };
                },
                results: function(data) {
                    return data;
                }
            }
        });

        input.on('change', function () {
            img.attr('src', '/media/render/' + $(this).val() + '/160/90');
            img.show();
        });
    });

    $('.octo-file-picker').each(function () {
        var input = $(this);

        input.select2({
            placeholder: "Search for a file",
            allowClear: true,
            minimumInputLength: 1,
            width: '100%',
            initSelection : function(element, callback) {

                if (input.val()) {
                    $.getJSON('/'+window.adminUri+'/media/autocomplete/files?q=' + input.val(), function (data) {
                        if (data.results[0]) {
                            callback(data.results[0]);
                        }
                    });
                }

            },
            ajax: {
                url: '/'+window.adminUri+'/media/autocomplete/files',
                dataType: 'json',
                data: function(term) {
                    return {
                        q: term
                    };
                },
                results: function(data) {
                    return data;
                }
            }
        });
    });

    $('.octo-page-picker').each(function () {
        var input = $(this);

        input.select2({
            placeholder: "Search for a page",
            minimumInputLength: 1,
            allowClear: true,
            width: '100%',
            initSelection : function(element, callback) {

                if (input.val()) {
                    $.getJSON('/'+window.adminUri+'/page/autocomplete?q=' + input.val(), function (data) {
                        if (data.results[0]) {
                            callback(data.results[0]);
                        }
                    });
                }

            },
            ajax: {
                url: '/'+window.adminUri+'/page/autocomplete',
                dataType: 'json',
                data: function(term) {
                    return {
                        q: term
                    };
                },
                results: function(data) {
                    return data;
                }
            }
        });
    });
});

// Sortable
var fixHelper = function(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
};