let version = function () {
    let init = function() {
        $('#version-select').change(function() {
            let path = window.location.pathname;
            let version = $('#version-select option:selected').val();
            $(location).attr('href','/version/' + version + path);
        });
    };
    $(document).ready(init);

    return {
        
    }
}();