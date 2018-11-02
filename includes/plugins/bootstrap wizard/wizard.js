$(document).ready(function(){

	$(".form-wizard").each(function(i, el){
		var $this = $(el),
			$tabs = $this.find('> .tabs > li'),
			$progress = $this.find(".progress-indicator"),
			_index = $this.find('> ul > li.active').index();

		// Validation
		var next = function(tab, navigation, index){
			if(tab.attr('data-id') == 'products'){
				// do nothing
			}else if(tab.attr('data-id') == 'address'){
				angular.element( $('[ng-app="cart"]')[0] ).scope().saveAddress();
			}else if(tab.attr('data-id') == 'voucher'){
				angular.element( $('[ng-app="cart"]')[0] ).scope().saveVoucherList();
			}
		};

		// Setup Progress
		if(_index > 0){
			$progress.css({width: _index/$tabs.length * 100 + '%'});
			$tabs.removeClass('completed').slice(0, _index).addClass('completed');
		}

		$this.bootstrapWizard({
			tabClass: "",
	  		onTabShow: function($tab, $navigation, index){
	  			var pct = $tabs.eq(index).position().left / $tabs.parent().width() * 100;

	  			$tabs.removeClass('completed').slice(0, index).addClass('completed');
	  			$progress.css({width: pct + '%'});
	  		},

	  		onNext: next,
	  		onTabClick: function(){ return false; },
	  	});

	  	$this.data('bootstrapWizard').show( _index );

	  	$this.find('.pager a').on('click', function(ev){
		  	ev.preventDefault();
	  	});

	  	//$this.bootstrapWizard('show',2);
	});

})