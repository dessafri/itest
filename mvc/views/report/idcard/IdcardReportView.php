<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa iniicon-idcardreport"></i> Report Penilaian</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"> <?=$this->lang->line('menu_idcardreport')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">

            <div class="col-sm-12">
            <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-2">No</th>
                                <th class="col-sm-2">Nama</th>
                                <?php foreach($subtype as $type){ ?>
                                    <th class="col-sm-2"><?=$type->name?></th>
                                <?php }?>
                                <th class="col-sm-2">Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; foreach($results as $result) { ?>
                                <tr>
                                    <td data-title="id">
                                        <?php echo $i; ?>
                                    </td>
                                    <td data-title="namaKaryawan">
                                        <?=$result['name']?>
                                    </td>
                                    <?php foreach($result['types'] as $type){ ?>
                                        <td><?=$type['value']?></td>
                                    <?php } ?>
                                    <td><?=$result['nilai_akhir'] ?></td>
                                </tr>
                            <?php $i++; } ?>
                        </tbody>
                    </table>
            </div>

        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->

<div id="load_idcardreport"></div>


<script type="text/javascript">
    
    function printDiv(divID) {
        var oldPage = document.body.innerHTML;
        var divElements = document.getElementById(divID).innerHTML;
        document.body.innerHTML = "<html><head><title></title></head><body>" + divElements + "</body>";

        window.print();
        document.body.innerHTML = oldPage;
        window.location.reload();
    }

    $(function(){
        $("#usertypeID").val(0);
        $("#classesID").val(0);
        $("#sectionID").val(0);
        $("#userID").val(0);
        $("#type").val(0);
        $("#background").val(0);
        $('#classesDiv').hide('slow');
        $('#sectionDiv').hide('slow');
        $('#userDiv').hide('slow');
        $(".select2").select2();
    });

    $(document).on('change', "#usertypeID", function() {
        $('#load_idcardreport').html("");
        var usertypeID = $(this).val();
        var classesID = $("#classesID").val();
        var sectionID = $("#sectionID").val();
        var idcardtext = $('#usertypeID option:selected').text();
        var error = 0;

        $('#userDivlabel').text(idcardtext);
        if(usertypeID == '0'){
            $('#classesDiv').hide('slow');
            $('#sectionDiv').hide('slow');
            $('#userDiv').hide('slow');
        } else if(usertypeID == '3') {
            $("#classesID").val(0);
            $("#sectionID").val(0);
            $("#userID").val(0);
            $('#classesDiv').show('slow');
            $('#sectionDiv').show('slow');
            $('#userDiv').show('slow');
        } else if(usertypeID !='3') {
            $("#classesID").val(0);
            $("#sectionID").val(0);
            $("#userID").val(0);
            $('#classesDiv').hide('slow');
            $('#sectionDiv').hide('slow');
            $('#userDiv').show('slow');
        }

        var passData = {
            'usertypeID':usertypeID,
            'classesID':classesID,
            'sectionID':sectionID,
        }

        if(usertypeID > 0)  {
            $.ajax({
                type : 'POST',
                url  : '<?=base_url('idcardreport/getUser')?>',
                data : passData,
                success : function(data) {
                    $('#userID').html(data);
                }
            });
        }
    });

    $(document).on('change', "#classesID", function() {
        $('#load_idcardreport').html('');
        var usertypeID = $('#usertypeID').val();
        var classesID = $(this).val();
        if(classesID == 0) {
            $('#sectionID').html('<option value="0">'+"<?=$this->lang->line("idcardreport_please_select")?>"+'</option>');
            $('#userID').html('<option value="0">'+"<?=$this->lang->line("idcardreport_please_select")?>"+'</option>');
        } else {
            $.ajax({
                type:'POST',
                url:'<?=base_url('idcardreport/getSection')?>',
                data:{'classesID':classesID},
                success:function(data) {
                    $('#sectionID').html(data);
                }
            });
        }

        if(classesID > 0 && usertypeID == 3) {
            $.ajax({
                type:'POST',
                url:'<?=base_url('idcardreport/getStudentByClass')?>',
                data:{'usertypeID':usertypeID,'classesID':classesID},
                success:function(data) {
                    $('#userID').html(data);
                }
            });
        }
    });

    $(document).on('change', "#sectionID", function() {
        $('#load_idcardreport').html('');
        var usertypeID = $('#usertypeID').val();
        var classesID = $('#classesID').val();
        var sectionID = $('#sectionID').val();

        if(classesID > 0 && usertypeID == 3) {
            $.ajax({
                type:'POST',
                url:'<?=base_url('idcardreport/getStudentBySection')?>',
                data:{'usertypeID':usertypeID,'classesID':classesID,'sectionID':sectionID},
                success:function(data) {
                    $('#userID').html('0');
                    $('#userID').html(data);
                }
            });
        }
    });

    $(document).on('change', "#userID", function() {
        $('#load_idcardreport').html("");
    });


    $(document).on('change', "#type", function() {
        $('#load_idcardreport').html("");
    });

    $(document).on('change', "#background", function() {
        $('#load_idcardreport').html("");
    });

    $(document).on('click','#get_idcardreport', function() {
        var usertypeID = $('#usertypeID').val();
        var classesID = $('#classesID').val();
        var sectionID = $('#sectionID').val();
        var userID    = $('#userID').val();
        var type      = $('#type').val();
        var background= $('#background').val();
        var error = 0;
        var field = {
            'usertypeID': usertypeID,
            'classesID' : classesID,
            'sectionID' : sectionID,
            'userID'    : userID,
            'type'      : type,
            'background': background,
        }

        if(usertypeID == 0 ) {
            $('#usertypeIDDiv').addClass('has-error');
            error++;
        } else {
            $('#usertypeIDDiv').removeClass('has-error');
        }

        if(usertypeID == 3 && classesID == 0 ) {
            $('#classesDiv').addClass('has-error');
            error++;
        } else {
            $('#classesDiv').removeClass('has-error');
        }

        if(type == 0 ) {
            $('#typeDiv').addClass('has-error');
            error++;
        } else {
            $('#typeDiv').removeClass('has-error');
        }

        if(background == 0 ) {
            $('#backgroundDiv').addClass('has-error');
            error++;
        } else {
            $('#backgroundDiv').removeClass('has-error');
        } 

        if(error == 0 ) {
            makingPostDataPreviousofAjaxCall(field);
        }
    });

    function makingPostDataPreviousofAjaxCall(field) {
        var passData = field;
        ajaxCall(passData);
    }

    function ajaxCall(passData) {
        $.ajax({
            type:'POST',
            url:'<?=base_url('idcardreport/getIdcardReport')?>',
            data:passData,
            dataType:'html',
            success:function(data) {
                var response = JSON.parse(data);
                renderLoder(response, passData);
            }
        });
    }

    function renderLoder(response, passData) {
        if(response.status) {
            $('#load_idcardreport').html(response.render);
            for (var key in passData) {
                if (passData.hasOwnProperty(key)) {
                    $('#'+key).parent().removeClass('has-error');
                }
            }
        } else {
            for (var key in passData) {
                if (passData.hasOwnProperty(key)) {
                    $('#'+key).parent().removeClass('has-error');
                }
            }

            for (var key in response) {
                if (response.hasOwnProperty(key)) {
                    $('#'+key).parent().addClass('has-error');
                }
            }
        }
    }

</script>


