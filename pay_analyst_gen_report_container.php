<?php
//BRG
include('inc_header.new.php');
?>
<script language ="javascript">
    function getCreatedReport() {
        params = {
            sel_qtr: $('#sel_qtr :selected').val(),
            sel_year: $('#sel_year :selected').val()
        };

        if (typeof (params.sel_qtr) === 'undefined' || typeof (params.sel_year) === 'undefined') {
            alert("Please select Quarter and Year");
            return false;
        }

        $('#notify').html('Report will be ready in 5-10 seconds.<br><img src="images/loading-bar.gif" border="0">');

        $.post('pay_analyst_report_excel_create.php', params, function(data) {
            $('#period_selector p').text('');
            $('#notify').html(data);
        });

    }

    $(document).ready(function() {
        $('#create_report').click(function() {
            if ($('#notify').html() == '') {
                getCreatedReport();
            }
        });
    });
</script>
<div id="ControlBox"><div class="Title Start">►</div><div class="Title Middle">Generate Analyst Allocations Report</div><div class="Title End">&nbsp;</div>
    <style type="text/css">
        .notify_x {
            font-family: verdana;
            font-size: 12px;
            font-weight: bold;
            color: #009900;
            text-decoration: none;
            background-color: #E6FFE6;
            border-top-width: 1px;
            border-bottom-width: 1px;
            border-top-style: solid;
            border-right-style: none;
            border-bottom-style: solid;
            border-left-style: none;
            border-top-color: #00CC33;
            border-bottom-color: #00CC33;
        }

        #period_selector {
            min-height: 145px;
            padding-top: 40px;
            padding-left: 25px;
            padding-bottom: 20px;
        }

        #period_selector select{
            margin-right: 20px;
        }

        #period_selector p{
            margin-top: 20px;
            font-weight: normal;
            font-size: medium;
            font-variant: normal;
            font-style: normal;
        }
    </style>
    <div id="period_selector">
        <select name="sel_qtr" id="sel_qtr">
            <option value="">Select Quarter</option>
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <option value="<?= $i ?>" <?= ($sel_qtr == $i) ? 'selected' : '' ?>>Qtr. <?= $i ?></option>
            <?php endfor; ?>
        </select>
        <select name="sel_year" id="sel_year">
            <option value="">Select Year</option>
            <?php
            for ($yearOption = date('Y'); $yearOption >= date('Y') - 3; $yearOption--):
                ?>
                <option value="<?= $yearOption ?>" <?= ($sel_year == $yearOption) ? 'selected' : '' ?>><?= $yearOption ?></option>
            <?php endfor; ?>
        </select>		
        <input type="image" id="create_report" src="images/lf_v1/form_submit.png"/>
        <?php if ($sel_qtr == "" || $sel_year == "") { ?>
            <p>Please select Quarter and Year.</p>
        <?php } ?>
        <div id="notify" class="ilt"></div>
    </div>
</div>
<?php
include('inc_footer.php');
?>