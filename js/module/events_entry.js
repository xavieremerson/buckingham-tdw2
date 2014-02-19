function loadCompanyInfo(){
    if ($('#de_news #vsymbol').val().length == 0) {
        return false;
    }

    $.get('events_entry_ajax.php', {
        mod_request: 'cname',
        symbol: $('#de_news #vsymbol').val()
    }, function(data){
        $('#de_news #company_info').html(data);
    });
    return false;
}

function clearInputs(){
    var now = new Date();
    $('#de_news [name="vdate"]').val((now.getMonth()+1)+'/'+now.getDate()+'/'+now.getFullYear());
    $('#de_news [name="vsymbol"]').val('');
    $('#de_news [name="vnote"]').val('');
}

$(document).ready(function() {
    $('#de_news [name="vdate"]').datepicker();
    
    $('#de_news .vdateBtn').click(function(){
        $('#de_news [name="vdate"]').focus();
    });
    
    $('#de_news [name="vsymbol"]').keypress(function(e){
        if(e.which == 13){
            loadCompanyInfo();
        }
    });

    $('#de_news [name="vsymbol"]').change(function(){
        loadCompanyInfo();
    });

    $('#save_symbol_btn').click(function() {
        //Check symbol
        if ($('#de_news #vsymbol').val() == '') {
            alert("Symbol is a required fields.\nPlease enter appropriate value and then proceed!");
            return false;
        }
        //check v enter by
        if ($('#de_news #venteredby').val() == 1) {
            alert("ERROR: TWO DIFFERENT USERS CONCURRENTLY LOGGED IN TO TDW/ETPA ON THIS M/C.\n\nPlease logout and login again to continue entering news items.");
            return false;
        }

        var params = {
            vdate: $('#de_news [name="vdate"]').val(),
            vtype: $('#de_news [name="vtype"]').val(),
            vsymbol: $('#de_news [name="vsymbol"]').val(),
            vnote: $('#de_news [name="vnote"]').val(),
            venteredby: $('#de_news [name="venteredby"]').val()
        };
        $.post('events_entry_process.php', params, function(htmlResponse) {
            //Replace html
            $('#resultContainer').html(htmlResponse);
            //Clear inputs
            clearInputs();
        });
    });
});