jQuery(window).load(function () {
var size = 1;
var normal_size_class = "gallery-content-center-normal";
var full_size_class = "gallery-content-center-full";
var jQuerycontainer = jQuery('.gallery-content-center-manage');
    
jQuerycontainer.isotope({itemSelector : 'img'});

function check_size(){
	jQuery(".gallery-content-center-manage").removeClass(normal_size_class).removeClass(full_size_class);
	if(size==0){
		jQuery(".gallery-content-center-manage").addClass(normal_size_class); 
		jQuery("#gallery-header-center-left-icon").html('<span class="iconb" data-icon="&#xe23a;"></span>');
		}
	if(size==1){
		jQuery(".gallery-content-center-manage").addClass(full_size_class); 
		jQuery("#gallery-header-center-left-icon").html('<span class="iconb" data-icon="&#xe23b;"></span>');
		}
	jQuerycontainer.isotope({itemSelector : 'img'});
}

jQuery("#gallery-header-center-left-icon").click(function() { if(size==0){size=1;}else if(size==1){size=0;} check_size(); });

check_size();
});
