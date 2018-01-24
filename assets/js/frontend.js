jQuery(document).ready(function($) {
  $('input[name=testing]').autocomplete({
    source: function(request, response){
      $.ajax({
        type: "POST", 
        url: ajax_object.ajax_url, 
        data: {
          action: 'myautocomplete',
          keyword: $('input[name=testing]').val(),
        },
        success:function(data) {
          console.log(data);
          response(JSON.parse(data));
        },
        error: function(errorThrown){
          console.log(errorThrown); 
        }
      })
    },
  });
})
