<?php

class upload {

    var $file_src_name;

    var $file_src_name_body;

    var $file_src_name_ext;

    var $file_src_mime;

    var $file_src_size;

    var $file_src_error;

    var $file_src_pathname;

    var $file_src_temp;

    var $file_dst_path;

    var $file_dst_name;

    var $file_dst_name_body;

    var $file_dst_name_ext;

    var $file_dst_pathname;

    var $image_src_x;

    var $image_src_y;

    var $image_dst_x;

    var $image_dst_y;

    var $uploaded;

    var $no_upload_check;

    var $processed;

    var $error;

    var $log;

    var $file_new_name_body;

    var $file_name_body_add;

    var $file_new_name_ext;

    var $file_safe_name;

    var $mime_check;

    var $mime_magic_check;

    var $no_script;

    var $file_auto_rename;

    var $dir_auto_create;

    var $dir_auto_chmod;

    var $dir_chmod;

    var $file_overwrite;

    var $file_max_size;

    var $image_resize;

    var $image_convert;

    var $image_x;

    var $image_y;

    var $image_ratio;

    var $image_ratio_crop;

    var $image_ratio_no_zoom_in;

    var $image_ratio_no_zoom_out;

    var $image_ratio_x;

    var $image_ratio_y;

    var $jpeg_quality;

    var $jpeg_size;

    var $preserve_transparency;
    
    var $image_brightness;

    var $image_contrast;
    
    var $image_threshold;

    var $image_tint_color;

    var $image_overlay_color;

    var $image_overlay_percent;

    var $image_negative;
    
    var $image_greyscale;

    var $image_text;

    var $image_text_direction;

    var $image_text_color;

    /**
     * Sets the text visibility in the text label
     *
     * Value is a percentage, as an integer between 0 and 100
     *
     * Default value is 100
     *
     * @access public
     * @var integer;
     */
    var $image_text_percent;

    /**
     * Sets the text background color for the text label
     *
     * Value is an hexadecimal color, such as #FFFFFF
     *
     * Default value is NULL (no background)
     *
     * @access public
     * @var string;
     */
    var $image_text_background;

    /**
     * Sets the text background visibility in the text label
     *
     * Value is a percentage, as an integer between 0 and 100
     *
     * Default value is 100
     *
     * @access public
     * @var integer;
     */
    var $image_text_background_percent;

    /**
     * Sets the text font in the text label
     *
     * Value is a an integer between 1 and 5 for GD built-in fonts. 1 is the smallest font, 5 the biggest
     * Value can also be a string, which represents the path to a GDF font. The font will be loaded into GD, and used as a built-in font.
     *
     * Default value is 5
     *
     * @access public
     * @var mixed;
     */
    var $image_text_font;

    /**
     * Sets the text label position within the image
     *
     * Value is one or two out of 'TBLR' (top, bottom, left, right)
     *
     * The positions are as following:   
     * <pre>
     *                        TL  T  TR
     *                        L       R
     *                        BL  B  BR
     * </pre>
     *
     * Default value is NULL (centered, horizontal and vertical)
     *
     * Note that is {@link image_text_x} and {@link image_text_y} are used, this setting has no effect
     *
     * @access public
     * @var string;
     */
    var $image_text_position;

    /**
     * Sets the text label absolute X position within the image
     *
     * Value is in pixels, representing the distance between the left of the image and the label
     * If a negative value is used, it will represent the distance between the right of the image and the label    
     *     
     * Default value is NULL (so {@link image_text_position} is used)
     *
     * @access public
     * @var integer;
     */
    var $image_text_x;

    /**
     * Sets the text label absolute Y position within the image
     *
     * Value is in pixels, representing the distance between the top of the image and the label
     * If a negative value is used, it will represent the distance between the bottom of the image and the label    
     *     
     * Default value is NULL (so {@link image_text_position} is used)
     *
     * @access public
     * @var integer;
     */
    var $image_text_y;

    /**
     * Sets the text label padding
     *
     * Value is in pixels, representing the distance between the text and the label background border
     *     
     * Default value is 0
     *
     * This setting can be overriden by {@link image_text_padding_x} and {@link image_text_padding_y}
     *
     * @access public
     * @var integer;
     */
    var $image_text_padding;

    /**
     * Sets the text label horizontal padding
     *
     * Value is in pixels, representing the distance between the text and the left and right label background borders
     *     
     * Default value is NULL
     *
     * If set, this setting overrides the horizontal part of {@link image_text_padding}
     *
     * @access public
     * @var integer;
     */
    var $image_text_padding_x;

    /**
     * Sets the text label vertical padding
     *
     * Value is in pixels, representing the distance between the text and the top and bottom label background borders
     *     
     * Default value is NULL
     *
     * If set, his setting overrides the vertical part of {@link image_text_padding}
     *
     * @access public
     * @var integer;
     */
    var $image_text_padding_y;

    /**
     * Sets the text alignment
     *
     * Value is a string, which can be either 'L', 'C' or 'R'
     *     
     * Default value is 'C'
     *
     * This setting is relevant only if the text has several lines.
     *
     * @access public
     * @var string;
     */
    var $image_text_alignment;

    /**
     * Sets the text line spacing
     *
     * Value is an integer, in pixels
     *     
     * Default value is 0
     *
     * This setting is relevant only if the text has several lines.
     *
     * @access public
     * @var integer;
     */
    var $image_text_line_spacing;

    /**
     * Sets the height of the reflection
     *
     * Value is an integer in pixels, or a string which format can be in pixels or percentage.
     * For instance, values can be : 40, '40', '40px' or '40%'
     *     
     * Default value is NULL, no reflection
     *
     * @access public
     * @var mixed;
     */
    var $image_reflection_height;

    /**
     * Sets the space between the source image and its relection
     *
     * Value is an integer in pixels, which can be negative
     *     
     * Default value is 2
     *
     * This setting is relevant only if {@link image_reflection_height} is set
     *
     * @access public
     * @var integer;
     */
    var $image_reflection_space;

    /**
     * Sets the color of the reflection background.
     *
     * Value is an hexadecimal color, such as #FFFFFF
     *     
     * Default value is #FFFFFF
     *
     * This setting is relevant only if {@link image_reflection_height} is set
     *
     * @access public
     * @var string;
     */
    var $image_reflection_color;

    /**
     * Sets the initial opacity of the reflection
     *
     * Value is an integer between 0 (no opacity) and 100 (full opacity).
     * The reflection will start from {@link image_reflection_opacity} and end up at 0
     *     
     * Default value is 60
     *
     * This setting is relevant only if {@link image_reflection_height} is set
     *
     * @access public
     * @var integer;
     */
    var $image_reflection_opacity;

    /**
     * Flips the image vertically or horizontally
     *
     * Value is either 'h' or 'v', as in horizontal and vertical
     *
     * Default value is NULL (no flip)
     *
     * @access public
     * @var string;
     */
    var $image_flip;

    /**
     * Rotates the image by increments of 45 degrees
     *
     * Value is either 90, 180 or 270
     *
     * Default value is NULL (no rotation)
     *
     * @access public
     * @var string;
     */
    var $image_rotate;

    /**
     * Crops an image
     *
     * Values are four dimensions, or two, or one (CSS style)
     * They represent the amount cropped top, right, bottom and left.
     * These values can either be in an array, or a space separated string.
     * Each value can be in pixels (with or without 'px'), or percentage (of the source image)
     *
     * For instance, are valid:
     * <pre>
     * $foo->image_crop = 20                  OR array(20);
     * $foo->image_crop = '20px'              OR array('20px');
     * $foo->image_crop = '20 40'             OR array('20', 40);
     * $foo->image_crop = '-20 25%'           OR array(-20, '25%');
     * $foo->image_crop = '20px 25%'          OR array('20px', '25%');
     * $foo->image_crop = '20% 25%'           OR array('20%', '25%');
     * $foo->image_crop = '20% 25% 10% 30%'   OR array('20%', '25%', '10%', '30%');
     * $foo->image_crop = '20px 25px 2px 2px' OR array('20px', '25%px', '2px', '2px');
     * $foo->image_crop = '20 25% 40px 10%'   OR array(20, '25%', '40px', '10%');
     * </pre>
     *
     * If a value is negative, the image will be expanded, and the extra parts will be filled with black
     *
     * Default value is NULL (no cropping)
     *
     * @access public
     * @var string OR array;
     */
    var $image_crop;

    /**
     * Adds a bevel border on the image
     *
     * Value is a positive integer, representing the thickness of the bevel
     *
     * If the bevel colors are the same as the background, it makes a fade out effect
     *
     * Default value is NULL (no bevel)
     *
     * @access public
     * @var integer;
     */
    var $image_bevel;

    /**
     * Top and left bevel color
     *
     * Value is a color, in hexadecimal format
     * This setting is used only if {@link image_bevel} is set
     *
     * Default value is #FFFFFF
     *
     * @access public
     * @var string;
     */
    var $image_bevel_color1;

    /**
     * Right and bottom bevel color
     *
     * Value is a color, in hexadecimal format
     * This setting is used only if {@link image_bevel} is set
     *
     * Default value is #000000
     *
     * @access public
     * @var string;
     */
    var $image_bevel_color2;


    /**
     * Adds a single-color border on the outer of the image
     *
     * Values are four dimensions, or two, or one (CSS style)
     * They represent the border thickness top, right, bottom and left.
     * These values can either be in an array, or a space separated string.
     * Each value can be in pixels (with or without 'px'), or percentage (of the source image)
     *
     * See {@link image_crop} for valid formats
     *
     * If a value is negative, the image will be cropped. 
     * Note that the dimensions of the picture will be increased by the borders' thickness
     *
     * Default value is NULL (no border)
     *
     * @access public
     * @var integer;
     */
    var $image_border;

    /**
     * Border color
     *
     * Value is a color, in hexadecimal format. 
     * This setting is used only if {@link image_border} is set
     *
     * Default value is #FFFFFF
     *
     * @access public
     * @var string;
     */
    var $image_border_color;

    /**
     * Adds a multi-color frame on the outer of the image
     *
     * Value is an integer. Two values are possible for now:
     * 1 for flat border, meaning that the frame is mirrored horizontally and vertically
     * 2 for crossed border, meaning that the frame will be inversed, as in a bevel effect
     *
     * The frame will be composed of colored lines set in {@link image_frame_colors}
     *
     * Note that the dimensions of the picture will be increased by the borders' thickness
     *
     * Default value is NULL (no frame)
     *
     * @access public
     * @var integer;
     */
    var $image_frame;

    /**
     * Sets the colors used to draw a frame
     *
     * Values is a list of n colors in hexadecimal format.
     * These values can either be in an array, or a space separated string.
     *
     * The colors are listed in the following order: from the outset of the image to its center
     * 
     * For instance, are valid:
     * <pre>
     * $foo->image_frame_colors = '#FFFFFF #999999 #666666 #000000';
     * $foo->image_frame_colors = array('#FFFFFF', '#999999', '#666666', '#000000');
     * </pre>
     *
     * This setting is used only if {@link image_frame} is set
     *
     * Default value is '#FFFFFF #999999 #666666 #000000'
     *
     * @access public
     * @var string OR array;
     */
    var $image_frame_colors;

    /**
     * Adds a watermark on the image
     *
     * Value is a local image filename, relative or absolute. GIF, JPG and PNG are supported, as well as PNG alpha.
     *
     * If set, this setting allow the use of all other settings starting with image_watermark_
     *
     * Default value is NULL
     *
     * @access public
     * @var string;
     */
    var $image_watermark;

    /**
     * Sets the watermarkposition within the image
     *
     * Value is one or two out of 'TBLR' (top, bottom, left, right)
     *
     * The positions are as following:   TL  T  TR
     *                                   L       R
     *                                   BL  B  BR
     *
     * Default value is NULL (centered, horizontal and vertical)
     *
     * Note that is {@link image_watermark_x} and {@link image_watermark_y} are used, this setting has no effect
     *
     * @access public
     * @var string;
     */
    var $image_watermark_position;

    /**
     * Sets the watermark absolute X position within the image
     *
     * Value is in pixels, representing the distance between the top of the image and the watermark
     * If a negative value is used, it will represent the distance between the bottom of the image and the watermark    
     *     
     * Default value is NULL (so {@link image_watermark_position} is used)
     *
     * @access public
     * @var integer;
     */
    var $image_watermark_x;

    /**
     * Sets the twatermark absolute Y position within the image
     *
     * Value is in pixels, representing the distance between the left of the image and the watermark
     * If a negative value is used, it will represent the distance between the right of the image and the watermark    
     *     
     * Default value is NULL (so {@link image_watermark_position} is used)
     *
     * @access public
     * @var integer;
     */
    var $image_watermark_y;

    /**
     * Allowed MIME types
     *
     * Default is a selection of safe mime-types, but you might want to change it
     *
     * @access public
     * @var integer
     */
    var $allowed;
    

    /**
     * Init or re-init all the processing variables to their default values
     *
     * This function is called in the constructor, and after each call of {@link process}
     *
     * @access private
     */
    function init() {

        // overiddable variables
        $this->file_new_name_body       = '';       // replace the name body
        $this->file_name_body_add       = '';       // append to the name body
        $this->file_new_name_ext        = '';       // replace the file extension
        $this->file_safe_name           = true;     // format safely the filename
        $this->file_overwrite           = false;    // allows overwritting if the file already exists
        $this->file_auto_rename         = true;     // auto-rename if the file already exists
        $this->dir_auto_create          = true;     // auto-creates directory if missing
        $this->dir_auto_chmod           = true;     // auto-chmod directory if not writeable
        $this->dir_chmod                = 0777;     // default chmod to use
        
        $this->mime_check               = true;     // don't check the mime type against the allowed list
        $this->mime_magic_check         = false;    // don't double check the MIME type with mime_magic
        $this->no_script                = true;     // turns scripts into test files 

        $val = trim(ini_get('upload_max_filesize'));
        $last = strtolower($val{strlen($val)-1});
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        $this->file_max_size = $val;   
        
        $this->image_resize             = false;    // resize the image
        $this->image_convert            = '';       // convert. values :''; 'png'; 'jpeg'; 'gif'

        $this->image_x                  = 150;
        $this->image_y                  = 150;
        $this->image_ratio              = false;    // keeps aspect ration with x and y dimensions
        $this->image_ratio_crop         = false;    // keeps aspect ration with x and y dimensions, filling the space
        $this->image_ratio_no_zoom_in   = false;
        $this->image_ratio_no_zoom_out  = false;
        $this->image_ratio_x            = false;    // calculate the $image_x if true
        $this->image_ratio_y            = false;    // calculate the $image_y if true
        $this->jpeg_quality             = 100;
        $this->jpeg_size                = NULL;
        $this->preserve_transparency    = false;
        
        $this->image_brightness         = NULL; 
        $this->image_contrast           = NULL;
        $this->image_threshold          = NULL;
        $this->image_tint_color         = NULL;
        $this->image_overlay_color      = NULL;
        $this->image_overlay_percent    = NULL;
        $this->image_negative           = false;
        $this->image_greyscale          = false;

        $this->image_text               = NULL;
        $this->image_text_direction     = NULL;
        $this->image_text_color         = '#FFFFFF';
        $this->image_text_percent       = 100;
        $this->image_text_background    = NULL;
        $this->image_text_background_percent = 100; 
        $this->image_text_font          = 5;
        $this->image_text_x             = NULL;
        $this->image_text_y             = NULL;
        $this->image_text_position      = NULL; 
        $this->image_text_padding       = 0;
        $this->image_text_padding_x     = NULL;
        $this->image_text_padding_y     = NULL;
        $this->image_text_alignment     = 'C';  
        $this->image_text_line_spacing  = 0;

        $this->image_reflection_height  = NULL;
        $this->image_reflection_space   = 2;
        $this->image_reflection_color   = '#ffffff';
        $this->image_reflection_opacity = 60;
       
        $this->image_watermark          = NULL;
        $this->image_watermark_x        = NULL;
        $this->image_watermark_y        = NULL;
        $this->image_watermark_position = NULL; 

        $this->image_flip               = NULL; 
        $this->image_rotate             = NULL;   
        $this->image_crop               = NULL;

        $this->image_bevel              = NULL;
        $this->image_bevel_color1       = '#FFFFFF';
        $this->image_bevel_color2       = '#000000';      
        $this->image_border             = NULL;
        $this->image_border_color       = '#FFFFFF';
        $this->image_frame              = NULL;
        $this->image_frame_colors       = '#FFFFFF #999999 #666666 #000000';

        $this->allowed = array("application/arj",
                               "application/excel",
                               "application/gnutar",
                               "application/msword",
                               "application/mspowerpoint",
                               "application/octet-stream",
                               "application/pdf",
                               "application/powerpoint",
                               "application/postscript",
                               "application/plain",
                               "application/rtf",
                               "application/vnd.ms-excel",
                               "application/vocaltec-media-file",
                               "application/wordperfect",
                               "application/x-bzip",
                               "application/x-bzip2",
                               "application/x-compressed",
                               "application/x-excel",
                               "application/x-gzip",
                               "application/x-latex",
                               "application/x-midi",
                               "application/x-msexcel",
                               "application/x-rtf",
                               "application/x-sit",
                               "application/x-stuffit",
                               "application/x-shockwave-flash",
                               "application/x-troff-msvideo",
                               "application/x-zip-compressed",
                               "application/xml",
                               "application/zip",
                               "audio/aiff",
                               "audio/basic",
                               "audio/midi",
                               "audio/mod",
                               "audio/mpeg",
                               "audio/mpeg3",
                               "audio/wav",
                               "audio/x-aiff",
                               "audio/x-au",
                               "audio/x-mid",
                               "audio/x-midi",
                               "audio/x-mod",
                               "audio/x-mpeg-3",
                               "audio/x-wav",
                               "audio/xm",
                               "image/bmp",
                               "image/gif",
                               "image/jpeg",
                               "image/pjpeg",
                               "image/png",
                               "image/x-png",
                               "image/tiff",
                               "image/x-tiff",
                               "image/x-windows-bmp",
                               "multipart/x-zip",
                               "multipart/x-gzip",
                               "music/crescendo",
                               "text/richtext",
                               "text/plain",
                               "text/xml",
                               "video/avi",
                               "video/mpeg",
                               "video/msvideo",
                               "video/quicktime",
                               "video/quicktime",
                               "video/x-mpeg",
                               "video/x-ms-wmv",
                               "video/x-ms-asf",
                               "video/x-ms-asf-plugin",
                               "video/x-msvideo",
                               "x-music/x-midi");
    }

    /**
     * Constructor. Checks if the file has been uploaded
     *
     * The constructor takes $_FILES['form_field'] array as argument
     * where form_field is the form field name
     *
     * The constructor will check if the file has been uploaded in its temporary location, and
     * accordingly will set {@link uploaded} (and {@link error} is an error occurred)
     *
     * If the file has been uploaded, the constructor will populate all the variables holding the upload 
     * information (none of the processing class variables are used here).
     * You can have access to information about the file (name, size, MIME type...).
     *
     *
     * Alternatively, you can set the first argument to be a local filename (string)
     * and the second argument to be a MIME type (string) (second argument optional if mime_magic is installed)
     * This allows processing of a local file, as if the file was uploaded
     *
     * @access private
     * @param  array  $file $_FILES['form_field']
     *    or   string $file Local filename
     */
    function upload($file) {

        $this->file_src_name      = '';
        $this->file_src_name_body = '';
        $this->file_src_name_ext  = '';
        $this->file_src_mime      = '';
        $this->file_src_size      = '';
        $this->file_src_error     = '';
        $this->file_src_pathname  = '';
        $this->file_src_temp      = '';

        $this->file_dst_path      = '';
        $this->file_dst_name      = '';
        $this->file_dst_name_body = '';
        $this->file_dst_name_ext  = '';
        $this->file_dst_pathname  = '';

        $this->image_src_x        = 0;
        $this->image_src_y        = 0;
        $this->image_dst_type     = '';
        $this->image_dst_x        = 0;
        $this->image_dst_y        = 0;

        $this->uploaded           = true;
        $this->no_upload_check    = false;
        $this->processed          = true;
        $this->error              = '';
        $this->log                = '';        
        $this->allowed            = array();
        $this->init();

        if (!$file) {
            $this->uploaded = false;
            $this->error = _("File error. Please try again");
        }

        // check if we sent a local filename rather than a $_FILE element
        if (!is_array($file)) {
            if (empty($file)) {
                $this->uploaded = false;
                $this->error = _("File error. Please try again");
            } else {
                $this->no_upload_check = TRUE;
                // this is a local filename, i.e.not uploaded
                $this->log .= '<b>' . _("source is a local file") . ' ' . $file . '</b><br />';

                if ($this->uploaded && !file_exists($file)) {
                    $this->uploaded = false;
                    $this->error = _("Local file doesn't exist");
                }
        
                if ($this->uploaded && !is_readable($file)) {
                    $this->uploaded = false;
                    $this->error = _("Local file is not readable");
                }

                if ($this->uploaded) {
                    $this->file_src_pathname   = $file;
                    $this->file_src_name       = basename($file);
                    $this->log .= '- ' . _("local file name OK") . '<br />';
                    ereg('\.([^\.]*$)', $this->file_src_name, $extension);
                    if (is_array($extension)) {
                        $this->file_src_name_ext      = strtolower($extension[1]);
                        $this->file_src_name_body     = substr($this->file_src_name, 0, ((strlen($this->file_src_name) - strlen($this->file_src_name_ext)))-1);
                    } else {
                        $this->file_src_name_ext      = '';
                        $this->file_src_name_body     = $this->file_src_name;
                    }
                    $this->file_src_size = (file_exists($file) ? filesize($file) : 0);
                    // we try to retrieve the MIME type
                    $info = getimagesize($this->file_src_pathname);
                    $this->file_src_mime = (array_key_exists('mime', $info) ? $info['mime'] : NULL); 
                    // if we don't have a MIME type, we attempt to retrieve it the old way
                    if (empty($this->file_src_mime)) {
                        $mime = (array_key_exists(2, $info) ? $info[2] : NULL); // 1 = GIF, 2 = JPG, 3 = PNG
                        $this->file_src_mime = ($mime==1 ? 'image/gif' : ($mime==2 ? 'image/jpeg' : ($mime==3 ? 'image/png' : NULL)));
                    }
                    // if we still don't have a MIME type, we attempt to retrieve it otherwise
                    if (empty($this->file_src_mime) && function_exists('mime_content_type')) {
                        $this->file_src_mime = mime_content_type($this->file_src_pathname);
                    }                     
                    $this->file_src_error = 0; 
                }                
                
            }
        } else {
            // this is an element from $_FILE, i.e. an uploaded file
            $this->log .= '<b>' . _("source is an uploaded file") . '</b><br />';
            if ($this->uploaded) {
                $this->file_src_error         = $file['error'];
                switch($this->file_src_error) {
                    case 0:
                        // all is OK
                        $this->log .= '- ' . _("upload OK") . '<br />';
                        break;
                    case 1:
                        $this->uploaded = false;
                        $this->error = _("File upload error (the uploaded file exceeds the upload_max_filesize directive in php.ini)");
                        break;
                    case 2:
                        $this->uploaded = false;
                        $this->error = _("File upload error (the uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form)");
                        break;
                    case 3:
                        $this->uploaded = false;
                        $this->error = _("File upload error (the uploaded file was only partially uploaded)");
                        break;
                    case 4:
                        $this->uploaded = false;
                        $this->error = _("File upload error (no file was uploaded)");
                        break;
                    default:
                        $this->uploaded = false;
                        $this->error = _("File upload error (unknown error code)");
                }
            }
    
            if ($this->uploaded) {
                $this->file_src_pathname   = $file['tmp_name'];
                $this->file_src_name       = $file['name'];
                if ($this->file_src_name == '') {
                    $this->uploaded = false;
                    $this->error = _("File upload error. Please try again");
                }
            }

            if ($this->uploaded) {
                $this->log .= '- ' . _("file name OK") . '<br />';
                ereg('\.([^\.]*$)', $this->file_src_name, $extension);
                if (is_array($extension)) {
                    $this->file_src_name_ext      = strtolower($extension[1]);
                    $this->file_src_name_body     = substr($this->file_src_name, 0, ((strlen($this->file_src_name) - strlen($this->file_src_name_ext)))-1);
                } else {
                    $this->file_src_name_ext      = '';
                    $this->file_src_name_body     = $this->file_src_name;
                }
                $this->file_src_size = $file['size'];
                $this->file_src_mime = $file['type'];
            }
        }

        $this->log .= '- ' . _("source variables") . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_name         : ' . $this->file_src_name . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_name_body    : ' . $this->file_src_name_body . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_name_ext     : ' . $this->file_src_name_ext . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_pathname     : ' . $this->file_src_pathname . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_mime         : ' . $this->file_src_mime . '<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_size         : ' . $this->file_src_size . ' (max= ' . $this->file_max_size . ')<br />';
        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_src_error        : ' . $this->file_src_error . '<br />';
    }


    /**
     * Returns the version of GD
     *
     * @access public
     * @return float GD version
     */
    function gd_version() {
        static $gd_version = null;
        if ($gd_version === null) {
            if (function_exists('gd_info')) {
                $gd = gd_info();
                $gd = $gd["GD Version"];
                $regex = "/([\d\.]+)/i";
            } else {
                ob_start();
                phpinfo(8);
                $gd = ob_get_contents();
                ob_end_clean();
                $regex = "/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i";
            }
            if (preg_match($regex, $gd, $m)) {
                $gd_version = (float) $m[1];
            } else {
                $gd_version = 0;
            }
        }
        return $gd_version;
    } 


    /**
     * Creates directories recursively
     *
     * @access private
     * @param  string  $path Path to create
     * @param  integer $mode Optional permissions
     * @return boolean Success
     */
    function r_mkdir($path, $mode = 0777) {
        return is_dir($path) || ( $this->r_mkdir(dirname($path), $mode) && @mkdir($path, $mode) );
    }


    /**
     * Actually uploads the file, and act on it according to the set processing class variables
     *
     * This function copies the uploaded file to the given location, eventually performing actions on it.
     * Typically, you can call {@link process} several times for the same file,
     * for instance to create a resized image and a thumbnail of the same file.
     * The original uploaded file remains intact in its temporary location, so you can use {@link process} several times.
     * You will be able to delete the uploaded file with {@link clean} when you have finished all your {@link process} calls.
     *
     * According to the processing class variables set in the calling file, the file can be renamed,
     * and if it is an image, can be resized or converted.
     *
     * When the processing is completed, and the file copied to its new location, the
     * processing class variables will be reset to their default value.
     * This allows you to set new properties, and perform another {@link process} on the same uploaded file
     *
     * It will set {@link processed} (and {@link error} is an error occurred)
     *
     * @access public
     * @param  string $server_path Path location of the uploaded file, with an ending slash
     */
    function process($server_path) {

        $this->error        = '';
        $this->processed    = true;

        if(strtolower(substr(PHP_OS, 0, 3)) === 'win') {
            if (substr($server_path, -1, 1) != '\\') $server_path = $server_path . '\\';
        } else {
            if (substr($server_path, -1, 1) != '/') $server_path = $server_path . '/';
        }
        $this->log .= '<b>' . _("process file to") . ' '  . $server_path . '</b><br />';

        // checks file size and mine type
        if ($this->uploaded) {

            if ($this->file_src_size > $this->file_max_size ) {
                $this->processed = false;
                $this->error = _("File too big");
            } else {
                $this->log .= '- ' . _("file size OK") . '<br />';
            }

            // turn dangerous scripts into text files
            if ($this->no_script) {
                if (((substr($this->file_src_mime, 0, 5) == 'text/' || strpos($this->file_src_mime, 'javascript') !== false)  && (substr($this->file_src_name, -4) != '.txt')) 
                    || preg_match('/\.(php|pl|py|cgi|asp)$/i', $this->file_src_name) || empty($this->file_src_name_ext)) {
                    $this->file_src_mime = 'text/plain';
                    $this->log .= '- ' . _("script") . ' '  . $this->file_src_name . ' ' . _("renamed as") . ' ' . $this->file_src_name . '.txt!<br />';
                    $this->file_src_name_ext .= (empty($this->file_src_name_ext) ? 'txt' : '.txt');
                } 
            }

            // checks MIME type with mime_magic
            if ($this->mime_magic_check && function_exists('mime_content_type')) {
                $detected_mime = mime_content_type($this->file_src_pathname);
                if ($this->file_src_mime != $detected_mime) {
                    $this->log .= '- ' . _("MIME type detected as") . ' ' . $detected_mime . ' ' . _("but given as") . ' ' . $this->file_src_mime . '!<br />';
                    $this->file_src_mime = $detected_mime;
                }
            } 

            if ($this->mime_check && empty($this->file_src_mime)) {
                $this->processed = false;
                $this->error = _("MIME type can't be detected!");
            } else if ($this->mime_check && !empty($this->file_src_mime) && !array_key_exists($this->file_src_mime, array_flip($this->allowed))) {
                $this->processed = false;
                $this->error = _("Incorrect type of file");
            } else {
                $this->log .= '- ' . _("file mime OK") . ' : ' . $this->file_src_mime . '<br />';
            }
        } else {
            $this->error = _("File not uploaded. Can't carry on a process");
            $this->processed = false;
        }

        if ($this->processed) {
            $this->file_dst_path        = $server_path;

            // repopulate dst variables from src
            $this->file_dst_name        = $this->file_src_name;
            $this->file_dst_name_body   = $this->file_src_name_body;
            $this->file_dst_name_ext    = $this->file_src_name_ext;


            if ($this->file_new_name_body != '') { // rename file body
                $this->file_dst_name_body = $this->file_new_name_body;
                $this->log .= '- ' . _("new file name body") . ' : ' . $this->file_new_name_body . '<br />';
            }
            if ($this->file_new_name_ext != '') { // rename file ext
                $this->file_dst_name_ext  = $this->file_new_name_ext;
                $this->log .= '- ' . _("new file name ext") . ' : ' . $this->file_new_name_ext . '<br />';
            }
               if ($this->file_name_body_add != '') { // append a bit to the name
                $this->file_dst_name_body  = $this->file_dst_name_body . $this->file_name_body_add;
                $this->log .= '- ' . _("file name body add") . ' : ' . $this->file_name_body_add . '<br />';
            }
            if ($this->file_safe_name) { // formats the name
                $this->file_dst_name_body = str_replace(array(' ', '-'), array('_','_'), $this->file_dst_name_body) ;
                $this->file_dst_name_body = ereg_replace('[^A-Za-z0-9_]', '', $this->file_dst_name_body) ;
                $this->log .= '- ' . _("file name safe format") . '<br />';
            }

            $this->log .= '- ' . _("destination variables") . '<br />';
            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_path         : ' . $this->file_dst_path . '<br />';
            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_name_body    : ' . $this->file_dst_name_body . '<br />';
            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_name_ext     : ' . $this->file_dst_name_ext . '<br />';

            // do we do some image manipulation?
            $image_manipulation  = ($this->image_resize 
                                 || $this->image_convert != '' 
                                 || is_numeric($this->image_brightness) 
                                 || is_numeric($this->image_contrast) 
                                 || is_numeric($this->image_threshold) 
                                 || !empty($this->image_tint_color) 
                                 || !empty($this->image_overlay_color) 
                                 || !empty($this->image_text)
                                 || $this->image_greyscale
                                 || $this->image_negative
                                 || !empty($this->image_watermark)
                                 || is_numeric($this->image_rotate)
                                 || is_numeric($this->jpeg_size)
                                 || !empty($this->image_flip)
                                 || !empty($this->image_crop)
                                 || !empty($this->image_border)
                                 || $this->image_frame > 0
                                 || $this->image_bevel > 0
                                 || $this->image_reflection_height);

            if ($image_manipulation) {
                if ($this->image_convert=='') {
                    $this->file_dst_name = $this->file_dst_name_body . (!empty($this->file_dst_name_ext) ? '.' . $this->file_dst_name_ext : '');
                    $this->log .= '- ' . _("image operation, keep extension") . '<br />';
                } else {
                    $this->file_dst_name = $this->file_dst_name_body . '.' . $this->image_convert;
                    $this->log .= '- ' . _("image operation, change extension for conversion type") . '<br />';
                }
            } else {
                $this->file_dst_name = $this->file_dst_name_body . (!empty($this->file_dst_name_ext) ? '.' . $this->file_dst_name_ext : '');
                $this->log .= '- ' . _("no image operation, keep extension") . '<br />';
            }
            
            if (!$this->file_auto_rename) {
                $this->log .= '- ' . _("no auto_rename if same filename exists") . '<br />';
                $this->file_dst_pathname = $this->file_dst_path . $this->file_dst_name;
            } else {
                $this->log .= '- ' . _("checking for auto_rename") . '<br />';
                $this->file_dst_pathname = $this->file_dst_path . $this->file_dst_name;
                $body     = $this->file_dst_name_body;
                $cpt = 1;
                while (@file_exists($this->file_dst_pathname)) {
                    $this->file_dst_name_body = $body . '_' . $cpt;
                    $this->file_dst_name = $this->file_dst_name_body . (!empty($this->file_dst_name_ext) ? '.' . $this->file_dst_name_ext : '');
                    $cpt++;
                    $this->file_dst_pathname = $this->file_dst_path . $this->file_dst_name;
                }               
                if ($cpt>1) $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("auto_rename to") . ' ' . $this->file_dst_name . '<br />';
            }

            $this->log .= '- ' . _("destination file details") . '<br />';
            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_name         : ' . $this->file_dst_name . '<br />';
            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;file_dst_pathname     : ' . $this->file_dst_pathname . '<br />';

            if ($this->file_overwrite) {
                 $this->log .= '- ' . _("no overwrite checking") . '<br />';
            } else {
                if (@file_exists($this->file_dst_pathname)) {
                    $this->processed = false;
                    $this->error = $this->file_dst_name . ' ' . _("already exists. Please change the file name");
                } else {
                    $this->log .= '- ' . $this->file_dst_name . ' '  . _("doesn't exist already") . '<br />';
                }
            }
        } else {
            $this->processed = false;
        }

        // if we have already moved the uploaded file, we use the temporary copy as source file, and check if it exists
        if (!empty($this->file_src_temp)) {
            $this->log .= '- ' . _("use the temp file instead of the original file since it is a second process") . '<br />';
            $this->file_src_pathname   = $this->file_src_temp;
            if (!file_exists($this->file_src_pathname)) {
                $this->processed = false;
                $this->error = _("No correct temp source file. Can't carry on a process");
            }
        // if we haven't a temp file, and that we do check on uploads, we use is_uploaded_file()
        } else if (!$this->no_upload_check) {
            if (!is_uploaded_file($this->file_src_pathname)) {
                $this->processed = false;
                $this->error = _("No correct uploaded source file. Can't carry on a process");
            }
        // otherwise, if we don;t check on uploaded files (local file for instance), we use file_exists()
        } else {
            if (!file_exists($this->file_src_pathname)) {
                $this->processed = false;
                $this->error = _("No correct source file. Can't carry on a process");
            }
        }
        
        // checks if the destination directory exists, and attempt to create it        
        if ($this->processed && !file_exists($this->file_dst_path)) {
            if ($this->dir_auto_create) {
                $this->log .= '- ' . $this->file_dst_path . ' '  . _("doesn't exist. Attempting creation:");
                if (!$this->r_mkdir($this->file_dst_path, $this->dir_chmod)) {
                    $this->log .= ' ' . _("failed") . '<br />';
                    $this->processed = false;
                    $this->error = _("Destination directory can't be created. Can't carry on a process");
                } else {
                    $this->log .= ' ' . _("success") . '<br />';
                }
            } else {
                $this->error = _("Destination directory doesn't exist. Can't carry on a process");
            }
        }

        if ($this->processed && !is_dir($this->file_dst_path)) {
            $this->processed = false;
            $this->error = _("Destination path is not a directory. Can't carry on a process");
        }

        // checks if the destination directory is writeable, and attempt to make it writeable  
        $hash = md5($this->file_dst_name_body . rand(1, 1000));
        if ($this->processed && !($f = @fopen($this->file_dst_path . $hash . '.' . $this->file_dst_name_ext, 'a+'))) {
            if ($this->dir_auto_chmod) {
                $this->log .= '- ' . $this->file_dst_path . ' '  . _("is not writeable. Attempting chmod:");
                if (!@chmod($this->file_dst_path, $this->dir_chmod)) {
                    $this->log .= ' ' . _("failed") . '<br />';
                    $this->processed = false;
                    $this->error = _("Destination directory can't be made writeable. Can't carry on a process");
                } else {
                    $this->log .= ' ' . _("success") . '<br />';
                    if (!($f = @fopen($this->file_dst_path . $hash . '.' . $this->file_dst_name_ext, 'a+'))) { // we re-check
                        $this->processed = false;
                        $this->error = _("Destination directory is still not writeable. Can't carry on a process");
                    } else {
                        @fclose($f);
                    }
                }                
            } else {
                $this->processed = false;
                $this->error = _("Destination path is not a writeable. Can't carry on a process");
            }
        } else {
            @fclose($f);
            @unlink($this->file_dst_path . $hash . '.' . $this->file_dst_name_ext);
        }


        // if we have an uploaded file, and if it is the first process, and if we can't access the file directly (open_basedir restriction)
        // then we create a temp file that will be used as the source file in subsequent processes
        // the third condition is there to check if the file is not accessible *directly* (it already has positively gone through is_uploaded_file(), so it exists)
        if (!$this->no_upload_check && empty($this->file_src_temp) && !file_exists($this->file_src_pathname)) {
            $this->log .= '- ' . _("attempting creating a temp file:");
            $hash = md5($this->file_dst_name_body . rand(1, 1000));
            if (move_uploaded_file($this->file_src_pathname, $this->file_dst_path . $hash . '.' . $this->file_dst_name_ext)) {
                $this->file_src_pathname = $this->file_dst_path . $hash . '.' . $this->file_dst_name_ext;
                $this->file_src_temp = $this->file_src_pathname;
                $this->log .= ' ' . _("file created") . '<br />';
                $this->log .= '    ' . _("temp file is:") . ' ' . $this->file_src_temp . '<br />';
            } else {
                $this->log .= ' ' . _("failed") . '<br />';
                $this->processed = false;
                $this->error = _("Can't create the temporary file. Can't carry on a process");
            }
        }

        if ($this->processed) {

            if ($image_manipulation) {

                // checks if the source file is readable     
                if ($this->processed && !($f = @fopen($this->file_src_pathname, 'r'))) {
                    $this->processed = false;
                    $this->error = _("Source file is not readable. Can't carry on a process");
                } else {
                    @fclose($f);
                }

                // we now do all the image manipulations
                $this->log .= '- ' . _("image resizing or conversion wanted") . '<br />';
                if ($this->gd_version()) {
                    switch($this->file_src_mime) {
                        case 'image/pjpeg':
                        case 'image/jpeg':
                        case 'image/jpg':
                            if (!function_exists('imagecreatefromjpeg')) {
                                $this->processed = false;
                                $this->error = _("No create from JPEG support");
                            } else {
                                $image_src = @imagecreatefromjpeg($this->file_src_pathname);
                                if (!$image_src) {
                                    $this->processed = false;
                                    $this->error = _("Error in creating JPEG image from source");
                                } else {
                                    $this->log .= '- ' . _("source image is JPEG") . '<br />';
                                }
                            }
                            break;
                        case 'image/x-png':	
                        case 'image/png':
                            if (!function_exists('imagecreatefrompng')) {
                                $this->processed = false;
                                $this->error = _("No create from PNG support");
                            } else {
                                $image_src = @imagecreatefrompng($this->file_src_pathname);
                                if (!$image_src) {
                                    $this->processed = false;
                                    $this->error = _("Error in creating PNG image from source");
                                } else {
                                    $this->log .= '- ' . _("source image is PNG") . '<br />';
                                }
                            }
                            break;
                        case 'image/gif':
                            if (!function_exists('imagecreatefromgif')) {
                                $this->processed = false;
                                $this->error = _("Error in creating GIF image from source");
                            } else {
                                $image_src = @imagecreatefromgif($this->file_src_pathname);
                                if (!$image_src) {
                                    $this->processed = false;
                                    $this->error = _("No GIF read support");
                                } else {
                                    $this->log .= '- ' . _("source image is GIF") . '<br />';
                                }
                            }
                            break;
                        default:
                            $this->processed = false;
                            $this->error = _("Can't read image source. not an image?");
                    }
                } else {
                    $this->processed = false;
                    $this->error = _("GD doesn't seem to be present");
                }

                if ($this->processed && $image_src) {

                    $this->image_src_x = imagesx($image_src);
                    $this->image_src_y = imagesy($image_src);
                    $this->image_dst_x = $this->image_src_x;
                    $this->image_dst_y = $this->image_src_y;
                    $gd_version = $this->gd_version();
                    $ratio_crop = null;

                    if ($this->image_resize) {
                        $this->log .= '- ' . _("resizing...") . '<br />';
 
                        if ($this->image_ratio_x) {
                            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("calculate x size") . '<br />';
                            $this->image_dst_x = round(($this->image_src_x * $this->image_y) / $this->image_src_y);
                            $this->image_dst_y = $this->image_y;
                        } else if ($this->image_ratio_y) {
                            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("calculate y size") . '<br />';
                            $this->image_dst_x = $this->image_x;
                            $this->image_dst_y = round(($this->image_src_y * $this->image_x) / $this->image_src_x);
                        } else if ($this->image_ratio || $this->image_ratio_crop || $this->image_ratio_no_zoom_in || $this->image_ratio_no_zoom_out) {
                            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("check x/y sizes") . '<br />';
                            if ((!$this->image_ratio_no_zoom_in && !$this->image_ratio_no_zoom_out)
                                 || ($this->image_ratio_no_zoom_in && ($this->image_src_x > $this->image_x || $this->image_src_y > $this->image_y))
                                 || ($this->image_ratio_no_zoom_out && $this->image_src_x < $this->image_x && $this->image_src_y < $this->image_y)) {
                                $this->image_dst_x = $this->image_x;
                                $this->image_dst_y = $this->image_y;
                                if ($this->image_ratio_crop) {
                                    if (!is_string($this->image_ratio_crop)) $this->image_ratio_crop = '';
                                    $this->image_ratio_crop = strtolower($this->image_ratio_crop);
                                    if (($this->image_src_x/$this->image_x) > ($this->image_src_y/$this->image_y)) {                                 
                                        $this->image_dst_y = $this->image_y;
                                        $this->image_dst_x = intval($this->image_src_x*($this->image_y / $this->image_src_y));
                                        $ratio_crop = array();
                                        $ratio_crop['x'] = $this->image_dst_x - $this->image_x;
                                        if (strpos($this->image_ratio_crop, 'l') !== false) {
                                            $ratio_crop['l'] = 0;
                                            $ratio_crop['r'] = $ratio_crop['x'];
                                        } else if (strpos($this->image_ratio_crop, 'r') !== false) {
                                            $ratio_crop['l'] = $ratio_crop['x'];
                                            $ratio_crop['r'] = 0;
                                        } else {
                                            $ratio_crop['l'] = round($ratio_crop['x']/2);
                                            $ratio_crop['r'] = $ratio_crop['x'] - $ratio_crop['l'];
                                        }
                                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("ratio_crop_x") . '         : ' . $ratio_crop['x'] . ' (' . $ratio_crop['l'] . ';' . $ratio_crop['r'] . ')<br />';
                                        if (is_null($this->image_crop)) $this->image_crop = array(0, 0, 0, 0);
                                    } else {                 
                                        $this->image_dst_x = $this->image_x;
                                        $this->image_dst_y = intval($this->image_src_y*($this->image_x / $this->image_src_x));
                                        $ratio_crop = array();
                                        $ratio_crop['y'] = $this->image_dst_y - $this->image_y;
                                        if (strpos($this->image_ratio_crop, 't') !== false) {
                                            $ratio_crop['t'] = 0;
                                            $ratio_crop['b'] = $ratio_crop['y'];
                                        } else if (strpos($this->image_ratio_crop, 'b') !== false) {
                                            $ratio_crop['t'] = $ratio_crop['y'];
                                            $ratio_crop['b'] = 0;
                                        } else {
                                            $ratio_crop['t'] = round($ratio_crop['y']/2);
                                            $ratio_crop['b'] = $ratio_crop['y'] - $ratio_crop['t'];
                                        }
                                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("ratio_crop_y") . '         : ' . $ratio_crop['y'] . ' (' . $ratio_crop['t'] . ';' . $ratio_crop['b'] . ')<br />';
                                        if (is_null($this->image_crop)) $this->image_crop = array(0, 0, 0, 0);
                                    }
                                } else {
                                    if (($this->image_src_x/$this->image_x) > ($this->image_src_y/$this->image_y)) {
                                        $this->image_dst_x = $this->image_x;
                                        $this->image_dst_y = intval($this->image_src_y*($this->image_x / $this->image_src_x));
                                    } else {
                                        $this->image_dst_y = $this->image_y;
                                        $this->image_dst_x = intval($this->image_src_x*($this->image_y / $this->image_src_y));
                                    }
                                }
                            } else {
                                $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("doesn't calculate x/y sizes") . '<br />';
                                $this->image_dst_x = $this->image_src_x;
                                $this->image_dst_y = $this->image_src_y;
                            }
                        } else {
                            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("use plain sizes") . '<br />';
                            $this->image_dst_x = $this->image_x;
                            $this->image_dst_y = $this->image_y;
                        }

                        if ($this->preserve_transparency && $this->file_src_mime != 'image/gif' && $this->file_src_mime != 'image/png') $this->preserve_transparency = false;        

                        if ($gd_version >= 2 && !$this->preserve_transparency) {
                            $image_dst = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        } else {
                            $image_dst = imagecreate($this->image_dst_x, $this->image_dst_y);
                        }
        
                        if ($this->preserve_transparency) {        
                            $this->log .= '- ' . _("preserve transparency") . '<br />';
                            $transparent_color = imagecolortransparent($image_src);
                            imagepalettecopy($image_dst, $image_src);
                            imagefill($image_dst, 0, 0, $transparent_color);
                            imagecolortransparent($image_dst, $transparent_color);
                        }
                        
                        if ($gd_version >= 2 && !$this->preserve_transparency) {
                           $res = imagecopyresampled($image_dst, $image_src, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y, $this->image_src_x, $this->image_src_y);
                        } else {
                            $res = imagecopyresized($image_dst, $image_src, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y, $this->image_src_x, $this->image_src_y);
                        }

                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("resized image object created") . '<br />';
                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;image_src_x y        : ' . $this->image_src_x . ' x ' . $this->image_src_y . '<br />';
                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;image_dst_x y        : ' . $this->image_dst_x . ' x ' . $this->image_dst_y . '<br />';

                    } else {
                        // we only convert, so we link the dst image to the src image
                        $image_dst = & $image_src;
                    }

                    // we have to set image_convert if it is not already
                    if (empty($this->image_convert)) {
                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("setting destination file type to") . ' ' . $this->file_src_name_ext . '<br />';
                        $this->image_convert = $this->file_src_name_ext;
                    }

                    // crop imag (and also crops if image_ratio_crop is used)
                    if ($gd_version >= 2 && (!empty($this->image_crop) || !is_null($ratio_crop))) {
                        if (is_array($this->image_crop)) {
                            $vars = $this->image_crop;
                        } else {
                            $vars = explode(' ', $this->image_crop);
                        }
                        if (sizeof($vars) == 4) {
                            $ct = $vars[0]; $cr = $vars[1]; $cb = $vars[2]; $cl = $vars[3];
                        } else if (sizeof($vars) == 2) {
                            $ct = $vars[0]; $cr = $vars[1]; $cb = $vars[0]; $cl = $vars[1];
                        } else {
                            $ct = $vars[0]; $cr = $vars[0]; $cb = $vars[0]; $cl = $vars[0];
                        } 
                        if (strpos($ct, '%')>0) $ct = $this->image_dst_y * (str_replace('%','',$ct) / 100);
                        if (strpos($cr, '%')>0) $cr = $this->image_dst_x * (str_replace('%','',$cr) / 100);
                        if (strpos($cb, '%')>0) $cb = $this->image_dst_y * (str_replace('%','',$cb) / 100);
                        if (strpos($cl, '%')>0) $cl = $this->image_dst_x * (str_replace('%','',$cl) / 100);
                        if (strpos($ct, 'px')>0) $ct = str_replace('px','',$ct);
                        if (strpos($cr, 'px')>0) $cr = str_replace('px','',$cr);
                        if (strpos($cb, 'px')>0) $cb = str_replace('px','',$cb);
                        if (strpos($cl, 'px')>0) $cl = str_replace('px','',$cl);
                        $ct = (int) $ct;
                        $cr = (int) $cr;
                        $cb = (int) $cb;
                        $cl = (int) $cl;
                        // we adjust the cropping if we use image_ratio_crop
                        if (!is_null($ratio_crop)) {
                            if (array_key_exists('t', $ratio_crop)) $ct += $ratio_crop['t'];
                            if (array_key_exists('r', $ratio_crop)) $cr += $ratio_crop['r'];
                            if (array_key_exists('b', $ratio_crop)) $cb += $ratio_crop['b'];
                            if (array_key_exists('l', $ratio_crop)) $cl += $ratio_crop['l'];
                        }
                        $this->log .= '- ' . _("crop image") . ' : ' . $ct . ' ' . $cr . ' ' . $cb . ' ' . $cl . ' <br />';
                        $this->image_dst_x = $this->image_dst_x - $cl - $cr;
                        $this->image_dst_y = $this->image_dst_y - $ct - $cb;
                        if ($this->image_dst_x < 1) $this->image_dst_x = 1;
                        if ($this->image_dst_y < 1) $this->image_dst_y = 1;

                        $tmp=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($tmp, $image_dst, 0, 0, $cl, $ct, $this->image_dst_x, $this->image_dst_y);

                        // we transfert tmp into image_dst
                        imagedestroy($image_dst);     
                        $image_dst=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($image_dst,$tmp,0,0,0,0,$this->image_dst_x,$this->image_dst_y);
                        imagedestroy($tmp);      
                    }
                    
                    
                    // flip image
                    if ($gd_version >= 2 && !empty($this->image_flip)) {
                        $this->image_flip = strtolower($this->image_flip);
                        $this->log .= '- ' . _("flip image") . ' : ' . $this->image_flip . '<br />';
                        $tmp=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        for ($x = 0; $x < $this->image_dst_x; $x++) {
                            for ($y = 0; $y < $this->image_dst_y; $y++){
                                if (strpos($this->image_flip, 'v') !== false) {
                                    imagecopy($tmp, $image_dst, $this->image_dst_x - $x - 1, $y, $x, $y, 1, 1);
                                } else {
                                    imagecopy($tmp, $image_dst, $x, $this->image_dst_y - $y - 1, $x, $y, 1, 1);
                                }
                            }
                        }

                        // we transfert tmp into image_dst
                        imagedestroy($image_dst);     
                        $image_dst=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($image_dst,$tmp,0,0,0,0,$this->image_dst_x,$this->image_dst_y);
                        imagedestroy($tmp);      
                    }


                    // rotate image
                    if ($gd_version >= 2 && is_numeric($this->image_rotate)) {
                        if (!in_array($this->image_rotate, array(0, 90, 180, 270))) $this->image_rotate = 0;  
                        if ($this->image_rotate != 0) {
                            if ($this->image_rotate == 90 || $this->image_rotate == 270) {
                                $tmp=imagecreatetruecolor($this->image_dst_y, $this->image_dst_x);
                            } else {
                                $tmp=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                            }
                            $this->log .= '- ' . _("rotate image") . ' : ' . $this->image_rotate . '<br />';
                            for ($x = 0; $x < $this->image_dst_x; $x++) {
                                for ($y = 0; $y < $this->image_dst_y; $y++){
                                    if ($this->image_rotate == 90) {
                                        imagecopy($tmp, $image_dst, $y, $x, $x, $this->image_dst_y - $y - 1, 1, 1);
                                    } else if ($this->image_rotate == 180) {
                                        imagecopy($tmp, $image_dst, $x, $y, $this->image_dst_x - $x - 1, $this->image_dst_y - $y - 1, 1, 1);
                                    } else if ($this->image_rotate == 270) {
                                        imagecopy($tmp, $image_dst, $y, $x, $this->image_dst_x - $x - 1, $y, 1, 1);
                                    } else {
                                        imagecopy($tmp, $image_dst, $x, $y, $x, $y, 1, 1);
                                    }
                                }
                            }
                            if ($this->image_rotate == 90 || $this->image_rotate == 270) {
                                $t = $this->image_dst_y;
                                $this->image_dst_y = $this->image_dst_x;
                                $this->image_dst_x = $t;
                            }
                            // we transfert tmp into image_dst
                            imagedestroy($image_dst);     
                            $image_dst=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                            imagecopy($image_dst,$tmp,0,0,0,0,$this->image_dst_x,$this->image_dst_y);
                            imagedestroy($tmp);      
                        }                        
                    }

                    // add color overlay
                   if ($gd_version >= 2 && (is_numeric($this->image_overlay_percent) && !empty($this->image_overlay_color))) {
                        $this->log .= '- ' . _("apply color overlay") . '<br />';
                        sscanf($this->image_overlay_color, "#%2x%2x%2x", $red, $green, $blue);
                        $filter=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        $color=imagecolorallocate($filter, $red, $green, $blue);
                        imagefilledrectangle($filter, 0, 0, $this->image_dst_x, $this->image_dst_y, $color);
                        imagecopymerge($image_dst, $filter, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y, $this->image_overlay_percent);
                        imagedestroy($filter);
                    }

                    // add brightness, contrast and tint, turns to greyscale and inverts colors
                    if ($gd_version >= 2 && ($this->image_negative || $this->image_greyscale || is_numeric($this->image_threshold)|| is_numeric($this->image_brightness) || is_numeric($this->image_contrast) || !empty($this->image_tint_color))) {
                        $this->log .= '- ' . _("apply tint, light, contrast correction, negative, greyscale and threshold") . '<br />';
                        if (!empty($this->image_tint_color)) sscanf($this->image_tint_color, "#%2x%2x%2x", $red, $green, $blue);
                        $background = imagecolorallocatealpha($image_dst, 255, 255, 255, 0);
                        imagefill($image_dst, 0, 0, $background);
                        imagealphablending($image_dst, TRUE);
                        for($y=0; $y < $this->image_dst_y; $y++) {
                            for($x=0; $x < $this->image_dst_x; $x++) {
                                if ($this->image_greyscale) {
                                    $rgb = imagecolorat($image_dst, $x, $y);           
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $r = $g = $b = round((0.2125 * $pixel['red']) + (0.7154 * $pixel['green']) + (0.0721 * $pixel['blue']));
                                    $a = $pixel['alpha'];           
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }      
                                if (is_numeric($this->image_threshold)) {
                                    $rgb = imagecolorat($image_dst, $x, $y);           
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $c = (round($pixel['red']+$pixel['green']+$pixel['blue'])/3) - 127;
                                    $r = $g = $b = ($c > $this->image_threshold ? 255 : 0);
                                    $a = $pixel['alpha'];           
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }
                                if (is_numeric($this->image_brightness)) {
                                    $rgb = imagecolorat($image_dst, $x, $y);           
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $r = max(min(round($pixel['red']+(($this->image_brightness*2))),255),0);
                                    $g = max(min(round($pixel['green']+(($this->image_brightness*2))),255),0);
                                    $b = max(min(round($pixel['blue']+(($this->image_brightness*2))),255),0);
                                    $a = $pixel['alpha'];           
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }
                                if (is_numeric($this->image_contrast)) {
                                    $rgb = imagecolorat($image_dst, $x, $y);           
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $r = max(min(round(($this->image_contrast+128)*$pixel['red']/128),255),0);
                                    $g = max(min(round(($this->image_contrast+128)*$pixel['green']/128),255),0);
                                    $b = max(min(round(($this->image_contrast+128)*$pixel['blue']/128),255),0);
                                    $a = $pixel['alpha'];           
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }
                                if (!empty($this->image_tint_color)) {
                                    $rgb = imagecolorat($image_dst, $x, $y);           
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $r = min(round($red*$pixel['red']/169),255);
                                    $g = min(round($green*$pixel['green']/169),255);
                                    $b = min(round($blue*$pixel['blue']/169),255);
                                    $a = $pixel['alpha'];           
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }                                
                                if (!empty($this->image_negative)) {
                                    $rgb = imagecolorat($image_dst, $x, $y);           
                                    $pixel = imagecolorsforindex($image_dst, $rgb);
                                    $r = round(255-$pixel['red']);
                                    $g = round(255-$pixel['green']);
                                    $b = round(255-$pixel['blue']);
                                    $a = $pixel['alpha'];           
                                    $pixelcolor = imagecolorallocatealpha($image_dst, $r, $g, $b, $a);
                                    imagesetpixel($image_dst, $x, $y, $pixelcolor);
                                }                                
                            }
                        }
                    }

                    // adds a border
                    if ($gd_version >= 2 && !empty($this->image_border)) {
                        if (is_array($this->image_border)) {
                            $vars = $this->image_border;
                            $this->log .= '- ' . _("add border") . ' : ' . implode(' ', $this->image_border) . '<br />';
                        } else {
                            $this->log .= '- ' . _("add border") . ' : ' . $this->image_border . '<br />';
                            $vars = explode(' ', $this->image_border);
                        }
                        if (sizeof($vars) == 4) {
                            $ct = $vars[0]; $cr = $vars[1]; $cb = $vars[2]; $cl = $vars[3];
                        } else if (sizeof($vars) == 2) {
                            $ct = $vars[0]; $cr = $vars[1]; $cb = $vars[0]; $cl = $vars[1];
                        } else {
                            $ct = $vars[0]; $cr = $vars[0]; $cb = $vars[0]; $cl = $vars[0];
                        } 
                        if (strpos($ct, '%')>0) $ct = $this->image_dst_y * (str_replace('%','',$ct) / 100);
                        if (strpos($cr, '%')>0) $cr = $this->image_dst_x * (str_replace('%','',$cr) / 100);
                        if (strpos($cb, '%')>0) $cb = $this->image_dst_y * (str_replace('%','',$cb) / 100);
                        if (strpos($cl, '%')>0) $cl = $this->image_dst_x * (str_replace('%','',$cl) / 100);
                        if (strpos($ct, 'px')>0) $ct = str_replace('px','',$ct);
                        if (strpos($cr, 'px')>0) $cr = str_replace('px','',$cr);
                        if (strpos($cb, 'px')>0) $cb = str_replace('px','',$cb);
                        if (strpos($cl, 'px')>0) $cl = str_replace('px','',$cl);
                        $ct = (int) $ct;
                        $cr = (int) $cr;
                        $cb = (int) $cb;
                        $cl = (int) $cl;
                        $this->image_dst_x = $this->image_dst_x + $cl + $cr;
                        $this->image_dst_y = $this->image_dst_y + $ct + $cb;
                        if (!empty($this->image_border_color)) sscanf($this->image_border_color, "#%2x%2x%2x", $red, $green, $blue);

                        $tmp=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        $background = imagecolorallocatealpha($tmp, $red, $green, $blue, 0);
                        imagefill($tmp, 0, 0, $background);
                        imagecopy($tmp, $image_dst, $cl, $ct, 0, 0, $this->image_dst_x - $cr - $cl, $this->image_dst_y - $cb - $ct);
                        
                        // we transfert tmp into image_dst
                        imagedestroy($image_dst);     
                        $image_dst=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($image_dst,$tmp,0,0,0,0,$this->image_dst_x,$this->image_dst_y);
                        imagedestroy($tmp);      
                    }
                    
                    // add frame border
                    if (is_numeric($this->image_frame)) {
                        if (is_array($this->image_frame_colors)) {
                            $vars = $this->image_frame_colors;
                            $this->log .= '- ' . _("add frame") . ' : ' . implode(' ', $this->image_frame_colors) . '<br />';
                        } else {
                            $this->log .= '- ' . _("add frame") . ' : ' . $this->image_frame_colors . '<br />';
                            $vars = explode(' ', $this->image_frame_colors);
                        }

                        $nb = sizeof($vars);
                        $this->image_dst_x = $this->image_dst_x + ($nb * 2);
                        $this->image_dst_y = $this->image_dst_y + ($nb * 2);
                        $tmp=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($tmp, $image_dst, $nb, $nb, 0, 0, $this->image_dst_x - ($nb * 2), $this->image_dst_y - ($nb * 2));
                        
                        for ($i=0; $i<$nb; $i++) {
                            sscanf($vars[$i], "#%2x%2x%2x", $red, $green, $blue);
                            $c = imagecolorallocate($tmp, $red, $green, $blue);
                            if ($this->image_frame == 1) {
                                imageline($tmp, $i, $i, $this->image_dst_x - $i -1, $i, $c);
                                imageline($tmp, $this->image_dst_x - $i -1, $this->image_dst_y - $i -1, $this->image_dst_x - $i -1, $i, $c);
                                imageline($tmp, $this->image_dst_x - $i -1, $this->image_dst_y - $i -1, $i, $this->image_dst_y - $i -1, $c);
                                imageline($tmp, $i, $i, $i, $this->image_dst_y - $i -1, $c);
                            } else {
                                imageline($tmp, $i, $i, $this->image_dst_x - $i -1, $i, $c);
                                imageline($tmp, $this->image_dst_x - $nb + $i, $this->image_dst_y - $nb + $i, $this->image_dst_x - $nb + $i, $nb - $i, $c);
                                imageline($tmp, $this->image_dst_x - $nb + $i, $this->image_dst_y - $nb + $i, $nb - $i, $this->image_dst_y - $nb + $i, $c);
                                imageline($tmp, $i, $i, $i, $this->image_dst_y - $i -1, $c);
                            }
                        }
                        
                        // we transfert tmp into image_dst
                        imagedestroy($image_dst);     
                        $image_dst=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($image_dst,$tmp,0,0,0,0,$this->image_dst_x,$this->image_dst_y);
                        imagedestroy($tmp);  
                    }

                    // add bevel border
                    if ($this->image_bevel > 0) {
                        if (empty($this->image_bevel_color1)) $this->image_bevel_color1 = '#FFFFFF'; 
                        if (empty($this->image_bevel_color2)) $this->image_bevel_color2 = '#000000'; 
                        sscanf($this->image_bevel_color1, "#%2x%2x%2x", $red1, $green1, $blue1);
                        sscanf($this->image_bevel_color2, "#%2x%2x%2x", $red2, $green2, $blue2);
                        imagealphablending($image_dst, true);
                        for ($i=0; $i<$this->image_bevel; $i++) {
                            $alpha = round(($i / $this->image_bevel) * 127);
                            $c1 = imagecolorallocatealpha($image_dst, $red1, $green1, $blue1, $alpha);
                            $c2 = imagecolorallocatealpha($image_dst, $red2, $green2, $blue2, $alpha);
                            imageline($image_dst, $i, $i, $this->image_dst_x - $i -1, $i, $c1);
                            imageline($image_dst, $this->image_dst_x - $i -1, $this->image_dst_y - $i, $this->image_dst_x - $i -1, $i, $c2);
                            imageline($image_dst, $this->image_dst_x - $i -1, $this->image_dst_y - $i -1, $i, $this->image_dst_y - $i -1, $c2);
                            imageline($image_dst, $i, $i, $i, $this->image_dst_y - $i -1, $c1);
                        }
                    }

                    // add watermark image
                    if ($this->image_watermark!='' && file_exists($this->image_watermark)) {
                        $this->log .= '- ' . _("add watermark") . '<br />';
                        $this->image_watermark_position = strtolower($this->image_watermark_position);
                        
                        $watermark_info = getimagesize($this->image_watermark);
                        $watermark_type = (array_key_exists(2, $watermark_info) ? $watermark_info[2] : NULL); // 1 = GIF, 2 = JPG, 3 = PNG
                        $watermark_checked = false;

                        if ($watermark_type == 1) {
                            if (!function_exists('imagecreatefromgif')) {
                                $this->error = _("No create from GIF support, can't read watermark");
                            } else {
                                $filter = @imagecreatefromgif($this->image_watermark);
                                if (!$filter) {
                                    $this->error = _("No GIF read support, can't create watermark");
                                } else {
                                    $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("watermark source image is GIF") . '<br />';
                                    $watermark_checked = true;
                                }
                            }
                        } else if ($watermark_type == 2) {
                            if (!function_exists('imagecreatefromjpeg')) {
                                $this->error = _("No create from JPG support, can't read watermark");
                            } else {
                                $filter = @imagecreatefromjpeg($this->image_watermark);
                                if (!$filter) {
                                    $this->error = _("No JPG read support, can't create watermark");
                                } else {
                                    $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("watermark source image is JPG") . '<br />';
                                    $watermark_checked = true;
                                }
                            }
                        } else if ($watermark_type == 3) {
                            if (!function_exists('imagecreatefrompng')) {
                                $this->error = _("No create from PNG support, can't read watermark");
                            } else {
                                $filter = @imagecreatefrompng($this->image_watermark);
                                if (!$filter) {
                                    $this->error = _("No PNG read support, can't create watermark");
                                } else {
                                    $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("watermark source image is PNG") . '<br />';
                                    $watermark_checked = true;
                                }
                            }
                        }
                        if ($watermark_checked) {
                            $watermark_width = imagesx($filter);
                            $watermark_height = imagesy($filter);
                            $watermark_x = 0;
                            $watermark_y = 0;
                            if (is_numeric($this->image_watermark_x)) {
                                if ($this->image_watermark_x < 0) {
                                    $watermark_x = $this->image_dst_x - $watermark_width + $this->image_watermark_x;
                                } else {
                                    $watermark_x = $this->image_watermark_x;
                                }
                            } else {
                                if (strpos($this->image_watermark_position, 'r') !== false) {
                                    $watermark_x = $this->image_dst_x - $watermark_width;
                                } else if (strpos($this->image_watermark_position, 'l') !== false) {
                                    $watermark_x = 0;
                                } else {
                                    $watermark_x = ($this->image_dst_x - $watermark_width) / 2;
                                }
                            }
         
                            if (is_numeric($this->image_watermark_y)) {
                                if ($this->image_watermark_y < 0) {
                                    $watermark_y = $this->image_dst_y - $watermark_height + $this->image_watermark_y;
                                } else {
                                    $watermark_y = $this->image_watermark_y;
                                }
                            } else {
                                if (strpos($this->image_watermark_position, 'b') !== false) {
                                    $watermark_y = $this->image_dst_y - $watermark_height;
                                } else if (strpos($this->image_watermark_position, 't') !== false) {
                                    $watermark_y = 0;
                                } else {
                                    $watermark_y = ($this->image_dst_y - $watermark_height) / 2;
                                }
                            }
                            imagecopyresampled ($image_dst, $filter, $watermark_x, $watermark_y, 0, 0, $watermark_width, $watermark_height, $watermark_width, $watermark_height);
                        
                        } else {
                            $this->error = _("Watermark image is of unknown type");
                        }                        
                    }

                    // add text
                    if (!empty($this->image_text)) {
                        $this->log .= '- ' . _("add text") . '<br />';
          
                        if (!is_numeric($this->image_text_padding)) $this->image_text_padding = 0;
                        if (!is_numeric($this->image_text_line_spacing)) $this->image_text_line_spacing = 0;
                        if (!is_numeric($this->image_text_padding_x)) $this->image_text_padding_x = $this->image_text_padding;
                        if (!is_numeric($this->image_text_padding_y)) $this->image_text_padding_y = $this->image_text_padding;
                        $this->image_text_position = strtolower($this->image_text_position);
                        $this->image_text_direction = strtolower($this->image_text_direction);
                        $this->image_text_alignment = strtolower($this->image_text_alignment);
                        
                        // if the font is a string, we assume that we might want to load a font
                        if (!is_numeric($this->image_text_font) && strlen($this->image_text_font) > 4 && substr(strtolower($this->image_text_font), -4) == '.gdf') {
                            $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("try to load font") . ' ' . $this->image_text_font . '... ';
                            if ($this->image_text_font = @imageloadfont($this->image_text_font)) {
                                $this->log .=  _("success") . '<br />';
                            } else {
                                $this->log .=  _("error") . '<br />';
                                $this->image_text_font = 5;
                            }
                        }
                  
                        $text = explode("\n", $this->image_text);
                        $char_width = ImageFontWidth($this->image_text_font);
                        $char_height = ImageFontHeight($this->image_text_font);
                        $text_height = 0;
                        $text_width = 0;
                        $line_height = 0;
                        $line_width = 0;
                  
                        foreach ($text as $k => $v) {
                            if ($this->image_text_direction == 'v') {
                                $h = ($char_width * strlen($v));
                                if ($h > $text_height) $text_height = $h;
                                $line_width = $char_height;
                                $text_width += $line_width + ($k < (sizeof($text)-1) ? $this->image_text_line_spacing : 0);                    
                            } else {
                                $w = ($char_width * strlen($v));
                                if ($w > $text_width) $text_width = $w;
                                $line_height = $char_height;                 
                                $text_height += $line_height + ($k < (sizeof($text)-1) ? $this->image_text_line_spacing : 0);                    
                            }
                        }
                        $text_width  += (2 * $this->image_text_padding_x);  
                        $text_height += (2 * $this->image_text_padding_y); 
                        $text_x = 0;
                        $text_y = 0;
                        if (is_numeric($this->image_text_x)) {
                            if ($this->image_text_x < 0) {
                                $text_x = $this->image_dst_x - $text_width + $this->image_text_x;
                            } else {
                                $text_x = $this->image_text_x;
                            }
                        } else {
                            if (strpos($this->image_text_position, 'r') !== false) {
                                $text_x = $this->image_dst_x - $text_width;
                            } else if (strpos($this->image_text_position, 'l') !== false) {
                                $text_x = 0;
                            } else {
                                $text_x = ($this->image_dst_x - $text_width) / 2;
                            }
                        }
     
                        if (is_numeric($this->image_text_y)) {
                            if ($this->image_text_y < 0) {
                                $text_y = $this->image_dst_y - $text_height + $this->image_text_y;
                            } else {
                                $text_y = $this->image_text_y;
                            }
                        } else {
                            if (strpos($this->image_text_position, 'b') !== false) {
                                $text_y = $this->image_dst_y - $text_height;
                            } else if (strpos($this->image_text_position, 't') !== false) {
                                $text_y = 0;
                            } else {
                                $text_y = ($this->image_dst_y - $text_height) / 2;
                            }
                        }
        
                        // add a background, maybe transparent
                        if (!empty($this->image_text_background)) {
                            sscanf($this->image_text_background, "#%2x%2x%2x", $red, $green, $blue);
                            if ($gd_version >= 2 && (is_numeric($this->image_text_background_percent)) && $this->image_text_background_percent >= 0 && $this->image_text_background_percent <= 100) {
                                $filter=imagecreatetruecolor($text_width, $text_height);
                                $background_color=imagecolorallocate($filter, $red, $green, $blue);
                                imagefilledrectangle($filter, 0, 0, $text_width, $text_height, $background_color);
                                imagecopymerge($image_dst, $filter, $text_x, $text_y, 0, 0, $text_width, $text_height, $this->image_text_background_percent);
                                imagedestroy($filter);
                            } else {
                                $background_color = imageColorAllocate($image_dst ,$red, $green, $blue);
                                imagefilledrectangle($image_dst, $text_x, $text_y, $text_x + $text_width, $text_y + $text_height, $background_color);
                            }
                        }

                        $text_x += $this->image_text_padding_x;
                        $text_y += $this->image_text_padding_y;
                        $t_width = $text_width - (2 * $this->image_text_padding_x);
                        $t_height = $text_height - (2 * $this->image_text_padding_y);                            
                        
                        sscanf($this->image_text_color, "#%2x%2x%2x", $red, $green, $blue);

                        // add the text, maybe transparent
                        if ($gd_version >= 2 && (is_numeric($this->image_text_percent)) && $this->image_text_percent >= 0 && $this->image_text_percent <= 100) {
                            if ($t_width < 0) $t_width = 0;
                            if ($t_height < 0) $t_height = 0;
                            $filter=imagecreatetruecolor($t_width, $t_height);
                            $color = imagecolorallocate($filter, 0, 0, ($blue == 0 ? 255 : 0));
                            imagefill($filter, 0, 0, $color);
                            $text_color = imagecolorallocate($filter ,$red, $green, $blue);                            
                            imagecolortransparent($filter, $color);
                            foreach ($text as $k => $v) {
                                if ($this->image_text_direction == 'v') {
                                    imagestringup($filter, 
                                                  $this->image_text_font, 
                                                  $k * ($line_width  + ($k > 0 && $k < (sizeof($text)) ? $this->image_text_line_spacing : 0)), 
                                                  $text_height - (2 * $this->image_text_padding_y) - ($this->image_text_alignment == 'l' ? 0 : (($t_height - strlen($v) * $char_width) / ($this->image_text_alignment == 'r' ? 1 : 2))) , 
                                                  $v, 
                                                  $text_color);
                                } else {
                                    imagestring($filter, 
                                                $this->image_text_font, 
                                                ($this->image_text_alignment == 'l' ? 0 : (($t_width - strlen($v) * $char_width) / ($this->image_text_alignment == 'r' ? 1 : 2))), 
                                                $k * ($line_height  + ($k > 0 && $k < (sizeof($text)) ? $this->image_text_line_spacing : 0)), 
                                                $v, 
                                                $text_color);
                                }
                            }
                            imagecopymerge($image_dst, $filter, $text_x, $text_y, 0, 0, $t_width, $t_height, $this->image_text_percent);
                            imagedestroy($filter);
                        } else {
                            $text_color = imageColorAllocate($image_dst ,$red, $green, $blue);
                            foreach ($text as $k => $v) {
                                if ($this->image_text_direction == 'v') {
                                    imagestringup($image_dst, 
                                                  $this->image_text_font, 
                                                  $text_x + $k * ($line_width  + ($k > 0 && $k < (sizeof($text)) ? $this->image_text_line_spacing : 0)), 
                                                  $text_y + $text_height - (2 * $this->image_text_padding_y) - ($this->image_text_alignment == 'l' ? 0 : (($t_height - strlen($v) * $char_width) / ($this->image_text_alignment == 'r' ? 1 : 2))), 
                                                  $v, 
                                                  $text_color);
                                } else {
                                    imagestring($image_dst, 
                                                $this->image_text_font, 
                                                $text_x + ($this->image_text_alignment == 'l' ? 0 : (($t_width - strlen($v) * $char_width) / ($this->image_text_alignment == 'r' ? 1 : 2))), 
                                                $text_y + $k * ($line_height  + ($k > 0 && $k < (sizeof($text)) ? $this->image_text_line_spacing : 0)), 
                                                $v, 
                                                $text_color);
                                }
                            }
                        }

                    }

                    // add a reflection
                    if ($this->image_reflection_height) {
                        $this->log .= '- ' . _("add reflection") . ' : ' . $this->image_reflection_height . '<br />';
                        // we decode image_reflection_height, which can be a integer, a string in pixels or percentage
                        $image_reflection_height = $this->image_reflection_height;
                        if (strpos($image_reflection_height, '%')>0) $image_reflection_height = $this->image_dst_y * (str_replace('%','',$image_reflection_height / 100));
                        if (strpos($image_reflection_height, 'px')>0) $image_reflection_height = str_replace('px','',$image_reflection_height);
                        $image_reflection_height = (int) $image_reflection_height;
                        if ($image_reflection_height > $this->image_dst_y) $image_reflection_height = $this->image_dst_y;
                        if (empty($this->image_reflection_color)) $this->image_reflection_color = '#FFFFFF'; 
                        if (empty($this->image_reflection_opacity)) $this->image_reflection_opacity = 60; 
                        // create the new destination image
                        $tmp = imagecreatetruecolor($this->image_dst_x, $this->image_dst_y + $image_reflection_height + $this->image_reflection_space);
                        sscanf($this->image_reflection_color, "#%2x%2x%2x", $red, $green, $blue);
                        $color = imagecolorallocate($tmp, $red, $green, $blue);
                        imagefilledrectangle($tmp, 0, 0, $this->image_dst_x, $this->image_dst_y + $image_reflection_height + $this->image_reflection_space, $color);
                        $transparency = $this->image_reflection_opacity;
                        // copy the original image
                        imagecopy($tmp, $image_dst, 0, 0, 0, 0, $this->image_dst_x, $this->image_dst_y + ($this->image_reflection_space < 0 ? $this->image_reflection_space : 0));
                        // copy the reflection
                        for ($y = 0; $y < $image_reflection_height; $y++) {
                            imagecopymerge($tmp, $image_dst, 0, $y + $this->image_dst_y + $this->image_reflection_space, 0, $this->image_dst_y - $y - 1 + ($this->image_reflection_space < 0 ? $this->image_reflection_space : 0), $this->image_dst_x, 1, (int) $transparency);
                            if ($transparency > 0) $transparency = $transparency - ($this->image_reflection_opacity / $image_reflection_height);
                        }
                        // copy the resulting image into the destination image
                        $this->image_dst_y = $this->image_dst_y + $image_reflection_height + $this->image_reflection_space;
                        imagedestroy($image_dst);     
                        $image_dst=imagecreatetruecolor($this->image_dst_x, $this->image_dst_y);
                        imagecopy($image_dst,$tmp,0,0,0,0,$this->image_dst_x,$this->image_dst_y);
                        imagedestroy($tmp);
                    }

                    if (is_numeric($this->jpeg_size) && $this->jpeg_size > 0 && ($this->image_convert == 'jpeg' || $this->image_convert == 'jpg')) {
                        // inspired by: JPEGReducer class version 1, 25 November 2004, Author: Huda M ElMatsani, justhuda at netscape dot net
                        $this->log .= '- ' . _("JPEG desired file size") . ' : ' . $this->jpeg_size . '<br />';
                        //calculate size of each image. 75%, 50%, and 25% quality
                        ob_start(); imagejpeg($image_dst,'',75);  $buffer = ob_get_contents(); ob_end_clean();
                        $size75 = strlen($buffer);
                        ob_start(); imagejpeg($image_dst,'',50);  $buffer = ob_get_contents(); ob_end_clean();
                        $size50 = strlen($buffer);
                        ob_start(); imagejpeg($image_dst,'',25);  $buffer = ob_get_contents(); ob_end_clean();
                        $size25 = strlen($buffer);
                
                        //calculate gradient of size reduction by quality
                        $mgrad1 = 25/($size50-$size25);
                        $mgrad2 = 25/($size75-$size50);
                        $mgrad3 = 50/($size75-$size25);
                        $mgrad  = ($mgrad1+$mgrad2+$mgrad3)/3;
                        //result of approx. quality factor for expected size
                        $q_factor=round($mgrad*($this->jpeg_size-$size50)+50);
                
                        if ($q_factor<1) {
                            $this->jpeg_quality=1;
                        } elseif ($q_factor>100) {
                            $this->jpeg_quality=100;
                        } else {
                            $this->jpeg_quality=$q_factor;
                        }
                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("JPEG quality factor set to") . ' ' . $this->jpeg_quality . '<br />';
                    }



                    // outputs image
                    $this->log .= '- ' . _("converting..") . '<br />';
                    switch($this->image_convert) {
                        case 'jpeg':
                        case 'jpg':
                            $result = @imagejpeg ($image_dst, $this->file_dst_pathname, $this->jpeg_quality);
                            if (!$result) {
                                $this->processed = false;
                                $this->error = _("No JPEG create support");
                            } else {
                                $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("JPEG image created") . '<br />';
                            }
                            break;
                        case 'png':
                            $result = @imagepng ($image_dst, $this->file_dst_pathname);
                            if (!$result) {
                                $this->processed = false;
                                $this->error = _("No PNG create support");
                            } else {
                                $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("PNG image created") . '<br />';
                            }
                            break;
                        case 'gif':
                            $result = @imagegif ($image_dst, $this->file_dst_pathname);
                            if (!$result) {
                                $this->processed = false;
                                $this->error = _("No GIF create support");
                            } else {
                                $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("GIF image created") . '<br />';
                            }
                            break;
                        default:
                            $this->processed = false;
                            $this->error = _("No convertion type defined");
                    }
                    if ($this->processed) {
                        if (is_resource($image_src)) imagedestroy($image_src);
                        if (is_resource($image_dst)) imagedestroy($image_dst);
                        $this->log .= '&nbsp;&nbsp;&nbsp;&nbsp;' . _("image objects destroyed") . '<br />';
                    }
                }              

            } else {
                $this->log .= '- ' . _("no image processing wanted") . '<br />';

                // copy the file to its final destination. we don;t use move_uploaded_file here
                // if we happen to have open_basedir restrictions, it is a temp file that we copy, not the original uploaded file
                if (!copy($this->file_src_pathname, $this->file_dst_pathname)) {
                    $this->processed = false;
                    $this->error = _("Error copying file on the server. copy() failed");
                }
            }
        }

        if ($this->processed) {
            $this->log .= '- <b>' . _("process OK") . '</b><br />';

        }
        // we reinit all the var
        $this->init();

    }

    /**
     * Deletes the uploaded file from its temporary location
     *
     * When PHP uploads a file, it stores it in a temporary location.
     * When you {@link process} the file, you actually copy the resulting file to the given location, it doesn't alter the original file.
     * Once you have processed the file as many times as you wanted, you can delete the uploaded file.
     * If there is open_basedir restrictions, the uploaded file is in fact a temporary file
     *
     * You might want not to use this function if you work on local files, as it will delete the source file
     *
     * @access public
     */
    function clean() {
        $this->log .= '<b>' . _("cleanup") . '</b><br />';
        $this->log .= '- ' . _("delete temp file") . ' '  . $this->file_src_pathname . '<br />';
        @unlink($this->file_src_pathname);
    }

}

// Handle Process
function WaterMarkProcess(&$handle, $gd_code, $position="") {

	$handle->image_watermark = './shopImgs/watermark.gif';

	/**
	 * Text Position (Null is Center)
	 *
     * TL  T  TR
     * L       R
     * BL  B  BR
	 */
	$handle->image_watermark_position = $position;

	$handle->Process('./dataroom/product_img/'.$gd_code.'/watermark');
	
	if ($handle->processed) {
		return true;
	} else {
		return false;
	}
}

// Handle Process
function WaterMarkProcess2(&$handle, $watermark_source, $directory, $position="") {

	$handle->image_watermark = $watermark_source.'/watermark/watermark.png';
//echo $watermark_source.'watermark/watermark.png';
//exit;	
	/**
	 * Text Position (Null is Center)
	 *
     * TL  T  TR
     * L       R
     * BL  B  BR
	 */
	$handle->image_watermark_position = $position;

	$handle->Process($directory.'/watermark');
	
	if ($handle->processed) {
		return true;
	} else {
		return false;
	}
}


// Handle Process
function WaterMarkProcess3(&$handle, $directory, $position="") {

	$handle->image_watermark = './shopImgs/watermark.gif';

	/**
	 * Text Position (Null is Center)
	 *
     * TL  T  TR
     * L       R
     * BL  B  BR
	 */
	$handle->image_watermark_position = $position;

	$handle->Process($directory.'/watermark');
	
	if ($handle->processed) {
		return true;
	} else {
		return false;
	}
}

// makeWaterMark
function makeWaterMark($img_file, $type=1, $position="") {
	global $watermarkConf;

	//워터마크 적용
	if($watermarkConf == 1) {
		$img_dir = dirname($img_file);
		$img_filename = basename($img_file);

		@mkdir($img_dir.'/watermark');
		@chmod($img_dir.'/watermark', 0777);

		$handle = new Upload($img_file);
		switch($type) {
			case '1' :	$result = WaterMarkProcess1($handle, $img_file, $position=""); // $img_file == $gd_code
				break;
			case '2' :	$result = WaterMarkProcess2($handle, $img_dir, $position="");
				break;
			case '3' :	$result = WaterMarkProcess3($handle, $img_dir, $position="");
				break;
		}

		@copy($img_file,$img_dir.'/original_'.$img_filename);
		@chmod($img_dir.'/original_'.$img_filename, 0777);

		@copy($img_dir.'/watermark/'.$img_filename,$img_file);
		@chmod($img_file, 0777);

		@unlink($img_dir.'/watermark/'.$img_filename);
		@rmdir($img_dir.'/watermark');

		return true;
	}else {
		return false;
	}
}

// i18n gettext compatibility
if (!function_exists("_")) {
  function _($str) {
    return $str;
  }
} 

?>