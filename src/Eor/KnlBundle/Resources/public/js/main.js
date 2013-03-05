(function(){
	$(".popmenu").each(function(){
		var $this = $(this);
		$this.find("> a").click(function(){
			$("#popmenu_holder .content").empty().append(
				$this.find(".popmenu-items").clone()
			);
			$("#popmenu_holder, #popmenu_holder_background").show();
			return false;
		});
	});
	
	$("#popmenu_holder_close").click(function(){
		$("#popmenu_holder, #popmenu_holder_background").hide();
		return false;
	});
	
	$("#item_detail_star").click(function(){
		var newState = !($(this).attr('data-isset') == '1');
		Reader.setState(Reader.currentItem.id, Reader.currentItem.originId, Reader.states.star, newState);
		$(this).find('img').attr('src', Reader.routes.asset_prefix+'bundles/eorknl/img/icon/'+(newState? 'star':'unstar')+'.png');
		$(this).attr('data-isset', newState? '1':'0');
		return false;
	});
	
	$("#item_detail_read").click(function(){
		var newState = !($(this).attr('data-isset') == '1');
		Reader.setState(Reader.currentItem.id, Reader.currentItem.originId, Reader.states.read, newState);
		$(this).find('img').attr('src', Reader.routes.asset_prefix+'bundles/eorknl/img/icon/'+(newState? 'eye_open':'eye_close')+'.png');
		$(this).attr('data-isset', newState? '1':'0');
		return false;
	});
})();

Reader = {};

Reader.forceRefresh = function(){
	$.get(Reader.routes.force_refresh, function(){
		window.location.reload();
	});
	
	return false;
}

Reader.setState = function(itemId, originId, state, set, onFinish){
	onFinish = onFinish || function(){};
	$.post(Reader.routes.set_state, {
		item_id: itemId,
		origin_id: originId,
		state: state,
		set: set? 1:0
	}, onFinish);
}