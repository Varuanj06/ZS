    
/* ======================================================= 
 *
 *      Popup 
 *      By David Blanco
 *      
 * ======================================================= */

(function( window, $, undefined ){

/* ====================================================================== *
        CLASS
 * ====================================================================== */    

    var Popup = function(container, options){
        this.init(container, options);
    }

/* ====================================================================== *
        INIT
 * ====================================================================== */    

    Popup.prototype.init = function(element, options){   
        
    /* ====================================================================== *
            SETUP
     * ====================================================================== */
    
        var body            = $(document.body);
        var $element        = $(element);
        
    /* ====================================================================== *
            CREATE HTML
     * ====================================================================== */    
    
        // Add HTML for background
        
        if($('.popup-background')[0] == undefined){
            body.prepend('<div class="popup-background"></div> ');
        }
    
        // Add extra HTML for modal
        
        if($element.attr('wrapped') == undefined){
            var html        =   ' <div class="popup-modal"><div class="popup-content"></div><span class="fa fa-window-close" /></div> '; 
            $element.wrapAll(html);
            $element.attr('wrapped', 'true');
            $element.show();
        }

        // Get modal and background

        var modal       = $element.closest('.popup-modal');
        var background  = $('.popup-background');
        
        // Show modal with effect
        
        setTimeout(function(){
            modal.addClass('popup-show');
            background.addClass('popup-show');
        }, 1)
        
        // Full height
        
        if($element.attr('full-height') == 'true'){
            modal.filter('.popup-modal').css('height', '90%');
        }

        // Add padding-right to body (remove scrollbar)

        if( body.css('padding-right').split('px').join('') <= 0  && body.css('margin-right').split('px').join('') <= 0 && false){
            body.css({
                'padding-right' : getScrollbarWidth()+'px',
                'overflow'    : 'hidden',
            });
            body.attr('changed-scroll', true);
        }else{
            body.attr('changed-scroll', false);
        }
        
        body.addClass('popup-open');
    
    /* ====================================================================== *
            CLOSE MODAL
     * ====================================================================== */                
    
        // Close on BG click
            
        background.on('click', function(){
            close_modal(true);
        });
        
        // Close on icon
        
        modal.find('.fa-window-close').on('click', function(){
            close_modal(true);
        });

        function close_modal(close_bg){

            body.removeClass('popup-open');
            modal.removeClass('popup-show');
            if(close_bg){
                background.removeClass('popup-show');
            }

            if(body.attr('changed-scroll')){
                setTimeout(function(){
                    body.css({
                        'padding-right' : '',
                        'overflow-y'    : '',
                    });
                }, 0); // before 200
            }

        }
        
        this.close = function () {
            close_modal(true);
        } 

        this.close_withouth_bg = function () {
            close_modal(false);
        } 
    
    /* ====================================================================== *
            GET SCROLLBAR WIDTH
     * ====================================================================== */                    

        function getScrollbarWidth() {
            var outer = document.createElement("div");
            outer.style.visibility = "hidden";
            outer.style.width = "100px";
            outer.style.msOverflowStyle = "scrollbar"; // needed for WinJS apps

            document.body.appendChild(outer);

            var widthNoScroll = outer.offsetWidth;
            // force scrollbars
            outer.style.overflow = "scroll";

            // add innerdiv
            var inner = document.createElement("div");
            inner.style.width = "100%";
            outer.appendChild(inner);        

            var widthWithScroll = inner.offsetWidth;

            // remove divs
            outer.parentNode.removeChild(outer);

            return widthNoScroll - widthWithScroll;
        }    
              

    };//END OF INIT   

/* ====================================================================== *
        POPUP PLUGIN
 * ====================================================================== */

    $.fn.popup = function(options, content, callback) {

        return this.each(function(key, value){
            var $this   = $(this);
            var data    = $this.data('popup')
            
            // Initialize plugin
            if (typeof options != 'string'){
                $this.data('popup', new Popup(this, options));
            }

            // Call method
            if (data && typeof options == 'string'){
                data[options](content, callback);    
            }
        });

    };      
    
})( window, jQuery );