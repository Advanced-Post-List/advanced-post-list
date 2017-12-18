(function() {
    tinymce.create('tinymce.plugins.advanced_post_list', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function( editor, url ) {
            //var apl_tinyMCE = apl_tinyMCE;
            var trans = apl_tinyMCE.trans;
            editor.addButton( 'apl_post_list', {
                title : trans.button_title,
                //cmd : 'apl_shortcode',
                icon: 'apl_shortcode',
                tooltip: trans.button_tooltip,
                onclick: function() {
                    editor.execCommand('apl_shortcode','',{
                        variable : '',
                    });
                }
            });

            editor.addCommand( 'apl_shortcode', function( ui, v ) {
                // Set Variables.
                var variable = '';
                if ( v.variable ) {
                    variable = v.variable;
                }

                var i        = 0;
                var i_slug   = '';
                var local_pl = apl_tinyMCE.post_lists;
                var pl_vals  = [];
                for ( i_slug in local_pl ) {
                    pl_vals[ i ] = {text: local_pl[ i_slug ], value: i_slug};
                    i++;
                }
                editor.windowManager.open( {
                    title: trans.window_title,
                    body: [
                        {
                            type: 'listbox',
                            name: 'apl_post_list',
                            label: trans.window_body_1_label,
                            'values': pl_vals,
                            tooltip: trans.window_body_1_tooltip
                        },
                    ],
                    onsubmit: function( e ) {
                        var pl_shortcode = '[post_list name="' + e.data.apl_post_list + '"]';

                        editor.insertContent( pl_shortcode );
                    }
                });

            });
        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        // createControl : function(n, cm) {
        //     return null;
        // },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'APL Buttons',
                author : 'EkoJR',
                authorurl : 'http://ekojr.com',
                infourl : 'http://advancedpostlist.com',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add( 'advanced_post_list', tinymce.plugins.advanced_post_list );
})();
