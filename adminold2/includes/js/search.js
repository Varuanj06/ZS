/*
 * 
 * 	By David Blanco
 * 
 * 
 */

(function($) {

    var Search = function(element, options){
        //Defaults are below
        var settings = $.extend({}, $.fn.search.defaults, options);
        

        var table = settings.table,
		tbody = table.children('tbody'),
		tr    = tbody.children('tr');
	
		$(element).keyup(function(){
			var value = $(this).val();
			tr.each(function(){
				$this = $(this);

				var encontro = false;
				$this.find('td').each(function(){
					if( $(this).text().toLowerCase().indexOf(value.toLowerCase()) !== -1 ){
                        if(settings.searchOnlyInTarget){
                            if( $(this).hasClass('search-target') == true ){
                                encontro = true;
                            }
                        }else{
                            encontro = true;
                        }
					}
				});
				if(!encontro){
					$this.hide();
				}else{
					$this.show();
				}
			});
		});

        return this;

    };//END


        
    $.fn.search = function(options) {
        return this.each(function(key, value){
            var element = $(this);
            // Return early if this element already has a plugin instance
            if (element.data('search')) return element.data('search');
            // Pass options to plugin constructor
            var search = new Search(this, options);
            // Store plugin object in this element's data
            element.data('search', search);
        });
    };
  
    //Default settings
    $.fn.search.defaults = {
        table: $('#table'),
        searchOnlyInTarget: false,
    };
    
    $.fn._reverse = [].reverse;
  
})(jQuery);