function clear_errors()
{
    let i;
    for (i = 0; i < arguments.length; i++) {

        if($('#'+arguments[i]).val().length > 0){
            $('.'+arguments[i]).closest('div.'+arguments[i]).removeClass('is-invalid').find('.text-danger').remove();
            $('.'+arguments[i]).find('#'+arguments[i]).removeClass('is-invalid');
        }
    }
}
