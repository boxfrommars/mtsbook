$(document).ready(function () {
    $('#book-form .file').each(function(key, fileField){
        var $field = $(fileField);
        var $input = $('<input>').attr('type', 'file').addClass('pseudo-file').attr('name', 'book_file[x-files]');
        $field.after($input);
        if ($field.hasClass('file-image') && $field.val()) {
            $field.after($('<img>').attr('src', '/files/' + $field.val()).addClass('admin-image'));
        }

        $input.fileupload({
            url: '/admin/upload/file',
            dataType: 'json',
            done: function (e, data) {
                if (data.result.success) {
                    $field.val(data.result.name);
                    if ($field.hasClass('file-image')) {
                        $field.after($('<img>').attr('src', '/files/' + data.result.name).addClass('admin-image'));
                    } else {
                        $('[data-size-of="' + $field.attr('data-file-type') + '"]').val(data.result.size);
                    }
                }
            }
        });
    });
});