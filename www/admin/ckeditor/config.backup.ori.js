/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ){
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config
    config.toolbarGroups = [
        { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'colors' },
        { name: 'paragraph',   groups: [ 'indent',  'align' ] },
        { name: 'insert' },
        { name: 'styles' }
        //{ name: 'tools' },//{ name: 'others' },//{ name: 'about' },//{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },//{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },//{ name: 'forms' },//{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },//{ name: 'links' }
    ];

    // The default plugins included in the basic setup define some buttons that
    // we don't want too have in a basic editor. We remove them here.
    //config.filebrowserImageUploadUrl = '/ckfinder/core/connector/java/connector.java?command=QuickUpload&type=Images';
    //config.filebrowserFlashUploadUrl = '/ckfinder/core/connector/java/connector.java?command=QuickUpload&type=Flash';

    config.filebrowserImageUploadUrl = "/admin/ckeditor/upload.php";
    config.removeDialogTabs = 'link:advanced';
    config.allowedContent = true;
    config.extraPlugins = 'pastebase64';
    //config.fullPage = true;

};