// run the show
function confirmRefresh( timeout, url ) 
   {
   // display the msg function after X milliseconds
   setTimeout( 'refreshMsg(' + timeout + ',\'' + url + '\' )', timeout);
   
   }

// display the msg box
function refreshMsg( timeout, url )
   {
   var okToRefresh = confirm("Would you like to check for changes to the dispue?");
   if( okToRefresh )
      {
      //location.reload(true);
      window.location.replace( url );
      }
   else
      {
      // loop the request
      confirmRefresh( timeout, url );
      }
   }
