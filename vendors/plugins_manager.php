<?php

    class PluginsManagerPM
    {
        var $mainShell;

        function __construct( $_params )
        {
            if( empty($_params['mainShell']) )
            {
                $this->mainShell->formattedOut( __d('plugin', "[bg=red][fg=black]PluginsManager sem acesso ao Shell[/fg][/bg]\n", true) );
                exit;
            }
            $this->mainShell = $_params['mainShell'];
        }

        function _getInstalledPlugins( )
        {
            if( !App::import('Core', 'Folder') )
            {
                $this->mainShell->formattedOut( __d('plugin', "Impossivel carregar [fg=red][u]Core.Folder[/u][/fg]\n", true) );
                exit;
            }

            $pluginsFolder = new Folder( APP.'plugins' );

            $listPluginsFolder = $pluginsFolder->ls( );

            return $listPluginsFolder[0];
        }

        function _checkUrl( $_plugin )
        {
            if( !App::import('Core', 'Folder') )
            {
                $this->mainShell->formattedOut( __d('plugin', "Impossivel carregar [fg=red][u]Core.Folder[/u][/fg]\n", true) );
                exit;
            }

            $pluginPath = APP.'plugins/'.$_plugin;
            $pluginFolder = new Folder( $pluginPath );

            $listPluginFolder = $pluginFolder->ls( );

            if( in_array('.url', $listPluginFolder[1]) )
            {
                return $this->_getPluginUrl( $pluginPath );
            }

            return false;
        }

        function _getPluginUrl( $_pluginPath )
        {
            $url = file_get_contents( $_pluginPath.'/.url' );

            return $url;
        }

        function listInstalledPlugins( )
        {
            $this->mainShell->formattedOut( String::insert(__d('plugin', "Listando plugins instalados em [u]:app[/u]:\n", true), array('app'=> APP_DIR)) );

            $installedPlugins = $this->_getInstalledPlugins( );

            foreach( $installedPlugins as $plugin )
            {
                $out = String::insert( __d('plugin', '  [fg=green]:plugin', true), array('plugin'=> $plugin) );

                if( $this->_checkUrl($plugin) )
                {
                    $out .= __d( 'plugin', " *[/fg]", true );
                }
                else
                {
                    $out .= __d( 'plugin', "[/fg]\n", true );
                }

                $this->mainShell->formattedOut( $out );
            }

            $this->mainShell->formattedOut( __d('plugin', '* Plugins que podem ser atualizados utilizando o [u]Plugin Manager[/u]', true) );
        }
    }

?>
