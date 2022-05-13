@extends('layouts.app')  
@section('2')
<table style="width: 600px;" align="center">
	<tr>
		<td>
			<div class="col-xs-12" style="margin-top:10px;" id="bdiva"> 
				<div class="col-xs-8 col-xs-offset-2 text-center" style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); "> 
					<h4 class="text-center" style="color: #022c69; margin:22px;">BUAT AKUN</h4>
				    <div class="form-group"><input type="text" id="ainputa" name="ainputa" class="form-control" placeholder="EMAIL" onkeypress="bkeya();"></div>
				    <div class="form-group"><input type="password" id="ainputb" name="ainputb" class="form-control" placeholder="PASSWORD"onkeypress="bkeyb();" required></div>
				    <div class="form-group"><input type="text" id="ainputc"  name="ainputc" class="form-control" placeholder="NO. SMS" onkeypress="bkeyc();"></div>
				    <div class="form-group"><button id="bbutton" type="button"  style="background:#898d94;color:#676767;border-color:#898d94 !important;" class="btn btn-lg btn-block" style="color: #70478f; border-color: #c7c7c7;  background: white;" onclick="bkirim();">BUAT AKUN</button></div> 
				</div>
			</div>
			
			
			<div class="hide" id="bdivb" style="background: #f8f8f8; border-radius: 10px; box-shadow: 0 1px 47px rgb(0, 0, 0); ">
				<div class="form-group text-center"><img class="img-responsive" style="display: initial!important;" src="{{asset('asset/img/true.png')}}">
					<h2 id="bnamaakun">Akun didaftarkan</h2>
					<h6>Akun didaftarkan</h6>
					<p><a href="{{action('AuthC@L')}}"><span class="glyphicon glyphicon-home" style="color: #49699e!important;"></span></a></p>
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
            $('#ainputa').focus();
        });
		function bkirim() {
		    $('#bbutton').attr('disabled', 'disabled');
		    
		    $.notifyClose();
            notificationdata = 'Loading..';
            notification();
            $.post(
                '{{action('a@b')}}',
                {
                    _token : $('meta[name="tn"]').attr('content'),
                    a:$('#ainputa').val(),
                    b:$('#ainputb').val(),
                    c:$('#ainputc').val() 
                },
                function (r){
                    $('#bbutton').attr('disabled', 'false');
                    $.notifyClose();
                    notificationdata = r['a'];
                    notification(); 
                    if (parseInt(r['b'])===0){
                        $('#ainputa').val('');
                        $('#ainputa').focus();
                    }
                    else if (parseInt(r['b'])===1){
                        $('#bdivb').attr('class', 'col-xs-8 col-xs-offset-2 text-center');
                        $('#bdiva').attr('class', 'hide');
                        $('#bnamaakun').html(r['c']);
                    }
                    else if (parseInt(r['b'])===3){
                        $('#ainputa').focus()
                    }
                    else if (parseInt(r['b'])===4){
                        $('#ainputb').focus()
                    }
                    else if (parseInt(r['b'])===5){
                        $('#ainputc').focus()
                        $('#bbutton').attr('disabled', false);
                    }
                }
            );
        }
        function bkeya() {
            $("input[name='ainputa']").keyup(function (e) {
                if (e.keyCode===13){
                    $('#bbutton').click();
                }
                e.fallback();
            });
        }
        function bkeyb() {
            $("input[name='ainputb']").keyup(function (e) {
                if (e.keyCode===13){
                    $('#bbutton').click();
                }
                e.fallback();
            });
        }
        function bkeyc() {
            $("input[name='ainputc']").keyup(function (e) {
                if (e.keyCode===13){
                    $('#bbutton').click();
                }
                e.fallback();
            });
        }
	</script>
@endsection
