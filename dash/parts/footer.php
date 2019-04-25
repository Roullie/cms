		<!--<script type="text/javascript" src="<?=FOLDER?>js/dash-common.js"></script>-->
		<script type="text/javascript" src="<?=FOLDER?>js/jquery.min.js"></script>
		<script type="text/javascript" src="<?=FOLDER?>js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?=FOLDER?>js/jquery.ui.1.12.0.js"></script>
		<script type="text/javascript" src="<?=FOLDER?>js/jquery.form.min.js"></script>
		<script type="text/javascript" src="<?=FOLDER?>js/lazyload.min.js"></script>
		
		<script type="text/javascript" src="<?=FOLDER?>js/spectrum.js"></script>
		<script type="text/javascript" src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		<script src="<?=FOLDER?>js/tinymce.min.js"></script>
		
		<script type="text/javascript">
			
			$(document).ready(function(){
				if( $('[data-toggle="tooltip"]').length ){
					$('[data-toggle="tooltip"]').tooltip();
				}
				
				if( $('textarea.tinymce-area').length ){
				
					if( typeof(content_css) != 'undefined' ){
						content_css.push('<?=FOLDER?>css/tiny.css');
						content_css.push('<?=FOLDER?>css/common.css');
						content_css.push('<?=FOLDER?>css/template.css');					
					}else{
						var content_css = ['<?=FOLDER?>css/tiny.css',
							'<?=FOLDER?>css/common.css',
							'<?=FOLDER?>css/template.css'];
					}
					
					tinymce.init({
						selector: 'textarea.tinymce-area',
						height: 450,				
						relative_urls: false,
						convert_urls: false,
						remove_script_host : false,
						theme : "modern",
						theme_advanced_font_sizes : "10pt,11pt,12px,14px,16px,24px",
						//plugins: ["advlist anchor autolink autoresize autosave bbcode charmap code codesample colorpicker contextmenu directionality emoticons fullpage fullscreen help hr image imagetools importcss insertdatetime legacyoutput link lists media nonbreaking noneditable pagebreak paste preview print save searchreplace spellchecker tabfocus table template textcolor textpattern toc visualblocks visualchars wordcount"],
						//plugins: ["image textcolor table code colorpicker"],
						//toolbar: 'formatselect | bold italic strikethrough forecolor backcolor fontselect fontsizeselect | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent',
						plugins: [
							'advlist autolink lists link image charmap print preview anchor textcolor',
							'searchreplace visualblocks code fullscreen',
							'insertdatetime media table paste code help wordcount'
						  ],
						  toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link',
						  
						font_formats: 'Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;AkrutiKndPadmini=Akpdmi-n',
						fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 26pt 30pt 36pt 48pt',
						//menu: {
							//edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
							//insert: {title: 'Insert', items: 'link media | template hr'},
							//view: {title: 'View', items: 'visualaid'},
							//format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
							//table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
							//tools: {title: 'Tools', items: 'spellchecker code'}
						//},
						//image_advtab: true,
						content_css: content_css,
						//extended_valid_elements: 'span,i,script[language|type|src],a[onclick|class|id],iframe[width|height|src|style|frameborder|allow|allowfullscreen]',
						extended_valid_elements: '*[*]',
						allow_unsafe_link_target: true,
						allow_conditional_comments: true
					});
					
				}
				if( $('.colorpicker').length ){
					$('.colorpicker').each(function(i,o){
						$(o).spectrum({
							color: $(o).val(),
							preferredFormat: "hex",
							showInput: true,
							change: function(color) {
								$(o).val(color.toHexString());
							}
						});
					});
				}
				
			});
			
		</script>
		<script type="text/javascript" src="<?=FOLDER?>js/jsPlumb.2.8.4.min.js"></script>
		<script type="text/javascript" src="<?=FOLDER?>js/default-katavorio-helper.js"></script>
		<script type="text/javascript" src="<?=FOLDER?>js/jquery.ui.touch-punch.js"></script>
	</body>
</html>