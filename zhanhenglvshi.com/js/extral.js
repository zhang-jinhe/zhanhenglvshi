if(self!=top){
$("div[layout_type='module']").each(function() {
var call = this;
var text="<style>.moset{position:relative;border:1px dashed #2a900b;;padding:5px;left:0;top:0; z-index:99;background:#f7f7f7; }</style>";
text+="<div class='moset'   name='"+$(call).attr("id")+"' >";	
text+=" "+$(call).attr("id")+" ";
text+="</div>";
$(call).prepend(text);  
$(call).find(".moset").hide();
$(call).mouseenter(function(){
$(call).find(".moset").show();
}).mouseleave(function(){
$(call).find(".moset").hide();
}); 
		
});  
}

