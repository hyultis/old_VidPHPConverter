$(document).ready(main);
var launched = false;


function main()
{
	//form
	$("#allfile").change(function () {
          if($('#allfile option:selected').val() == 0)
          {
          	$("#secondchoix").addClass("inactive");
          	$("#method").attr("disabled","disabled");

          }
          else
          {
          	$("#secondchoix").removeClass("inactive");
          	$("#method").removeAttr("disabled");
          }
        })
		
	
	
}

function form_submit()
{
	if($('#allfile option:selected').val() == "0")
		get_allfile();
	else
		launch_convert();
}

function launch_convert()
{
	if(launched)
	{
		alert('Une convertion est deja en cours');
	}
	
	launched = true;
	if($('#method option:selected').val()==1)
	{
		launch_randimg();
	}
	else if($('#method option:selected').val()==2)
	{
		launch_gif();
	}
	else
	{
		launch_vid();
	}
}

function launch_randimg()
{
	create_barre_avancement('#avancement',400);
	barre_set_avancement('#avancement',0);

	$.ajax({
		url: "EX_convert.php?file="+$('#allfile option:selected').text()+"&method=1",
		global: false,
		type: "GET",
		cache: false,
		dataType: "text",
		success: function(data)
		{
			$('#avancement').empty();
			$('#avancement').append('<img src="resultat/'+$('#allfile option:selected').text()+'.jpg"/>');
			launched = false;
		}
	});
}

function launch_gif()
{
	create_barre_avancement('#avancement',400);
	barre_set_avancement('#avancement',0);

	var xhr = $.ajax({
		url: "EX_convert.php?file="+$('#allfile option:selected').text()+"&method=2",
		global: false,
		type: "GET",
		cache: false,
		dataType: "text"
	});
	
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 3){
			finder = xhr.responseText.split("\n");
			finder = finder[(finder.length-2)].split(",");
			finder = finder[0].split("=");
			finder = finder[1].split("/");
			barre_set_avancement('#avancement',(finder[0]/30)*100);
		}
		if(xhr.readyState == 4)
		{
			$('#avancement').empty();
			$('#avancement').append('<img src="resultat/'+$('#allfile option:selected').text()+'.gif"/>');
			launched = false;
		}
	};
}

function launch_vid()
{
	create_barre_avancement('#avancement',400);
	barre_set_avancement('#avancement',0);
	
	var nbframe = $.ajax({
		url: "EX_convert.php?file="+$('#allfile option:selected').text()+"&method=0",
		global: false,
		type: "GET",
		cache: false,
		dataType: "text",
		async: false
	}).responseText;

	var xhr = $.ajax({
		url: "EX_convert.php?file="+$('#allfile option:selected').text()+"&method=3",
		global: false,
		type: "GET",
		cache: false,
		dataType: "text"
	});
	
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 3){
			//alert(finder[(finder.length-2)]);
			var reg=new RegExp("frame=[ ]+([0-9]+)","g");
			var regdata = xhr.responseText.match(reg);
			if(regdata != null)
			{
				if(regdata.length>1)
				{
					regdata = regdata[(regdata.length-1)].split("=");
					barre_set_avancement('#avancement',(regdata[1]/nbframe)*100);
				}
			}
		}
		if(xhr.readyState == 4)
		{
			$('#avancement').empty();
			$('#avancement').append('<object id="flashplayer" type="application/x-shockwave-flash" data="data/player_flv_maxi.swf" width="640" height="480">'+
						'<param name="movie" value="data/player_flv_maxi.swf" />'+
						'<param name="allowFullScreen" value="true" />'+
						'<param name="FlashVars" value="flv=../resultat/'+
						$('#allfile option:selected').text()+'.flv'+
						'&amp;showstop=1&amp;showvolume=1&amp;showtime=0&amp;showfullscreen=1&amp;autoplay=1" />');

			launched = false;
		}
	};
}

function get_allfile()
{
	$.ajax({
		url: "EX_filelist.php",
		global: false,
		type: "GET",
		cache: false,
		dataType: "text",
		success: function(data)
		{
			data = data.split(';');
			$('#allfile').empty();
			$('#allfile').append(new Option("Rafraichir liste","0"));
			for(t=0;t<data.length-1;t++)
				$('#allfile').append(new Option(data[t],""+(t+1)));
		}
	});
}

function create_barre_avancement(elem,w)
{
	barre = $('<div id="barre" style="width:'+w+'px;">'+
			'<div id="barre_size" style="background-image:url(\'data/barre.png\');width:1px;top:0px;"></div>'+
			'<div id="barre_value" style="width:'+w+'px;top:-50px;">10%</div>'+
			'<div style="background-image:url(\'data/borderleft.png\');"></div>'+
			'<div style="background-image:url(\'data/border.png\');width:'+(w-100)+'px;"></div>'+
			'<div style="background-image:url(\'data/borderright.png\');"></div>'+
		'</div>')
	$(elem).append(barre);
}

function barre_set_avancement(elem,a)
{
	a = Math.round(a);

	if(a<0)
		a=0;
	if(a>100)
		a=100;

	$(elem+' #barre_value').text(a+'%');

	if(a<1)
		a=1;		
	$(elem+' #barre_size').width(a+'%');
	
}
