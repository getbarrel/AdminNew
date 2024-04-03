/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ){
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config
    config.toolbar = [
        { name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview' ] },
        { name: 'undo', items: [ 'Undo','Redo' ] },
        { name: 'links', items: [ 'Link', 'Unlink' ] },
        { name: 'insert', items: [ 'Image', 'Table' ] },
        { name: 'paragraph', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline' ] },
        { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
        { name: 'styles', items: [ 'Font', 'FontSize' ] }
    ];

	// The default plugins included in the basic setup define some buttons that
	// we don't want too have in a basic editor. We remove them here.
	//config.filebrowserImageUploadUrl = '/ckfinder/core/connector/java/connector.java?command=QuickUpload&type=Images';
	//config.filebrowserFlashUploadUrl = '/ckfinder/core/connector/java/connector.java?command=QuickUpload&type=Flash';
    config.removeButtons = 'Smiley,Special,Flash,Anchor,Print,Save,Subscript,Superscript,HorizontalRule,SpecialChar,PageBreak,Iframe';
	config.filebrowserImageUploadUrl = "/admin/ckeditor/upload.php";
    config.removeDialogTabs = 'image:advanced;link:advanced';
	config.allowedContent = true;
    config.extraPlugins = 'pastebase64';
	//config.fullPage = true;

};