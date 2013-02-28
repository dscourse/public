(function( $ ){
  var methods = {
    init : function( options ) { 
        var min, max;
        min = (options.min)?options.min:0;
        max = (options.max)?options.max:500;
        
        if($(this).siblings('.wordCount').length == 0){
            $(this).after($('<span>',{
                html: $(this).val().length + '/' + max,
                css: {
                    verticalAlign: 'bottom',
                    position: 'relative',
                    left: '5px'
                }
            }).addClass('wordCount'));
        }
        else
            $(this).siblings('.wordCount').css('vertical-align', 'bottom').html($(this).val().length + '/' + max);
        
        $(this).on('keyup', function(){
            if($(this).val().length > max || $(this).val().length < min)
                $(this).next('.wordCount').css('color','red');
            else
                $(this).next('.wordCount').css('color','black');
           $(this).next('.wordCount').html($(this).val().length + '/' +max);
       });
    }
  };
  $.fn.counter = function( method ) {
    // Method calling logic
    if ( methods[method] ) {
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.counter' );
    }    
  };
})( jQuery );