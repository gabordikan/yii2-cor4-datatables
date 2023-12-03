function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for(let i = 0; i <ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function cor4DataTables( selector, options ) {

    jQuery(selector).DataTable({

      searching: false,
      //ajax: '/api/listkess',
      paging: true,
      scrollX: true,
      scroller: true,
      scrollY: '100%',
      pageLength: 25,
      order: options.order,
      buttons: [
        'click'
      ],

      initComplete : function() {

        var table = this.api();
        $('<tr></tr>').appendTo($('thead'));
  
        // Add filtering
        table.columns().every(function() {
          var column = this;

          $('<th></th>').appendTo($("thead tr:eq(1)"));
      
          let th_style = $("thead tr:eq(0) th").eq(this.index()).attr('style');
          let th_value = $("thead tr:eq(0) th").eq(this.index()).text();
          /*let searchParams = new URLSearchParams(window.location.search);
          let param = searchParams.get("search["+this.index()+"]");*/

          param = getCookie("search["+this.index()+"]");
          if (param == null) {
            param = '';
          }
          if (th_value.length > 1) {
            $('<input type="text" style="'+th_style+'" value="'+param+'"/>')
              .appendTo($("thead tr:eq(1) th").eq(this.index()))
              .on("keyup", function(evt) {
                if(evt.key == 'Enter') {
                  var searchText = '';
                  table.columns().every(function() {
                    let th_value = $("thead tr:eq(0) th").eq(this.index()).text();
                    if (th_value.length > 1) {
                      searchText += 'search['+this.index()+']='+$("thead tr:eq(1) th input").eq(this.index()).val()+'&';
                    }
                  });
                  window.location = '/site/listkess?'+searchText;
                }
              });
          } else {
            $('<button text=""><i class="fa-solid fa-rotate"></button>')
            .appendTo($("thead tr:eq(1) th").eq(this.index()))
              .on("click", function(evt) {
                var searchText = '';
                table.columns().every(function() {
                  let th_value = $("thead tr:eq(0) th").eq(this.index()).text();
                  if (th_value.length > 1) {
                    searchText += 'search['+this.index()+']='+$("thead tr:eq(1) th input").eq(this.index()).val()+'&';
                  }
                });
                window.location = '/site/listkess?'+searchText;
              });
          }
        });
    }
    });
}
