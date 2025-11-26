
/*
 * @version $Id: init_minimal.js 248 2017-04-28 14:29:03Z hi $
 *
 */

{
    baseHref : '%BASE_HREF%',
    //%fontawesomeCss_url%,
    contentsCss : [%STYLESHEET%],
    //remove default styles
    stylesSet : [],
    //%height%
    defaultLanguage : 'en',
    language : '%LANGUAGE%',
    skin: '%SKIN%',
    //%autogrow_on_startup%
    toolbarCanCollapse : true,
	
    fillEmptyBlocks: false,
    forcePasteAsPlainText: true,
	
    entities : false,
    entities_latin : false,
    entities_greek : false,
    entities_additional : '', // '#39' (The single quote (') character.) 
    
    //%tbgroups%
    
    //%rmbuttons%
    	
    //Filebrowser - settings
    //%FbWinW%
    //%FbWinH%
    //%FILEBROWSER%
    //%removePlugins%
    //%extraPlugins%
    extraPlugins: 'autogrow',
    autoGrow_minHeight: 200,
    autoGrow_maxHeight: 600,
    autoGrow_bottomSpace: 50,		    
    //%additionaConfigs%
    format_tags : '%FORMAT_TAGS%',
    font_names : %FORMAT_FONTS%,
    fontSize_sizes : '%FORMAT_FONTSIZES%'
}
