// call lazyload on dom ready
$(document).ready(function(){
	var myLazyLoad = new LazyLoad({
		elements_selector: ".img-lazy"
	});	
});
function __deferFile( o ){
	
	var ele = document.createElement( o.tag );
	
	if( o.tag == 'script' ){
		ele.type = 'text/javascript';
		ele.src = o.src;
	}else{
		ele.rel = 'stylesheet';
		ele.href = o.src;
		ele.type = 'text/css';
	}
	
	var h = document.getElementsByTagName('link')[0];
	h.parentNode.insertBefore(ele, h);
}
var styles = [
	'css/common.css'
];

styles.forEach(function(s){
	__deferFile({
		tag: 'link',
		src: s
	});
});