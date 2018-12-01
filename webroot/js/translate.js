function init_global_msgstr($msgid) {
    NProgress.start();
    var file =  $('#file').val();
    $.ajax({
        // setup x-csrf-token
        headers : {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')},
        url: '/translate/translate/getMsgstr',
        type:'POST',
        dataType: 'json',
        data : {'file' : file, 'msgid' : $msgid},
        success: function (response) {
            if(response.status == 400){
                console.log('error');
            }else{
                $('.js-msgstr').html(response.data);
                // hide button add new
                $('.js-add-new').hide();
                // show button update
                $('.js-update-language').show();
            }
            NProgress.done();
        },
        error:function (error) {
            NProgress.done();
            console.log('error',error);
        }
    });
}

// define validation

var validateForm = function (form) {
    var isValid = false;
    var a = form.find(':input').not(':button, .optional, .select2-search__field').filter(function () {
        return $(this).val() === "";
    });
    $.map(a, function (ele) {
        var $element = $(ele);
        var formGroup = $element.closest('.form-group');
        formGroup.addClass('has-error');
    });

    var elapsed_element = $('.result');

    $.each(elapsed_element, function (index, item) {
        if($(item).val() > 480){
            $(item).parent().addClass('has-error');
            $('.time-error').show();
        }
    });

    var elementCondition = form.find('.form-group.has-error');
    if (elementCondition.length > 0) {
        isValid = false;
    } else {
        isValid = true;
    }
    return isValid;
};

var translate = function () {
    return {
        init: function () {
            this.getMsgid();
            this.getMsgstr();
            this.updateLanguage();
            this.addNewElement();
            this.sendNewLanguage();
            this.goBack();
        },
        getMsgid: function () {
            onLoadFile($('.js-msgid').val());
            function onLoadFile(file){
                NProgress.start();
                $.ajax({
                    // setup x-csrf-token
                    headers : {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')},
                    url: '/translate/translate/showMsgid',
                    type:'POST',
                    async: true,
                    dataType: 'json',
                    data : {'file' : file},
                    success: function (response) {
                        if(response.status == 400){
                            console.log('error');
                        }else{
                            $('#msgid').html(response.data);
                            // call again
                            init_global_msgstr($('#msgid').val())
                        }
                        NProgress.done();
                    },
                    error:function (error) {
                        NProgress.done();
                        console.log('error',error);
                    }
                });
            }
            $(document).on('change','.js-msgid',function (e) {
                var file = $(this).val();
                onLoadFile(file);
            });
        },
        getMsgstr : function () {
            init_global_msgstr($('#msgid').val());
            // call again if happen event change
            $(document).on('change','.js-get-msgstr',function (e) {
                init_global_msgstr($('#msgid').val())
            });
        },
        updateLanguage:function () {
            $(document).on('click','.js-update-language',function (e) {
                var obj = {};
                $.each($(".translate-msgstr"), function (i, news) {
                    obj[$(news).data('language')] = $(news).val();
                });
                if (Object.keys(obj).length >= 0){
                    var file = $('#file').val();
                    var key = $('#msgid').val();
                    $.ajax({
                        // setup x-csrf-token
                        headers : {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')},
                        url: '/translate/translate/updateLanguage',
                        type:'POST',
                        dataType: 'json',
                        data : {'obj' : obj, 'file' : file, 'key' : key},
                        success: function (response) {
                            if(response.status == 400){
                                toastr.error(response.message, 'Message');
                            }else{
                                toastr.success(response.message, 'Message');
                            }
                            NProgress.done();
                        },
                        error:function (error) {
                            NProgress.done();
                            toastr.error(error.responseJSON.message, 'Error');
                        }
                    });
                }else{
                    alert('Empty value');
                    return false;
                }

            })
        },
        addNewElement:function () {
            $(document).on('click','.add-new',function (e) {
                e.preventDefault();
                var element = '<div class="form-group"> ';
                    element += '<label for="new_key" class="font-green bold">New Key</label>';
                    element +='<input class="form-control" id="new_key" type="text"  value="">';
                    element +='</div>';
                $('.add-new-key').show().html(element);
                // set empty value
                $('.translate-msgstr').val('');
                // hide button update
                $('.js-update-language').hide();
                // show button add new
                $('.js-add-new').show();

            })
        },
        sendNewLanguage:function () {
            $(document).on('click','.js-add-new',function (e) {
                e.preventDefault();
                if(validateForm($('#translate-form')) ) {
                    var obj = {};
                    $.each($(".translate-msgstr"), function (i, news) {
                        obj[$(news).data('language')] = $(news).val();
                    });
                    if (Object.keys(obj).length >= 0){
                        var file = $('#file').val();
                        var key = $('#new_key').val();
                        $.ajax({
                            // setup x-csrf-token
                            headers : {'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')},
                            url: '/translate/translate/updateLanguage',
                            type:'POST',
                            dataType: 'json',
                            data : {'obj' : obj, 'file' : file, 'key' : key},
                            success: function (response) {
                                if(response.status == 400){
                                    toastr.error(response.message, 'Message');
                                }else{
                                    toastr.success(response.message, 'Message');
                                    // hide form add new key
                                    $('.add-new-key').hide();
                                }
                                NProgress.done();
                            },
                            error:function (error) {
                                NProgress.done();
                                toastr.error(error.responseJSON.message, 'Error');
                            }
                        });
                    }else{
                        alert('Empty value');
                        return false;
                    }
                } else {
                    return;
                }

            })
        },
        goBack:function () {
            $('.go-back').click(function (e) {
                e.preventDefault();
                window.history.back();
            })
        }
    }
}();

jQuery(document).ready(function () {
    translate.init();

    // catch event keyup to remove class has-error
    $('#translate-form').on('dp.change keyup', 'input:not(.optional)', function () {
        var $this = $(this);
        var formGroup = $this.closest('.form-group');
        var ipt = $this.val().trim();
        if (ipt !== '') {
            formGroup.removeClass('has-error');
        } else {
            if (!formGroup.hasClass('has-error')) {
                formGroup.addClass('has-error');
            }
        }
    })
});
