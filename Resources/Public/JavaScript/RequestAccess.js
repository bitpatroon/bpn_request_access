(function () {

    function initDatePicker()
    {
        if(!$.fn.datetimepicker){
            return;
        }

        $('[data-datepicker="1"]').each(function () {
            var $element = $(this);
            $element.datetimepicker({
                format: 'd-m-Y',
                locale: 'nl',
                timepicker: false
            });
        });
        $('[data-timepicker="1"]').each(function () {
            var $element = $(this);
            loadExternals(function () {
                $element.datetimepicker({
                    datepicker: false,
                    format: 'H:i'
                });
            });
        });
        $('[data-datetimepicker="1"]').each(function () {
            var $element = $(this);
            $element.datetimepicker({
                format: 'd.m.Y H:i'
            });
        });
    }

    function initSelect2()
    {
        if(!$.fn.select2){
            return;
        }
        $('[data-s2="1"]').each(function () {
            $(this).select2();
        });
    }

    function updateEndDate()
    {
        var $selection = $('#input-permittedDuration option:selected');
        $selection.each(function (){
            var tsVal = $(this).attr('data-ts') || null;
            if(tsVal){
                var ts = parseInt(tsVal);
                $('#input-end').text(moment.unix(ts).format('DD-MM-YYYY'));
            }
            return false;
        });
    }

    function initForm()
    {
        var $form = $('form[name="accessForm"]');
        if (!$form.length) {
            return;
        }
        $('#input-start').on('change', updateEndDate);
        $('#input-permittedDuration').on('change', updateEndDate);

        // trigger on load
        updateEndDate();
    }

    document.addEventListener('DOMContentLoaded', function () {
        initForm();
        initDatePicker();
        initSelect2();
    });
})();
