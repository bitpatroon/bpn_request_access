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
        var start = $('#input-start').val();
        var duration = $('#input-permittedDuration').val();
        console.log('RequestAccess.js:1620304419419:', start, duration);
        if (start && duration) {
            // calculate end date
            var durationParts = duration.split(' ');
            $('#input-end').text(moment(start, 'DD-MM-YYYY').add(parseInt(durationParts[0]), durationParts[1] + 's').format('DD-MM-YYYY'));
        }
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
