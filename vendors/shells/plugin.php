<?php

    if( !App::import( 'Plugins', 'ImprovedCakeShell.ImprovedCakeShell' ) )
    {
        App::import( 'Vendors', 'InstallImprovedCakeShell.InstallImprovedCakeShell' );

        $installICS =& ClassRegistry::init( 'InstallImprovedCakeShell' );

        $installICS->install( );
    }

    class PluginShell extends ImprovedCakeShell 
    {
        var $proxy = false;

        function _initialize( )
        {
            if( !empty($this->params['proxy']) )
            {
                $this->proxy = $this->params['proxy'];
            }
        }

        function main()
        {
            $this->_initialize( );

            if( empty($this->args) )
            {
                $this->formattedOut( __d('plugin', 'Voce precisa especifiar o que deseja fazer...', true) );

                $this->_listaOpcoesDisponiveis( );

                $this->out( '' );
                $this->hr( );
                exit;
            }

            switch( $this->args[0] )
            {
                case 'add-rep':
                    $this->_addRep( $this->args[1] );
                    break;
                case 'rem-rep':
                    $this->_remRep( $this->args[1] );
                    break;
                case 'list-rep':
                    ( isset($this->args[1]) ) ? $this->_listRep( $this->args[1] ) : $this->_listRep( );
                    break;
                case 'find':
                    $this->_find( $this->args[1] );
                    break;
                case 'list':
                    $this->_list( );
                    break;
                case 'install':
                    $this->_install( $this->args[1] );
                    break;
                case 'uninstall':
                    $this->_uninstall( $this->args[1] );
                    break;
                case 'update':
                    $this->_update( $this->args[1] );
                    break;
                default:
                    $this->formattedOut( __d('plugin', '[bg=red][fg=white] OPCAO INVALIDA [/fg][/bg]', true) );
                    $this->out( '' );
                    $this->_listaOpcoesDisponiveis( );
                    break;
            }
            $this->hr( );
        }

        function _listaOpcoesDisponiveis( )
        {
            $this->formattedOut( __d('plugin', "
Opcoes disponiveis:

  [fg=yellow]add-rep[/fg] [fg=green]url_repositorio[/fg]
    Adiciona um repositorio de busca

  [fg=yellow]rem-rep[/fg] [fg=green]url_repositorio[/fg]
    Remove um repositorio de busca

  [fg=yellow]list-rep[/fg]
    Lista os repositorios disponiveis

  [fg=yellow]list-rep[/fg] [fg=green]url_repositorio[/fg]
    Lista os plugins disponiveis no repositorio especificado

  [fg=yellow]find[/fg] [fg=green]nome_do_plugin[/fg]
    Busca um plugin na lista de repositorios disponiveis

  [fg=yellow]list[/fg]
    Lista os plugins instalados atualmente

  [fg=yellow]install[/fg] [fg=green]url_plugin[/fg]
    Instala o plugin especificado na url, executando o script
    de instalacao, se existir

  [fg=yellow]uninstall[/fg] [fg=green]nome_plugin[/fg] [fg=red](Indisponivel)[/fg]
    Remove o plugin especificado, executando o script de
    desinstalacao se existir

  [fg=yellow]update[/fg] [fg=green]nome_plugin[/fg] [fg=red](Indisponivel)[/fg]
    Verifica se existem atualizacoes disponiveis para o plugin
    especificado e as instala

  [fg=yellow]-proxy[/fg] [fg=green]username:password@endereco.do.proxy:porta[/fg]
    Utiliza as configuracoes do proxy para realizar as operacoes
    desejadas", true) );
        }

        function _importResource( $_resource, $_constructorParams )
        {
            if( !App::import( 'Vendors', 'PluginManager.'.$_resource ) )
            {
                $this->formattedOut( String::insert(__d('plugin', "Impossivel carregar [fg=red][u]:resource[/u][/fg]\n", true), array('resource'=>$_resource)) );

                $this->hr( );
                exit;
            }

            $className = $_resource.'PM';
            return new $className( $_constructorParams );
        } 

        function _addRep( $url )
        {
            $repositoriesManager = $this->_importResource( 'RepositoriesManager', array( 'mainShell' => $this ) );
            $repositoriesManager->add( $url );
        }

        function _remRep( $url )
        {
            $repositoriesManager = $this->_importResource( 'RepositoriesManager', array( 'mainShell' => $this ) );
            $repositoriesManager->remove( $url );
        }

        function _selectRepositorie( $_repositories )
        {
            $this->formattedOut( __d('plugin', 'Selecione um Repositorio para listar', true) );
            $this->out( '' );

            $this->_listFoundRepositories( $_repositories );
            $options = range(1, count($_repositories));
            $rep = $this->in( '' );

            $this->out( '' );
            if( in_array($rep, $options) )
            {
                $flipped = array_flip($options);
                $url = $_repositories[$flipped[$rep]];
            }
            else
            {
                $this->formattedOut( __d('plugin', '[bg=red][fg=black] ERRO [/fg][/bg] Opcao Invalida', true) );

                $this->out( '' );
                $this->hr( );
                exit;
            }

            return $url;
        }

        function _listFoundRepositories( $_repositories )
        {
            $counter = 1;

            foreach( $_repositories as $repositorie )
            {
                $this->formattedOut( String::insert(__d('plugin', '[fg=green](:counter)[/fg]  [u]:rep_url[/u]', true), array('counter' => $counter++, 'rep_url' => $repositorie)) );
            }
        }

        function _listRep( $url = null )
        {
            $repositoriesManager = $this->_importResource( 'RepositoriesManager', array( 'mainShell' => $this ) );

            if( empty($url) )
            {
                $repositories = $repositoriesManager->get( );
                if( !count($repositories) )
                {
                    $this->formattedOut( __d('plugin', "Nao existem repositorios para serem listados\n", true) );
                    exit;
                }

                $url = $this->_selectRepositorie( $repositories );
            }

            $repositoriesManager->showRepositorieContent( $url, $this->proxy );
        }

        function _find( $pluginName )
        {
            $pluginsManager = $this->_importResource( 'PluginsManager', array( 'mainShell' => $this ) );

            $pluginsManager->find( $pluginName );
        }

        function _list( )
        {
            $pluginsManager = $this->_importResource( 'PluginsManager', array( 'mainShell' => $this ) );

            $pluginsManager->listInstalledPlugins( );
        }

        function _install( $nameOrUrl )
        {
            $pluginsManager = $this->_importResource( 'PluginsManager', array( 'mainShell' => $this ) );

            $pluginsManager->installPlugin( $nameOrUrl );
        }

        function _uninstall( $pluginName )
        {
        }

        function _update( $pluginName )
        {
        }

    }

?>
