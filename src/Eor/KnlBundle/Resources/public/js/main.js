(function(){
	$(".popmenu").each(function(){
		var $this = $(this);
		$this.find("> a").click(function(){
			$("#popmenu_holder .content").empty().append(
				$this.find(".popmenu-items").clone()
			);
			$("#popmenu_holder, #popmenu_holder_background").show();
		});
	});
	
	$("#popmenu_holder_close").click(function(){
		$("#popmenu_holder, #popmenu_holder_background").hide();
	});
})();