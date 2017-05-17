tinyMceWysiwygSetup.prototype.originalGetSettings = tinyMceWysiwygSetup.prototype.getSettings;

tinyMceWysiwygSetup.prototype.getSettings = function (mode) {
    var settings = this.originalGetSettings(mode);
    settings.theme_advanced_fonts = "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Hollie Script Pro=hollie script pro,brush script mt;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;TTMoons=ttmoons,georgia;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats";
    return settings;
};
