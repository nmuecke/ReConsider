  /*
   * function that hides html elemant by using 
   * their ID tag
   */
  function hideShowByClass(id) {
        //var obj = document.getElementsByTagName("tr");
        var obj = document.getElementsByName(id);

	if (!obj){ 
          return;
          }

        var cookieData = getCookie("hideStuff").split(":");
        var el = '';
        var str = '';

        for(var xx = 0; xx < cookieData.length; xx++ ){
           if( cookieData[xx].indexOf(id+"=") > -1 ){
              el = cookieData[xx].split("=");
              }
           else{
              str += cookieData[xx]+":";
              }
           } 
	if (obj.style.display == "none" || el[1] == "hide" ){
           obj.style.display = "";
           
           document.cookie = "hideStuff="+ str + id + "=show";
           }
	else{
           obj.style.display = "none";
           document.cookie = "hideStuff="+ str + id + "=hide";
           }

        }

  /**
   * Hides or shows a html element based on the elements id
   */ 
  function hideShow(id) {
        var obj = document.getElementById(id);

        if (!obj){ 
          return;
          }

        var cookieData = getCookie("hideStuff").split(":");
        var el = '';
        var str = '';

        for(var xx = 0; xx < cookieData.length; xx++ ){
           if( cookieData[xx].indexOf(id+"=") > -1 ){
              el = cookieData[xx].split("=");
              }
           else{
              str += cookieData[xx]+":";
              }
           } 
	if (obj.style.display == "none" || el[1] == "hide" ){
           obj.style.display = "";
           
           document.cookie = "hideStuff="+ str + id + "=show";
           }
	else{
           obj.style.display = "none";
           document.cookie = "hideStuff="+ str + id + "=hide";
           }
        }

  /**
   * Hides a html element based on the elements id
   */ 
  function hide(id) {
        var obj = document.getElementById(id);

        if (!obj){
          return;
          }

        var cookieData = getCookie("hideStuff").split(":");
        var el = '';
        var str = '';

        for(var xx = 0; xx < cookieData.length; xx++ ){
           if( cookieData[xx].indexOf(id+"=") > -1 ){
              el = cookieData[xx].split("=");
              }
           else{
              str += cookieData[xx]+":";
              }
           }

        obj.style.display = "none";
        document.cookie = "hideStuff="+ str + id + "=hide";
        
        }

  /**
   * Shows a html element based on the elements id
   */ 
  function show(id) {
        var obj = document.getElementById(id);

        if (!obj){
          return;
          }

        var cookieData = getCookie("hideStuff").split(":");
        var el = '';
        var str = '';

        for(var xx = 0; xx < cookieData.length; xx++ ){
           if( cookieData[xx].indexOf(id+"=") > -1 ){
              el = cookieData[xx].split("=");
              }
           else{
              str += cookieData[xx]+":";
              }
           }

        obj.style.display = "";
        document.cookie = "hideStuff="+ str + id + "=show";
        }

document.getElementsByClassName = function(clsName){
    var retVal = new Array();
    var elements = document.getElementsByTagName("*");
    for(var i = 0;i < elements.length;i++){
        if(elements[i].className.indexOf(" ") >= 0){
            var classes = elements[i].className.split(" ");
            for(var j = 0;j < classes.length;j++){
                if(classes[j] == clsName)
                    retVal.push(elements[i]);
            }
        }
        else if(elements[i].className == clsName)
            retVal.push(elements[i]);
    }
    return retVal;
}
   /*
    * when a page is reloaded this will make shore that 
    * things are the way the user left them
    *
    */
   function refreshHidden(){

        var cookieData = getCookie("hideStuff").split(":");
        var el = '';
        var str = '';

        for( var xx = 0; xx < cookieData.length; xx++ ){
           el = cookieData[xx].split("=");
           if( el.length == 2 ){ // to remove any erros or edits to the cookie that could brake the js
              var obj = document.getElementById(el[0]);
              if( obj != null ){ // to remove any erros or edits to the cookie that could brake the js
                 if ( el[1] == "hide" ){
                    obj.style.display = "none";
                    }
                 else{
                    obj.style.display = "";
                    }
                 str += cookieData[xx]+":"; // to remove any erros or edits to the cookie that could brake the js
		}		
              }
           } 
        document.cookie = "hideStuff="+ str;
        }


   /*
    * Gets the content of a cookie by it't name
    * 
    * source w3schools.com
    */
   function getCookie( cookieName ){
      if( document.cookie.length > 0 ){
         c_start = document.cookie.indexOf( cookieName + "=" )

         if( c_start != -1 ){ 
            c_start = c_start + cookieName.length + 1; 
            c_end = document.cookie.indexOf( ";", c_start );

            if( c_end == -1 ) 
               c_end = document.cookie.length;

            return unescape( document.cookie.substring( c_start, c_end ))
            } 
        }
     return ""
     }
