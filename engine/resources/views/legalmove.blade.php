@extends('layouts.app')  
@section('1')
 
@endsection
@section('2')
<table style="width: 700px;" align="center">
	<tr>
		<td>
			<div class="col-xs-12" style="margin-top:10px;">  
				<div class="col-xs-8 col-xs-offset-2 text-center" style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); ">
					<div class="form-group text-left" style="margin-top: 10px !important; "> <a href="{{action('b@a')}}"><span class="glyphicon glyphicon-arrow-left" style="color: #8e8e8e;!important;"></span></a></div> 
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Nama akun legalmove (Email)" id="busername" name="busername" onkeypress="bkey();">
					</div>
					<div class="form-group">
					<button type="button" class="btn btn-xs btn-like" style="color: #8e8e8e!important; cursor: pointer;" onclick="bcek();" id="bplus"><span class="glyphicon glyphicon-plus"></span></button>
					</div> 
				     <table class="table table-active" id="btable">
					     <tr>
						     <th>Akun</th>
						     <th>Lembar</th>
						     <th>Tombol tindakan</th>
					     </tr>
					    
				     </table>  
					<div class="hide" id="btimeform">	
					<div class="form-group" id="btimeform">
						<h6><label id="namaperpanjangan"></label></h6> 	
						<label>Jumlah   </label> 	
						<input type="number" class="form-control" placeholder="Lembar" min="0" max="1000" onkeypress="binputharga();" id="bharga" name="bharga"></div>
						<div class="form-group">	<a style="color: #8e8e8e!important; cursor: pointer;" id="brefresh" onclick="bharga();"><span class="glyphicon glyphicon-refresh"></span></a></div>
						<div class="form-group"><label for="" id="blabelhari"> </label> <label for="">Lembar</label></div>
						<div class="form-group"><label for="">Total biaya :</label></div>
						<div class="form-group">	<label for="" id="blabelharga">Rp. 0,-</label></div>
						<div class="form-group">	<button class="btn btn-primary" id="bbeli" onclick="bbeli();">Beli</button></div>
					</div>   
					<div class="form-group">	<label for="">Saldo saya </label>  :	<label>Rp. </label> 	<label id="blabelsaldo">-</label><label>,-</label> <span class="glyphicon glyphicon-refresh" onclick="bsaldo();" style="cursor: pointer!important;"></span></div> 		
				</div>
				<div class="col-xs-8 col-xs-offset-2 text-center hide" style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); ">
					<div class="form-group text-center"><img class="img-responsive" style="display: initial!important;" src="{{asset('asset/img/true.png')}}">
						<h2 id="bnamaakun">Pembayaran selesai</h2>
						<h6>Pembayaran telah dilakukan</h6>
					
						<p>Unduh struk pembayaran</p>
						<p><a href="{{action('AuthC@L')}}"><span class="glyphicon glyphicon-home" style="color: #49699e!important;"></span></a></p>
					</div> 
				</div>
			</div>
			<div class="container col-xs-12">
				<div class="row"></div>
			</div>
			<div class="col-xs-12">
				<h1>&nbsp;</h1>
				<h1>&nbsp;</h1>
			</div>
		</td>
	</tr>
</table> 
@endsection 
@section('3') 
	<script >
        $(document).ready(function () {  
            bdata();
            bsaldo();
            $('#busername').focus();
        });
        function bkey() {
            $("input[name='busername']").keyup(function (e) {
                if (e.keyCode===13){
                    $('#bplus').click();
                }
                e.fallback();
            });
        }
        function binputharga() {
            $("input[name='bharga']").keyup(function (e) { 
                $('#brefresh').click();
                e.fallback();
            }); 
        }
		function btime(btimeid, btimeid2) {
		    if (parseInt(btimeid)===1){
                $('#btime'+btimeid2).attr('onclick', 'btime(2, '+btimeid2+');');
                $('#bbeli').attr('onclick', 'bbeli('+btimeid2+');');
                $('#btimeform').attr('class', 'form-group'); 
                var varbakun = $('#bakun' +btimeid2).html();
                $('#namaperpanjangan').html(varbakun); 
            }
		    else if (parseInt(btimeid)===2){
                $('#btime'+btimeid2).attr('onclick', 'btime(1, '+btimeid2+');');
                $('#btimeform').attr('class', 'hide');
                $('#bbeli').attr('onclick', '');
		    } 

			
        }
        function bcek() {
            $('#bplus').attr('disabled', true);
            $.notifyClose();
            notificationdata = 'Loading..';
            notification(); 
            $.post(
                '{{action('d@b')}}',
                {
                    _token : $('meta[name="tn"]').attr('content'),
	                a : $('#busername').val()
                },
                function (r){ 
                    $.notifyClose(); 
                    notificationdata = r['a'];
                    notification();
                    $('#bplus').attr('disabled', false);
                    if (parseInt(r['b'])===1){
                        bdata();
                    } 
                }
            );
        }
        function bdata() {
            $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $.post(
                '{{action('d@c')}}',
                {
                    _token : $('meta[name="tn"]').attr('content')
                },
                function (r){
                    $.notifyClose();
                    $('#btable').html('');
                    var bvartable= "";
                    bvartable += '<tr>';
                    bvartable += '<th>Akun</th>';
                    bvartable += '<th>Lembar</th>';
                    bvartable += '<th>Tombol tindakan</th>';
                    bvartable += '</tr>'; 
                  if (r['a']===null || parseInt(r['a'])===0 || r['a'].length===0){
                      bvartable += '<tr>';
                      bvartable += '<td>Email akun legalmove belum ada</td>';
                      bvartable += '<td>- </td>';
                      bvartable += '<td>-</td>';
                      bvartable += '</tr>';
                  }
                  else { 
                      $.each(r['a'], function (bdataa, bdatab) {
                          bvartable += '<tr>';
                          bvartable += '<td id="bakun'+bdatab.id+'">'+bdatab.b+'</td>';
                          bvartable += '<td>'+bdatab.d+' Lembar</td>';
                          bvartable += '<td><div class="btn-group">';
                          bvartable += '<a class="btn btn-xs btn-like" href="{{action('d@f')}}/'+bdatab.id+'" style="color: #8e8e8e!important; cursor: pointer;"><span class="glyphicon glyphicon-time"></span></a>';
                          bvartable += '<a class="btn btn-xs btn-like" onclick="btime(1, '+bdatab.id+');" id="btime'+bdatab.id+'" style="color: #8e8e8e!important; cursor: pointer;">Beli lembar</a></div></td>';
                          bvartable += '</tr>';
                      });
                  } 
                    $('#btable').append(bvartable);
                }
            );
        }
        function bharga() {
            $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $.post(
                '{{action('d@d')}}',
                {
                    _token : $('meta[name="tn"]').attr('content'),
	                a : $('#bharga').val()
                },
                function (r){
                    $.notifyClose(); 
                    $('#blabelhari').html(r['b']);
                    $('#blabelharga').html('Rp. '+r['a']+',-');
                }
            );
        }
        function bbeli(bbeliid) { 
            $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $.post(
                '{{action('d@e')}}',
                {
                    _token : $('meta[name="tn"]').attr('content'),
	                a : $('#bharga').val(),
	                b : bbeliid
                },
                function (r){ 
                    $.notifyClose();
                    notificationdata = r['a'];
                    notification();
                    if (parseInt(r['b'])===1) {
                        bsaldo();
                        bdata();
                        $('#bharga').val('');
                        $('#blabelhari').html('');
                        $('#blabelharga').html('Rp. 0 ,-');
                        
                    }
                }
            );
        }
        function bsaldo(){  
            $.post(
                '{{action('g@e')}}',
                {
                    _token : $('meta[name="tn"]').attr('content')
                },
                function (r){
                    $('#blabelsaldo').html(r['c']); 
                }
            );
        }
	</script>
@endsection
