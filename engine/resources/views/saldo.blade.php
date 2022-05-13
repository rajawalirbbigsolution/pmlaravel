@extends('layouts.app')  
@section('1')
	<style>
		.asubmit{
			border: none!important;
		}
	</style>
@endsection
@section('2') 
		<table style="width: 800px;" align="center">
	<tr>
		<td>
			<div class="col-xs-12" style="margin-top:10px;">  
				<div id="bforma" class="col-xs-8 col-xs-offset-2" style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); "> 
					<div class="form-group" style="margin-top: 10px !important; "> <a href="{{action('b@a')}}"><span class="glyphicon glyphicon-arrow-left" style="color: #8e8e8e;!important;"></span></a></div>
					<h4 class="text-center" style="color: #022c69; margin:22px;">Saldo</h4>
					<h6 class="text-center" style="color: #022c69; margin:22px;">Saldo anda saat ini</h6>
					<h6 class="text-center" style="color: #022c69; margin:22px;"><label>Rp.</label><label>{{$c}}</label><label>,-</label></h6>
					<div class="form-group">
						<label for="">Nominal saldo yang ingin diisikan (Rp.)</label>
						<input type="number" class="form-control" id="binput" name="binput" onkeypress="bkey();" min="0">
					</div>
					<p> Saldo : <label>Rp.</label> <label for="" id="blabela">0</label><label>,-</label>   </p>
					<p> Sumbangan infrastruktur  server : <label>Rp.</label> <label for="" id="blabelb">20000</label><label>,-</label></p>
					<p> Sumbangan peningkatan kualitas jaringan backbone internet : <label>Rp.</label> <label for="" id="blabelc">20000</label><label>,-</label></p>
					<p> Kode unik : <label>Rp.</label> <label for="" id="blabeld">{{$a}}</label><label>,-</label></p>
					<p> Total bayar : <label>Rp.</label> <label for="" id="blabele"> 0</label><label>,-</label></p>
					<div class="form-group">	<button class="btn btn-primary" onclick="bsimpan();" id="bbutton">Isi Saldo</button></div> 
					<table class="table table-active text-center" id="btable">
						<tr>
							<th>Nomor struk</th>
							<th>Nominal transfer</th>
							<th>Verifikasi</th>
							<th>Struk</th>
						</tr> 
					</table> 
				</div>
				<div id="bformb" class="hide" style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); "> 
						<h2>Pembayaran melalui  BCA</h2> 
						<p>1. Datang ke Bank BCA </p>
						<p>2. Kirim/transfer dana ke PT. Parulian Produk Presisi</p>
						<p>3. Nomor Rekening 860-0671813</p>
						<p>4. Transfer dengan nominal sama persis dengan angka nominal transfer yang tertera</p>
						<p>5. Upload dokumen bukti transfer</p> 
						<p class="text-center"><a ><span class="glyphicon glyphicon-circle-arrow-left" style="color: #49699e!important; cursor: pointer;" onclick="bkembali();"></span></a></p>
					</div> 
				</div> 
 <h1>&nbsp;</h1>
<h1>&nbsp;</h1> 
		</td>
	</tr>
</table> 
@endsection 
@section('3') 
	<script >
        $(document).ready(function () {
            $('#binput').focus();
            bdata();
        });
        function bkey() {
            $("input[name='binput']").keyup(function (e) {
                if (e.keyCode===13){
                    $('#bbutton').click();
                }
                
                bupdate();
                e.fallback();
            });
        }
        function bupdate(){
            
            $('#blabela').text($('#binput').val());
            var bvara = $('#blabela').text();
            var bvarb = $('#blabelb').text();
            var bvarc = $('#blabelc').text();
            var bvard = $('#blabeld').text();
	        if($('#binput').val()===""){
	                $('#blabele').text(0);
	            }
            else if(parseInt(bvara)!==null){
                $('#blabele').text(parseInt(bvara)+parseInt(bvarb)+parseInt(bvarc)+parseInt(bvard));
            } 
        }
        function bsimpan() {
            $('#bbutton').attr('disabled', 'disabled'); 
            $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $.post(
                '{{action('g@b')}}',
                {
                    _token : $('meta[name="tn"]').attr('content'),
                    a:$('#blabela').text(),
                    b:$('#blabelb').text(),
                    c:$('#blabelc').text(),
                    d:$('#blabeld').text(),
                    e:$('#blabele').text() ,
	                f: $('#binput').val()
                },
                function (r){
                    $('#bbutton').attr('disabled', false);
                    $.notifyClose();
                    notificationdata = r['a'];
                    notification();
                    if (parseInt(r['b'])===1){ 
                        $('#bforma').attr('class', 'hide');
                        $('#bformb').attr('class', 'col-xs-8 col-xs-offset-2 text-left');
                    } 
                }
            );
        }
        function bkembali() {
            $('#bforma').attr('class', 'col-xs-8 col-xs-offset-2');
            $('#bformb').attr('class', 'hide');
            bdata()
        }
        function bdata() { 
            $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $.post(
                '{{action('g@c')}}',
                {
                    _token : $('meta[name="tn"]').attr('content')
                },
                function (r){ 
                    $.notifyClose();  
                    $('#btable').html('');
                    var bvartable= ""; 
                    bvartable += '<tr>';
                    bvartable += '<th>Nomor struk</th>';
                    bvartable += '<th>Nominal transfer</th>';
                    bvartable += '<th>Verifikasi</th>';
                    bvartable += '<th>Struk</th>';
                    bvartable += '</tr>';
                    bvartable += '<tr>';
                    $.each(r['a'], function (bdataa, bdatab) {
                        bvartable += '<td>'+bdatab.b+'</td>';
                        bvartable += '<td>Rp. '+bdatab.e+',-</td>'; 
                        if (parseInt(bdatab.c)===0){
                            bvartable += '<td id="btdupload'+bdatab.id+'">'; 
                            bvartable += ' <form method="post" id="eform'+bdatab.id+'" enctype="multipart/form-data">';
                            bvartable += '  {{csrf_field()}}';
                            bvartable += ' <div id="ediva'+bdatab.id+'">';  
                            bvartable += ' <input type="hidden" name="einputid" value="'+bdatab.id+'">';  
                            bvartable += ' <label for="efile'+bdatab.id+'" class="label label-published" style="cursor: pointer;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Pilih File Untuk Diupload" onclick="buploadswitch(1,'+bdatab.id+');">Upload dokumen <i id="eprogress'+bdatab.id+'" style="font-style: normal !important; width: 100%;"></i></label>';
                            bvartable += ' <input type="file" name="efile" accept=".png,.jpg"  style="opacity: 0" id="efile'+bdatab.id+'" class="hidden">';
                            bvartable += ' </div>'; 
                            bvartable += ' <div class="hide" id="edivb'+bdatab.id+'">';
                            bvartable += ' <div class="btn-group">';
                            bvartable += ' <button id="ebutton'+bdatab.id+'" onclick="buploadswitch(2,'+bdatab.id+');" type="button" class="btn btn-xs btn-like asubmit">Batal</button>'; 
                            bvartable += ' <button id="ebutton'+bdatab.id+'" onclick="bupload(1,'+bdatab.id+');" type="submit" class="btn btn-xs btn-like asubmit">Kirim</button>'; 
                            bvartable += ' </div>';
                            bvartable += ' </div>'; 
                            bvartable += ' </form>'; 
                            bvartable += ' <div class="hide" id="edivc'+bdatab.id+'">'; 
                            bvartable += ' <label class="label label-published">Memeriksa dokumen</label>'; 
                            bvartable += ' </div>'; 
                            bvartable += '</td>';
                        }
                        else if (parseInt(bdatab.c)===1){
                            bvartable += '<td><span class="glyphicon glyphicon-ok-circle"></span> </td>';
                        } 
                        else if (parseInt(bdatab.c)===2){
                            bvartable += '<td><label class="label label-published">Memeriksa dokumen</label> </td>';
                        } 
                        else if (parseInt(bdatab.c)===3){
                            bvartable += '<td><label class="label label-published"><span class="glyphicon glyphicon-remove-circle"></span> Tidak valid</label> </td>';
                        } 
                        bvartable += '<td><a href="{{action('h@c')}}/'+bdatab.id+'"><span class="glyphicon glyphicon-search" style="color: #8e8e8e !important;"></span></a></td>';
                        bvartable += '    </tr>';             
                    }); 
                    $('#btable').append(bvartable);
                }
            );
        }
        function buploadswitch(buploadid1, buploadid2) {
            if (parseInt(buploadid1)===1){ 
                $('#ediva'+buploadid2).attr('class', 'hide');
                $('#edivb'+buploadid2).attr('class', 'form-group');
                $('#edivc'+buploadid2).attr('class', 'hide');
            }
            else if (parseInt(buploadid1)===2){ 
                $('#ediva'+buploadid2).attr('class', 'form-group');
                $('#edivb'+buploadid2).attr('class', 'hide');
                $('#edivc'+buploadid2).attr('class', 'hide');
                
            }
            else if (parseInt(buploadid1)===3){ 
                $('#ediva'+buploadid2).attr('class', 'hide');
                $('#edivb'+buploadid2).attr('class', 'hide');
                $('#edivc'+buploadid2).attr('class', 'form-group');
                
            }
        }
        function bupload(buploadid1, buploadid2) {
            $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $('#edivb').attr('class', 'form-group');
            $('#eform'+buploadid2).on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{action('g@d')}}",
                    method: "POST",
                    data: new FormData(this),
                    datatype: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        $("#ebutton"+buploadid2).prop('disabled', true);
                    },
                    success: function (r) { 
                        $.notifyClose();
                        notificationdata = r['a'];
                        notification();
                        $("#ebutton"+buploadid2).prop('disabled', false);
                        if (parseInt(r['b'])===1){
                            $(''+buploadid2)
                            buploadswitch(3, buploadid2);
                        }
//                        location.reload();
                    },
                    xhr: function () {
                        var xhr = $.ajaxSettings.xhr();
                        xhr.upload.onprogress = function (data) {
                            var perc = Math.round((data.loaded / data.total) * 100); 
                            $('#eprogress').val(perc + '%');
                            $('#eprogress').text(perc + '%');
                            $('#eprogress').css('width', perc + '%');
                        };
                        return xhr;
                    },
                });
            });
        }
	</script>
@endsection 
