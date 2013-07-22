<script>
	window.fbAsyncInit = function() {
    // init the FB JS SDK
		FB.init( {
			app_id		:  '250524601748571',
			xfbml       : true  // parse XFBML tags on this page?
		});
	};
	
	(function(d, debug){
		var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement('script'); js.id = id; js.async = true;
		js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
		ref.parentNode.insertBefore(js, ref);
		}(document, /*debug*/ false));

	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/nl_NL/all.js#xfbml=1&appId=250524601748571";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));

	!function(d,s,id){
		var js,fjs=d.getElementsByTagName(s)[0];
		if(!d.getElementById(id)){
			js=d.createElement(s);
			js.id=id;js.src="//platform.twitter.com/widgets.js";
			fjs.parentNode.insertBefore(js,fjs);
		}
	}(document,"script","twitter-wjs");
	
	function popitup(url) {
		newwindow=window.open(url,'name','height=400,width=400');
		if (window.focus) {newwindow.focus()}
		return false;
	}
	
	(function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-39120737-1']);
  _gaq.push(['_setDomainName', 'thoseannoyingdupes.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

  function validate(frm)  
{  
    var ele = frm.elements['playlist[]','tracks[]'];  
  
    if (! ele.length)  
    {  
        alert(ele.value);  
    }  
  
    for(var i=0; i<ele.length; i++)  
    {  
        alert(ele[i].value);  
    }  
  
    return true;  
}  
  
function add_pl_feed()  
{  
    var div1 = document.createElement('div');  
 
    // Get template data  
    div1.innerHTML = document.getElementById('newplaylist').innerHTML;  
  
    // append to our form, so that template data  
    //become part of form  
    document.getElementById('newlink').appendChild(div1);  
  
}  

function add_tr_feed()  
{  
    var div1 = document.createElement('div');  
 
    // Get template data  
    div1.innerHTML = document.getElementById('newtracklist').innerHTML;  
  
    // append to our form, so that template data  
    //become part of form  
    document.getElementById('newlink2').appendChild(div1);  
  
} 
</script>			